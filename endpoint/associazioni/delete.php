<?php
header('Content-Type: application/json');

/**
 * ============================
 * CORS CONFIG
 * ============================
 */
$allowed_origins = [
    'https://barrettasalvatore.it',
    'https://www.barrettasalvatore.it',
    'https://fantamanagerpro.eu'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

/**
 * Se same-origin (Origin mancante) oppure origin consentito
 */
if ($origin === '' || in_array($origin, $allowed_origins, true)) {
    header('Access-Control-Allow-Origin: ' . ($origin ?: '*'));
    header('Access-Control-Allow-Methods: DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
} else {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Origine non autorizzata'
    ]);
    exit;
}

/**
 * ============================
 * PREFLIGHT OPTIONS
 * ============================
 */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * ============================
 * CONSENTI SOLO DELETE
 * ============================
 */
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Metodo non consentito'
    ]);
    exit;
}

/**
 * ============================
 * LOGICA APPLICATIVA
 * ============================
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/associazioni.php';

use component\database;

try {
    $database = new database();
    $db = $database->getConnection();
    $associazioni = new associazioni($db);

    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID associazione mancante o non valido'
        ]);
        exit;
    }

    $deleted = $associazioni->delete((int)$id);

    if ($deleted) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Associazione eliminata con successo'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Nessuna associazione trovata con questo ID'
        ]);
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Errore del server',
        'error'   => $e->getMessage()
    ]);
}
