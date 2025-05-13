<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../config/database.php';
require_once '../../models/trattative.php';
use component\database;

// Gestione CORS
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
    $requiredFields = ['id_competizione', 'id_squadra1', 'id_squadra2'];
    foreach ($requiredFields as $field) {
        if (!isset($data->$field) || empty($data->$field)) {
            http_response_code(400);
            echo json_encode(["message" => "Il campo $field è obbligatorio", "success" => false]);
            exit;
        }
    }

    // Conversione e sanitizzazione input
    $id_competizione = (int) $data->id_competizione;
    $id_squadra1 = (int) $data->id_squadra1;
    $id_squadra2 = (int) $data->id_squadra2;

    // Connessione al database
    $database = new Database();
    $db = $database->getConnection();

    // Creazione oggetto Operazioni
    $trattative = new trattative($db);

    // Chiamata al metodo create e recupero dell'ID
    $id_trattativa = $trattative->create($id_competizione, $id_squadra1, $id_squadra2);

    if ($id_trattativa) {
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