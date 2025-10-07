<?php
header("Access-Control-Allow-Origin: *");

// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/partecipazione.php';
use component\database;

try {
    $database = new database();
    $db = $database->getConnection();
    $partecipazione = new partecipazione($db);

    // Ottieni l'ID dalla URL
    $id_squadra = $_GET['id_squadra'] ?? die(json_encode([
        "message" => "ID partecipazione mancante",
        "status" => "error"
    ]));

    $id_competizione = $_GET['id_competizione'] ?? die(json_encode([
        "message" => "ID competizione mancante",
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
    $result = $partecipazione->update($id_squadra, $id_competizione, $data);

    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Partecipazione aggiornata con successo.",
            "status" => "success"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Nessun cambiamento applicato.",
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