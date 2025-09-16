<?php
use component\database;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/database.php';
require_once '../../models/offerte_sgs.php';

$database = new database();
$db = $database->getConnection();
$offerte_sgs = new offerte_sgs($db);

$assegnato = isset($_GET['assegnato']) ? $_GET['assegnato'] : null;
$id_divisione = isset($_GET['id_divisione']) ? intval($_GET['id_divisione']) : null;
$id_squadra = isset($_GET['id_squadra']) ? intval($_GET['id_squadra']) : null;
$ruolo = isset($_GET['ruolo']) ? $_GET['ruolo'] : null;
$squadra_reale = isset($_GET['squadra_reale']) ? $_GET['squadra_reale'] : null;

$stmt = $offerte_sgs->read($assegnato, $id_divisione, $id_squadra, $ruolo, $squadra_reale);
$num = $stmt->rowCount();

if ($num > 0) {
    $response = array();
    $response["gestione_settore_giovanile"] = array();

    // array temporaneo per raggruppare le associazioni
    $associazioni_map = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $key = $id; // invece di $id_associazione_g

        if (!isset($associazioni_map[$key])) {
            $associazioni_map[$key] = array(
                "associazione" => array(
                    "id" => $id,
                    "id_divisione" => $id_divisione,
                    "nome_divisione" => $nome_divisione,
                    "id_calciatore" => $id_calciatore_g,
                    "calciatore" => array(
                        "cognome" => $cognome,
                        "nome" => $nome,
                        'squadra' => $squadra,
                        'ruolo' => $ruolo,
                        "offerte" => array()
                    )
                )
            );
        }


        // aggiungo l'offerta solo se presente
        if (!empty($id_offerta)) {
            $associazioni_map[$id_associazione_g]["associazione"]["calciatore"]["offerte"][] = array(
                "id_offerta" => $id_offerta,
                "id_squadra" => $id_squadra,
                "valore_offerta" => $valore_offerta,
                "assegnato" => $assegnato
            );
        }
    }

    // trasformo la mappa in array
    foreach ($associazioni_map as $assoc) {
        $response["gestione_settore_giovanile"][] = $assoc;
    }

    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna offerta trovata."));
}
