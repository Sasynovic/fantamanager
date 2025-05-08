<?php
use component\database;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/divisione.php';

$database = new database();
$db = $database->getConnection();
$divisione = new divisione($db);

// Parametri di paginazione
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 100;
$offset = ($page - 1) * $limit;

// Parametro di ricerca
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Conta il totale dei record
$total_records = $divisione->count($id);

// Recupera i record paginati
$stmt = $divisione->read($id,$limit);
$num = $stmt->rowCount();

if ($num > 0) {
  $response = [
    'divisioni' => [],
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
        $response['divisioni'][] = [
            "id" => $row['id'],
            "nome_divisione" => $row['nome_divisione'],
            "bandiera" => $row['bandiera']
        ];
    }

    http_response_code(200);
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode([
        'message' => 'Nessuna divisione trovato.',
        'pagination' => [
            'total_items' => 0,
            'current_page' => (int)$page,
            'items_per_page' => (int)$limit,
            'total_pages' => 0
        ]
    ]);
}