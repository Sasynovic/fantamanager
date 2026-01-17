<?php
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
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config/database.php';
require_once '../../models/calciatori.php';
use component\database;
// Connessione al database
$database = new Database();
$db = $database->getConnection();

// Creazione oggetto Presidenti
$calciatori = new calciatori($db);

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
    $requiredFields = ['id', 'ruolo', 'nome', 'squadra', 'fvm'];
    foreach ($requiredFields as $field) {
        if (!isset($data->$field) || empty($data->$field)) {
            http_response_code(400);
            echo json_encode(["message" => "Il campo $field è obbligatorio", "success" => false]);
            exit;
        }
    }

    $created_at = $data->created_at ?? date('Y-m-d H:i:s');

    // Chiamata al metodo create
    $result = $calciatori->create(
        $data->id,
        $data->ruolo,
        $data->nome,
        $data->squadra,
        $data->fvm
    );

    if ($result) {
        http_response_code(201);
        echo json_encode([
            "message" => "Calciatore creata con successo",
            "success" => true
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "message" => "Errore durante la creazione del calciatore",
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
