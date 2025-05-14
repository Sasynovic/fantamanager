<?php
use component\database;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/operazioni.php';

$database = new database();
$db = $database->getConnection();
$operazioni = new operazioni($db);

// Parametri di paginazione
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : null;
if($limit != null) {
    $offset = ($page - 1) * $limit;
}else{
    $offset = null;
}


$idTrattativa = isset($_GET['id_trattativa']) ? intval($_GET['id_trattativa']) : null;

// Conta il totale dei record
$total_records = $operazioni->count($idTrattativa);

// Recupera i record paginati
$stmt = $operazioni->read($idTrattativa,$limit, $offset);
$num = $stmt->rowCount();

if ($num > 0) {
    if($limit != null) {
        $response = [
            'operazioni' => [],
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
        $response['operazioni'][] = [
            "id_operazione" => (int)$row['id'],
            "trattativa" => [
                "id" => (int)$row['id_trattativa'],
                "descrizione" => $row['descrizione_trattativa'],
                "id_competizione" => (int)$row['id_competizione'],
                "id_squadra_c" => (int)$row['id_squadra_c'],
                "id_squadra_r" => (int)$row['id_squadra_r'],
                "nome_squadra_1" => $row['nome_squadra1'],
                "nome_squadra_2" => $row['nome_squadra2'],
                "data_creazione" => $row['data_creazione'],
                "ufficializzata" => (bool)$row['ufficializzata'],
            ],
            "calciatore" => [
                "id" => (int)$row['id_calciatore'],
                "nome" => $row['nome_calciatore'],
                "n_movimenti" => (int)$row['n_movimenti'],
                "scambiato" => (bool)$row['scambiato'],
            ],
            "scambio" => [
                "id_associazione" => (int)$row['id_associazione'],
                "id_tipologia" => (int)$row['id_tipologia_scambio'],
                "metodo" => $row['nome_metodo_scambio'],
                "valore_riscatto" => $row['valore_riscatto'],
            ],
            "finestra_mercato" => [
                "nome" => $row['nome_finestra_mercato'] ?? '',
                "data_inizio" => $row['data_inizio_finestra'],
                "data_fine" => $row['data_fine_finestra']
            ]
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