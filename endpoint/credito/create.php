<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../config/database.php';
require_once '../../models/credito.php';
use component\database;


// Gestione CORS
// Definisci gli origini consentiti
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

// Validazione metodo HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Metodo non consentito", "success" => false]);
    exit;
}

// Verifica tipo contenuto
if ($_SERVER["CONTENT_TYPE"] !== "application/json") {
    http_response_code(415);
    echo json_encode(["message" => "Tipo di contenuto non supportato", "success" => false]);
    exit;
}

// Leggi l'input JSON
$data = json_decode(file_get_contents("php://input"));

// Validazione input
if (empty($data)) {
    http_response_code(400);
    echo json_encode(["message" => "Input non valido", "success" => false]);
    exit;
}

try {
    // Verifica campi obbligatori
    $requiredFields = ['id_squadra', 'id_trattativa', 'id_fm'];
    foreach ($requiredFields as $field) {
        if (!isset($data->$field) || empty($data->$field)) {
            http_response_code(400);
            echo json_encode(["message" => "Il campo $field è obbligatorio", "success" => false]);
            exit;
        }
    }

    // Conversione e sanitizzazione input
    $id_squadra = (int) $data->id_squadra;
    $id_trattativa = (int) $data->id_trattativa;
    $id_fm = (int) $data->id_fm;
    $credito_val = isset($data->credito) ? (int) $data->credito : null;

    // Connessione al database
    $database = new Database();
    $db = $database->getConnection();

    // Creazione oggetto Operazioni
    $creditoModel = new credito($db);

    // Chiamata al metodo create
    $result = $creditoModel->create($id_squadra, $id_trattativa, $id_fm, $credito_val);

    if ($result) {
        http_response_code(201);
        echo json_encode([
            "message" => "Credito creato con successo",
            "success" => true
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "message" => "Errore durante la creazione del credito",
            "success" => false
        ]);
    }

} catch (InvalidArgumentException $e) {
    error_log("Input non valido: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        "message" => $e->getMessage(),
        "success" => false
    ]);
} catch (PDOException $e) {
    error_log("Errore DB: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "message" => "Errore del database: " . $e->getMessage(),
        "success" => false
    ]);
} catch (Exception $e) {
    error_log("Errore generico: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "message" => "Errore generico: " . $e->getMessage(),
        "success" => false
    ]);
}
