<?php
$allowed_origins = [
    'https://barrettasalvatore.it',
    'https://fantamanagerpro.eu'
];

// Verifica se l'origine della richiesta è nella lista degli origini consentiti
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // Se l'origine non è consentita, restituire un errore 403 Forbidden
    http_response_code(403);
    echo json_encode(["message" => "Origine non autorizzata", "success" => false]);
    exit;
}

// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/calciatori.php';
use component\database;

try {
    $database = new database();
    $db = $database->getConnection();
    $calciatore = new calciatori($db);

    // Ottieni l'ID dalla URL
    $id = $_GET['id'] ?? die(json_encode([
        "message" => "ID calciatore mancante",
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
    $result = $calciatore->update($id, $data);

    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Calciatore aggiornata con successo.",
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
