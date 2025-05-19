<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../config/database.php';
require_once '../../models/presidenti.php';
use component\database;

// Gestione CORS
// Definisci gli origini consentiti
$allowed_origins = [
    'https://barrettasalvatore.it',
    'https://fantamanagerpro.eu'
];

// Verifica se l'origine della richiesta è nella lista degli origini consentiti
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // Se l'origine non è consentita, imposta un'origine predefinita o non impostare l'header
    header("Access-Control-Allow-Origin: https://tuodominio.com");
    // Alternativamente, puoi restituire un errore 403 Forbidden
    // http_response_code(403);
    // echo json_encode(["message" => "Origine non autorizzata", "success" => false]);
    // exit;
}

// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Connessione al database
$database = new Database();
$db = $database->getConnection();

// Creazione oggetto Presidenti
$presidenti = new presidenti($db);

// Leggi l'input JSON
$data = json_decode(file_get_contents("php://input"));

// Validazione metodo HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    $requiredFields = ['nome', 'cognome'];
    foreach ($requiredFields as $field) {
        if (!isset($data->$field) || empty($data->$field)) {
            http_response_code(400);
            echo json_encode(["message" => "Il campo $field è obbligatorio", "success" => false]);
            exit;
        }
    }

    // Chiamata al metodo create
    $result = $presidenti->create($data->nome, $data->cognome);

    if ($result) {
        http_response_code(201);
        echo json_encode([
            "message" => "Presidente creato con successo",
            "success" => true
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "message" => "Errore durante la creazione del presidente",
            "success" => false
        ]);
    }

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        "message" => $e->getMessage(),
        "success" => false
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Errore del database: " . $e->getMessage(),
        "success" => false
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Errore generico: " . $e->getMessage(),
        "success" => false
    ]);
}
