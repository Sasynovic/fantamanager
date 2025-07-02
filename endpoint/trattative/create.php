<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../config/database.php';
require_once '../../models/trattative.php';
use component\database;

header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Validazione metodo HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Metodo non consentito", "success" => false]);
    exit;
}

// Verifica tipo contenuto
$contentType = $_SERVER["CONTENT_TYPE"] ?? '';
if (stripos($contentType, 'application/json') === false) {
    http_response_code(415);
    echo json_encode(["message" => "Tipo di contenuto non supportato", "success" => false]);
    exit;
}

// Leggi e decodifica l'input JSON
$rawInput = file_get_contents("php://input");

if (!$rawInput) {
    http_response_code(400);
    echo json_encode(["message" => "Corpo della richiesta vuoto", "success" => false]);
    exit;
}

$data = json_decode($rawInput);

if (json_last_error() !== JSON_ERROR_NONE || !is_object($data)) {
    error_log("Errore decodifica JSON: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(["message" => "JSON non valido: " . json_last_error_msg(), "success" => false]);
    exit;
}

// Validazione campi richiesti
$requiredFields = ['id_competizione', 'id_squadra1', 'id_squadra2', 'id_presidente'];
foreach ($requiredFields as $field) {
    if (empty($data->$field)) {
        http_response_code(400);
        echo json_encode(["message" => "Il campo $field è obbligatorio", "success" => false]);
        exit;
    }
}

// Sanitizzazione input
$id_competizione = (int) $data->id_competizione;
$id_squadra1 = (int) $data->id_squadra1;
$id_squadra2 = (int) $data->id_squadra2;
$descrizione = isset($data->descrizione) ? trim($data->descrizione) : '';
$descrizione = htmlspecialchars(strip_tags($descrizione), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$id_presidente = isset($data->id_presidente) ? (int) $data->id_presidente : null;

// Esecuzione
try {
    $database = new Database();
    $db = $database->getConnection();
    $trattative = new trattative($db);

    $id_trattativa = $trattative->create($id_competizione, $id_squadra1, $id_squadra2, $descrizione, $id_presidente);

    if ($id_trattativa && is_numeric($id_trattativa)) {
        http_response_code(201);
        echo json_encode([
            "message" => "Trattativa creata con successo",
            "success" => true,
            "id_trattativa" => $id_trattativa
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "message" => "Errore durante la creazione della trattativa",
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
        "message" => "Errore del database",
        "success" => false
    ]);
} catch (Exception $e) {
    error_log("Errore generico: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "message" => "Errore generico",
        "success" => false
    ]);
}
