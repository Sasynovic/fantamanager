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

// Leggi l'input JSON
    $data = json_decode(file_get_contents("php://input"));

// Validazione metodo HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405);
        echo json_encode(["message" => "Metodo non consentito", "success" => false]);
        exit;
    }

// Validazione input
    if (empty($data)) {
        http_response_code(400);
        echo json_encode(["message" => "Input non valido", "success" => false]);
        exit;
    }

    try {
        // Verifica campi obbligatori
        $requiredFields = ['id_squadra', 'id_calciatore'];
        foreach ($requiredFields as $field) {
            if (!isset($data->$field) || empty($data->$field)) {
                http_response_code(400);
                echo json_encode(["message" => "Il campo $field è obbligatorio", "success" => false]);
                exit;
            }
        }

        // Chiamata al metodo create
        $result = $associazioni->release(
            $data->id_squadra,
            $data->id_calciatore
        );
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
                "message" => "Nessuna associazione trovata relativa ai dati forniti o nessun cambiamento applicato.",
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
}catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Errore di connessione al database: " . $e->getMessage(),
        "status" => "error"
    ]);
}