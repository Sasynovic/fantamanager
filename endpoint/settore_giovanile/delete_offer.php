<?php
// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/offerte_sgs.php';
use component\database;

try {
    $database = new database();
    $db = $database->getConnection();
    $offerte_sgs = new offerte_sgs($db);

    $id = isset($_GET['id']) ? $_GET['id'] : die(json_encode([
        "message" => "ID offerte_sgs mancante",
        "status" => "error"
    ]));

    $result = $offerte_sgs->delete($id);

    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Squadra eliminata con successo.",
            "status" => "success"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Nessuna offerte_sgs trovata con questo ID.",
            "status" => "error"
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Errore del server: " . $e->getMessage(),
        "status" => "error"
    ]);
}