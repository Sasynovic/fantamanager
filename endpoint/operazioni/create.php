<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../config/database.php';
require_once '../../models/operazioni.php';
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
    $requiredFields = ['id_trattativa', 'id_associazione', 'id_tipologia_scambio', 'id_squadra_c', 'id_squadra_r'];
    foreach ($requiredFields as $field) {
        if (!isset($data->$field) || empty($data->$field)) {
            http_response_code(400);
            echo json_encode(["message" => "Il campo $field è obbligatorio", "success" => false]);
            exit;
        }
    }

    // Conversione e sanitizzazione input
    $id_trattativa = (int) $data->id_trattativa;
    $id_associazione = (int) $data->id_associazione;
    $id_tipologia_scambio = (int) $data->id_tipologia_scambio;
    $valore_riscatto = isset($data->valore_riscatto) ? (int) $data->valore_riscatto : null;
    $id_squadra_c = (int) $data->id_squadra_c;
    $id_squadra_r = (int) $data->id_squadra_r;

    // Connessione al database
    $database = new Database();
    $db = $database->getConnection();

    // Creazione oggetto Operazioni
    $operazioni = new operazioni($db);

    // Chiamata al metodo create
    $result = $operazioni->create($id_trattativa, $id_associazione, $id_tipologia_scambio, $id_squadra_c, $id_squadra_r, $valore_riscatto);

    if ($result) {
        http_response_code(201);
        echo json_encode([
            "message" => "Operazione creata con successo",
            "success" => true
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "message" => "Errore durante la creazione dell'operazione",
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
