<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/competizione.php';
use component\database;

try {
    $database = new database();
    $db = $database->getConnection();
    $competizione = new competizione($db);

    $id = isset($_GET['id']) ? $_GET['id'] : die(json_encode([
        "message" => "ID competizione mancante",
        "status" => "error"
    ]));

    $result = $competizione->delete($id);

    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "competizione eliminata con successo.",
            "status" => "success"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Nessuna competizione trovata con questo ID.",
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