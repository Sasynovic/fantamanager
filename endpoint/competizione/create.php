<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../config/database.php';
require_once '../../models/competizione.php';
use component\database;

// Gestione CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Connessione al database
$database = new Database();
$db = $database->getConnection();

// Creazione oggetto Presidenti
$competizione = new competizione($db);

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
    $requiredFields = ['id_divisione', 'nome_competizione', 'id_stagione'];
    foreach ($requiredFields as $field) {
        if (!isset($data->$field) || empty($data->$field)) {
            http_response_code(400);
            echo json_encode(["message" => "Il campo $field è obbligatorio", "success" => false]);
            exit;
        }
    }

    // Chiamata al metodo create
    $result = $competizione->create($data->id_divisione, $data->nome_competizione, $data->id_stagione);

    if ($result) {
        http_response_code(201);
        echo json_encode([
            "message" => "Competizione creata con successo",
            "success" => true
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "message" => "Errore durante la creazione della competizione",
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
