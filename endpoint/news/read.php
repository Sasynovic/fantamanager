<?php
use component\database;
header("Access-Control-Allow-Origin: *");

// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
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
$id = $_GET['id'] ?? null;
$id_competizione_filter = isset($_GET['id_competizione']) ? intval($_GET['id_competizione']) : null;
$search = $_GET['search'] ?? null;
$visibile =$_GET['visibile'] ?? null;

// Conta il totale dei record
$total_records = $news->count($visibile,$id,$id_competizione_filter,$search);

// Recupera i record paginati
$stmt = $news->read($visibile,$id,$id_competizione_filter, $search, $limit, $offset);
$num = $stmt->rowCount();

if ($num > 0) {
    $response = [
        'news' => [],
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