<?php
use component\database;
// Definisci gli origini consentiti
header("Access-Control-Allow-Origin: *");
// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once '../../config/database.php';
require_once '../../models/tipologia_scambio.php';

$database = new database();
$db = $database->getConnection();
$tipologia_scambio = new tipologia_scambio($db);

// Parametri di paginazione
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 100;
$offset = ($page - 1) * $limit;

// Parametro di ricerca
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Conta il totale dei record
$total_records = $tipologia_scambio->count($search);

// Recupera i record paginati
$stmt = $tipologia_scambio->read($search, $limit, $offset);
$num = $stmt->rowCount();

if ($num > 0) {
    $response = [
        'tipologia_scambio' => [],
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
        $response['tipologia_scambio'][] = [
            "id_tipologia" => $row['id_tipologia'],
            "id_metodo" => $row['id_metodo'],
            "nome_metodo" => $row['nome_metodo'],
            "id_finestra_mercato" => $row['id_finestra_mercato'],
            "finestra_mercato" => [
                "id" => $row['id_finestra_mercato'],
                "nome" => $row['nome'],
                "data_inizio" => $row['data_inizio'],
                "data_fine" => $row['data_fine'],
                "stagione" => [
                    "id" => $row['id_stagione'],
                    "stagione" => $row['stagione'],
                    "attiva" => $row['attiva']
                ]
            ]
        ];
    }

    http_response_code(200);
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode([
        'message' => 'Nessuna tipologia scambio trovato.',
        'pagination' => [
            'total_items' => 0,
            'current_page' => (int)$page,
            'items_per_page' => (int)$limit,
            'total_pages' => 0
        ]
    ]);
}