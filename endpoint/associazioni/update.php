<?php
// Definisci gli origini consentiti
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/associazioni.php';
use component\database;

try {
    $database = new database();
    $db = $database->getConnection();
    $associazioni = new associazioni($db);

    // Ottieni l'ID dalla URL
    $id = $_GET['id'] ?? die(json_encode([
        "message" => "ID associazione mancante",
        "status" => "error"
    ]));

    // Leggi i dati JSON dal body della richiesta
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'] ?? die(json_encode([
        "message" => "ID associazione mancante",
        "status" => "error"
    ]));

    if (empty($data)) {
        http_response_code(400);
        die(json_encode([
            "message" => "Nessun dato fornito per l'aggiornamento",
            "status" => "error"
        ]));
    }

    // Esegui l'update
    $result = $associazioni->update($id, $data);

    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Associazione aggiornata con successo.",
            "status" => "success"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Nessuna associazione trovata con questo ID o nessun cambiamento applicato.",
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