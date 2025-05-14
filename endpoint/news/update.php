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

    // Ottieni l'ID dalla URL
    $id = $_GET['id'] ?? die(json_encode([
        "message" => "ID trattativa mancante",
        "status" => "error"
    ]));

    // Leggi i dati JSON dal body della richiesta
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data)) {
        http_response_code(400);
        die(json_encode([
            "message" => "Nessun dato fornito per l'aggiornamento",
            "status" => "error"
        ]));
    }

    // Esegui l'update
    $result = $news->update($id, $data);

    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "News aggiornata con successo.",
            "status" => "success"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Nessuna news trovata con questo ID o nessun cambiamento applicato.",
            "status" => "error"
        ]);
    }
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "status" => "error"
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Errore del server: " . $e->getMessage(),
        "status" => "error"
    ]);
}