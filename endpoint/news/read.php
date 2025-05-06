<?php

use component\database;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/news.php';

$database = new database();
$db = $database->getConnection();
$news = new news($db);

// Parametri di paginazione
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$offset = ($page - 1) * $limit;

// Parametro di ricerca
$id_competizione_filter = isset($_GET['id_competizione']) ? intval($_GET['id_competizione']) : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Conta il totale dei record
$total_records = $news->count($id_competizione_filter,$search);

// Recupera i record paginati
$stmt = $news->read($id_competizione_filter, $search, $limit, $offset);
$num = $stmt->rowCount();

if ($num > 0) {
    $response = [
        'presidenti' => [],
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
        $response['news'][] = [
            "id" => $row['id'],
            "titolo" => $row['titolo'],
            "contenuto" => $row['contenuto'],
            "data_pubblicazione" => $row['data_pubblicazione'],
            "autore" => $row['autore'],
            "id_competizione" => $row['id_competizione'],
            "nome_competizione" => $row['nome_divisione'] . ' ' . $row['nome_competizione'],
            "visibile" => $row['visibile']
        ];
    }

    http_response_code(200);
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode([
        'message' => 'Nessun news trovato.',
        'pagination' => [
            'total_items' => 0,
            'current_page' => (int)$page,
            'items_per_page' => (int)$limit,
            'total_pages' => 0
        ]
    ]);
}