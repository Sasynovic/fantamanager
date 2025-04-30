<?php

use component\database;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/partecipazione.php';

$database = new database();
$db = $database->getConnection();
$partecipazione = new partecipazione($db);

$id_competizione = isset($_GET['id_competizione']) ? $_GET['id_competizione'] : null;
$stmt = $partecipazione->read($id_competizione);
$num = $stmt->rowCount();

if ($num > 0) {
    $partecipazioni_arr = array();
    // Recupero nome competizione solo dalla prima riga
    $first_row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Estrai i campi una volta
    extract($first_row);

    $partecipazioni_arr["nome_competizione"] = $nomeDivisione . " " . $nomeCompetizione;
    $partecipazioni_arr["squadre"] = array();



    // Inserisci la prima squadra
    $partecipazioni_arr["squadre"][] = array(
        "id" => $id,
        "nome_squadra" => $nome_squadra,
        "presidente" => $nome_pres . ' ' . $cognome_pres,
        "vicepresidente" => $nome_vice . ' ' . $cognome_vice
    );

    // Continua con il resto dei risultati
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $partecipazioni_arr["squadre"][] = array(
            "id" => $id,
            "nome_squadra" => $nome_squadra,
            "presidente" => $nome_pres . ' ' . $cognome_pres,
            "vicepresidente" => $nome_vice . ' ' . $cognome_vice
        );
    }

    http_response_code(200);
    echo json_encode($partecipazioni_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna partecipazione trovata."));
}
