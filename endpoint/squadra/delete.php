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
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/squadra.php';
use component\database;

try {
    $database = new database();
    $db = $database->getConnection();
    $squadra = new squadra($db);

    $id = isset($_GET['id']) ? $_GET['id'] : die(json_encode([
        "message" => "ID squadra mancante",
        "status" => "error"
    ]));

    $result = $squadra->delete($id);

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
            "message" => "Nessuna squadra trovata con questo ID.",
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