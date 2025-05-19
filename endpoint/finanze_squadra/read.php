<?php

use component\database;

// Definisci gli origini consentiti
$allowed_origins = [
    'https://barrettasalvatore.it',
    'https://fantamanagerpro.eu'
];

// Verifica se l'origine della richiesta è nella lista degli origini consentiti
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // Se l'origine non è consentita, imposta un'origine predefinita o non impostare l'header
    header("Access-Control-Allow-Origin: https://tuodominio.com");
    // Alternativamente, puoi restituire un errore 403 Forbidden
    // http_response_code(403);
    // echo json_encode(["message" => "Origine non autorizzata", "success" => false]);
    // exit;
}

// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once '../../config/database.php';
require_once '../../models/finanze_squadra.php';

$database = new database();
$db = $database->getConnection();
$finanze_squadra = new finanze_squadra($db);

$id_squadra_filter = isset($_GET['id_squadra']) ? intval($_GET['id_squadra']) : null;

$stmt = $finanze_squadra->read($id_squadra_filter);


$num = $stmt->rowCount();

if ($num > 0) {
    $squadre_arr = array();
    $squadre_arr["finanze_squadra"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $squadra_item = array(
            "id" => $id,
            "guadagno_crediti_stadio_league" => $guadagno_crediti_stadio_league,
            "guadagno_crediti_stadio_cup" => $guadagno_crediti_stadio_cup,
            "premi_league" => $premi_league,
            "premi_cup" => $premi_cup,
            "prequalifiche_uefa_stadio" => $prequalifiche_uefa_stadio,
            "prequalifiche_uefa_premio" => $prequalifiche_uefa_premio,
            "competizioni_uefa_stadio" => $competizioni_uefa_stadio,
            "competizioni_uefa_premio" => $competizioni_uefa_premio,
            "crediti_residui_cassa" => $crediti_residui_cassa,
            "totale_crediti_bilancio" => $totale_crediti_bilancio,
            "punteggio_ranking" => $punteggio_ranking,
            "id_squadra" => $id_squadra
        );

        $squadre_arr["finanze_squadra"][] = $squadra_item;
    }

    http_response_code(200);
    echo json_encode($squadre_arr);

} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna squadra trovata."));
}
