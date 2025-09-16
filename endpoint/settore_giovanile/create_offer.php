<?php
// Gestione CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/database.php';
require_once '../../models/offerte_sgs.php';
use component\database;

// Connessione al database
$database = new Database();
$db = $database->getConnection();

// Creazione oggetto Presidenti
$offerte_sgs = new offerte_sgs($db);

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
    $requiredFields = ['id_squadra', 'id_associazione_g', 'valore_offerta'];
    foreach ($requiredFields as $field) {
        if (!isset($data->$field)) {
            http_response_code(400);
            echo json_encode(["message" => "Il campo $field è obbligatorio", "success" => false]);
            exit;
        }
        if ($field !== 'valore_offerta' && empty($data->$field)) {
            http_response_code(400);
            echo json_encode(["message" => "Il campo $field è vuoto", "success" => false]);
            exit;
        }
    }

    // Chiamata al metodo create
    $result = $offerte_sgs->create(
        (int)$data->id_squadra,
        (int)$data->id_associazione_g,
        (int)$data->valore_offerta
    );

    if ($result) {
        http_response_code(201);
        echo json_encode([
            "message" => "Offerta creata con successo",
            "success" => true
        ]);
    } else if($result === false) {
        http_response_code(400);
        echo json_encode([
            "message" => "Errore nella creazione dell'offerta",
            "success" => false
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "message" => "Errore del server",
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
