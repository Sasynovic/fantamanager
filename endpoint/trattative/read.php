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
require_once '../../models/trattative.php';

$database = new database();
$db = $database->getConnection();
$trattative = new trattative($db);

// Parametri di paginazione
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$offset = ($page - 1) * $limit;

//Parametri di ricerca
$ufficializzata = $_GET['ufficializzata'] ?? false;
$id_trattativa = $_GET['id'] ?? null;
$id_squadra = $_GET['id_squadra'] ?? null;

// Conta il totale dei record
$total_records = $trattative->count($id_squadra,$id_trattativa,$ufficializzata);

// Recupera i record paginati
$stmt = $trattative->read($id_squadra,$id_trattativa,$ufficializzata,$limit, $offset);
$num = $stmt->rowCount();

if ($num > 0) {
    $response = [
        'trattative' => [],
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
        $response['trattative'][] = [
            'id' => $row['id'],
            'descrizione' => $row['descrizione'],
            'id_squadra1' => $row['id_squadra1'],
            'id_squadra2' => $row['id_squadra2'],
            'id_competizione' => $row['id_competizione'],
            'nome_squadra1' => $row['nome_squadra1'],
            'nome_squadra2' => $row['nome_squadra2'],
            'ufficializzata' => $row['ufficializzata'],
            'data_creazione' => $row['data_creazione']
        ];
    }

    http_response_code(200);
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode([
        'message' => 'Nessuna trattativa trovato.',
        'pagination' => [
            'total_items' => 0,
            'current_page' => (int)$page,
            'items_per_page' => (int)$limit,
            'total_pages' => 0
        ]
    ]);
}