<?php
use component\database;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/competizione.php';

$database = new database();
$db = $database->getConnection();
$competizione = new competizione($db);

// Parametri di paginazione
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$offset = ($page - 1) * $limit;

// Parametro di ricerca
$id_divisione = isset($_GET['id_divisione']) ? $_GET['id_divisione'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$id_competizione = isset($_GET['id_competizione']) ? $_GET['id_competizione'] : null;

// Conta il totale dei record
$total_records = $competizione->count($search, $id_divisione,$id_competizione);
// Recupera i record paginati
$stmt = $competizione->read($id_competizione, $id_divisione, $search, $limit, $offset);
$num = $stmt->rowCount();

if ($num > 0) {
    $response = [
        'competizione' => [],
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
        $response['competizione'][] = [
            "id" => $row['id'],
            "nome_competizione" => $row['nome_divisione'] . " " . $row['nome_competizione'],
            "anno" => $row['anno'],
            "id_divisione" => $row['id_divisione'],
            "attiva" => $row['attiva']
        ];
    }

    http_response_code(200);
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode([
        'message' => 'Nessuna competizione trovata..',
        'pagination' => [
            'total_items' => 0,
            'current_page' => (int)$page,
            'items_per_page' => (int)$limit,
            'total_pages' => 0
        ]
    ]);
}