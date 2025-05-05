<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/news.php';
use component\database;

try {
    $database = new database();
    $db = $database->getConnection();
    $news = new news($db);

    $id = isset($_GET['id']) ? $_GET['id'] : die(json_encode([
        "message" => "ID news mancante",
        "status" => "error"
    ]));

    $result = $news->delete($id);

    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "News eliminata con successo.",
            "status" => "success"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Nessuna news trovata con questo ID.",
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