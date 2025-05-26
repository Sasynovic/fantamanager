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
    // Se l'origine non è consentita, restituire un errore 403 Forbidden
    http_response_code(403);
    echo json_encode(["message" => "Origine non autorizzata", "success" => false]);
    exit;
}

// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once '../../config/database.php';
require_once '../../models/presidenti.php';

$database = new database();
$db = $database->getConnection();
$presidenti = new presidenti($db);

// Parametri di paginazione
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : null;
if($limit != null) {
    $offset = ($page - 1) * $limit;
}else{
    $offset = null;
}

// Parametro di ricerca
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Conta il totale dei record
$total_records = $presidenti->count($search);

// Recupera i record paginati
$stmt = $presidenti->read($limit, $offset, $search);
$num = $stmt->rowCount();

if ($num > 0) {
    if($limit != null) {
        $response = [
            'credito' => [],
            'pagination' => [
                'total_items' => (int)$total_records,
                'current_page' => (int)$page,
                'items_per_page' => (int)$limit,
                'total_pages' => ceil($total_records / $limit),
                'has_next_page' => ($page * $limit) < $total_records,
                'has_previous_page' => $page > 1
            ]
        ];
    }

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response['presidenti'][] = [
            "id" => $row['id'],
            "nome" => $row['nome'],
            "cognome" => $row['cognome']
        ];
    }

    http_response_code(200);
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode([
        'message' => 'Nessun presidente trovato.',
        'pagination' => [
            'total_items' => 0,
            'current_page' => (int)$page,
            'items_per_page' => (int)$limit,
            'total_pages' => 0
        ]
    ]);
}