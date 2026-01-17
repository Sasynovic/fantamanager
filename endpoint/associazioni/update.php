<?php
// Definisci gli origini consentiti
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/associazioni.php';
use component\database;

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
header("Content-Type: application/json; charset=UTF-8");

try {
    $database = new database();
    $db = $database->getConnection();
    $associazioni = new associazioni($db);

    // Determina il metodo della richiesta
    $method = $_SERVER['REQUEST_METHOD'];

    // Inizializza $data come array vuoto
    $data = [];

    // Leggi i dati in base al metodo della richiesta
    if ($method === 'PUT') {
        $input = file_get_contents("php://input");
        if (!empty($input)) {
            $data = json_decode($input, true);
            if ($data === null) {
                http_response_code(400);
                die(json_encode([
                    "message" => "JSON non valido",
                    "status" => "error"
                ]));
            }
        }
    } elseif ($method === 'GET') {
        // Per GET, usa i parametri della query string
        $data = $_GET;
    }

    $id = $data['id'] ?? $_GET['id'] ?? null;
    $reset = isset($data['reset']) ? filter_var($data['reset'], FILTER_VALIDATE_BOOLEAN) : false;

    // Se reset=true è passato come GET param (es: reset=1)
    if (!$reset && isset($_GET['reset'])) {
        $reset = filter_var($_GET['reset'], FILTER_VALIDATE_BOOLEAN);
    }

    if ($reset !== true && $id === null) {
        http_response_code(400);
        die(json_encode([
            "message" => "ID associazione mancante",
            "status" => "error"
        ]));
    }

    // Solo se $data non è vuoto e contiene 'timestamp'
    if (!empty($data) && array_key_exists('timestamp', $data)) {
        $data['timestamp'] = date('Y-m-d H:i:s'); // Formato MySQL compatibile
    }

    // Per richieste GET di reset, non verificare se $data è vuoto
    if ($reset !== true && empty($data)) {
        http_response_code(400);
        die(json_encode([
            "message" => "Nessun dato fornito per l'aggiornamento",
            "status" => "error"
        ]));
    }

    // Esegui l'update
    if ($reset === true) {
        $result = $associazioni->dealReset();
    } else {
        $result = $associazioni->update($id, $data);
    }

    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => $reset ? "Reset eseguito con successo." : "Associazione aggiornata con successo.",
            "status" => "success"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => $reset ? "Errore durante il reset." : "Nessuna associazione trovata con questo ID o nessun cambiamento applicato.",
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