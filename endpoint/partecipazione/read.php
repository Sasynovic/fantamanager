<?php
use component\database;
header("Access-Control-Allow-Origin: *");

// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once '../../config/database.php';
require_once '../../models/partecipazione.php';

$database = new database();
$db = $database->getConnection();
$partecipazione = new partecipazione($db);

$id_competizione = $_GET['id_competizione'] ?? null;
$id_squadra = $_GET['id_squadra'] ?? null;
$stmt = $partecipazione->read($id_squadra,$id_competizione);
$num = $stmt->rowCount();

if ($num > 0) {
    $partecipazioni_arr = array();
    // Recupero nome competizione solo dalla prima riga
    $first_row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Estrai i campi una volta
    extract($first_row);

    $partecipazioni_arr["nome_competizione"] = $nomeDivisione . " " . $nomeCompetizione;
    $partecipazioni_arr["id_competizione"] = $id_competizione;
    $partecipazioni_arr["squadre"] = array();

    // Inserisci la prima squadra
    $partecipazioni_arr["squadre"][] = array(
        "id" => $id,
        "nome_squadra" => $nome_squadra,
        "credito" => $credito,
        "presidente" => $nome_pres . ' ' . $cognome_pres,
        "vicepresidente" => $nome_vice . ' ' . $cognome_vice,
        "posizione" => $Pos ?? 0,
        "penalizzazione" => $Pen ?? 0,
        "giocate" => $G ?? 0,
        "vittorie" => $V ?? 0,
        "pareggi" => $N ?? 0,
        "sconfitte" => $P ?? 0,
        "gol_fatti" => $Gf ?? 0,
        "gol_subiti" => $Gs ?? 0,
        "differenza_reti" => $Dr ?? 0,
        "punti" => $Pt ?? 0,
        "punti_totali" => $PtTotali ?? 0,
        "girone" => $girone ?? null
    );

    // Continua con il resto dei risultati
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $partecipazioni_arr["squadre"][] = array(
            "id" => $id,
            "nome_squadra" => $nome_squadra,
            "presidente" => $nome_pres . ' ' . $cognome_pres,
            "vicepresidente" => $nome_vice . ' ' . $cognome_vice,
            "posizione" => $Pos ?? 0,
            "penalizzazione" => $Pen ?? 0,
            "giocate" => $G ?? 0,
            "vittorie" => $V ?? 0,
            "pareggi" => $N ?? 0,
            "sconfitte" => $P ?? 0,
            "gol_fatti" => $Gf ?? 0,
            "gol_subiti" => $Gs ?? 0,
            "differenza_reti" => $Dr ?? 0,
            "punti" => $Pt ?? 0,
            "punti_totali" => $PtTotali ?? 0,
            "girone" => $girone ?? null
        );
    }

    http_response_code(200);
    echo json_encode($partecipazioni_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna partecipazione trovata."));
}
