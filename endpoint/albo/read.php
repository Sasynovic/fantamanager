<?php

use component\database;

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

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/albo.php';

$database = new database();
$db = $database->getConnection();
$albo = new albo($db);

// Parametri di paginazione
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$offset = ($page - 1) * $limit;

// Filtri ricerca
$id_squadra_filter = isset($_GET['id_squadra']) ? intval($_GET['id_squadra']) : null;
$id_competizione_filter = isset($_GET['id_competizione']) ? intval($_GET['id_competizione']) : null;
$anno_filter = isset($_GET['anno']) ? intval($_GET['anno']) : null;

// Conta il totale dei record
$total_records = $albo->count($id_squadra_filter, $anno_filter, $id_competizione_filter);

// Richiama read con o senza filtro
$stmt = $albo->read($id_squadra_filter, $anno_filter, $id_competizione_filter, $limit, $offset);

$num = $stmt->rowCount();

if ($num > 0) {
    $response = [
        'albo' => [],
        'pagination' => [
            'total_items' => (int)$total_records,
            'current_page' => (int)$page,
            'items_per_page' => (int)$limit,
            'total_pages' => ceil($total_records / $limit),
            'has_next_page' => ($page * $limit) < $total_records,
            'has_previous_page' => $page > 1
        ]
    ];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
       $response['albo'][] = [
            "id" => $row['id'],
            "stagione" => $row['stagione'],
            "id_competizione" => $row['id_competizione'],
            "nome_competizione" => $row['nome_divisione'] .' '. $row['nome_competizione'],
            "nome_squadra" => $row['nome_squadra'],
            "id_squadra" => $row['id_squadra'],
           ];
    }
    http_response_code(200);
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode([
        'message' => 'Nessun vincitore trovato.',
        'pagination' => [
            'total_items' => 0,
            'current_page' => (int)$page,
            'items_per_page' => (int)$limit,
            'total_pages' => 0
        ]
    ]);
}