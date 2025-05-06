<?php
use component\database;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/squadra.php';

$database = new database();
$db = $database->getConnection();
$squadra = new squadra($db);

// Parametri di paginazione
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$offset = ($page - 1) * $limit;

// Parametro di ricerca
$vendita_filter = isset($_GET['vendita']) ? intval($_GET['vendita']) : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$nome_presidente_filter = isset($_GET['nome_presidente']) ? $_GET['nome_presidente'] : null;
$id_squadra_filter = isset($_GET['id_squadra']) ? intval($_GET['id_squadra']) : null;

//Conta il totale dei record
$total_records = $squadra->count($vendita_filter, $search, $nome_presidente_filter, $id_squadra_filter);

// Recupera i record paginati
$stmt = $squadra->read($vendita_filter, $search, $nome_presidente_filter, $id_squadra_filter, $limit, $offset);
$num = $stmt->rowCount();

if ($num > 0) {
    $response = [
        'squadra' => [],
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
        extract($row);
        $response['squadra'][] = [
            "id" => $row['id'],
            "nome_squadra" => $row['nome_squadra'],
            "presidente" => $row['nome_pres'] . ' ' . $row['cognome_pres'],
            "vicepresidente" => $row['nome_vice'] . ' ' . $row['cognome_vice'],
            "valore_fvm" => $row['valore_fvm'],
            "stadio" => $row['nome_stadio'],
            "livello_stadio" => $row['livello_stadio'],
            "costo_manutenzione" => $row['costo_manutenzione'],
            "bonus_casa_nazionale" => $row['bonus_casa_n'],
            "bonus_casa_uefa" => $row['bonus_casa_u'],
            "guadagno_crediti_campionato" => $row['guadagno_crediti_campionato'],
            "guadagno_crediti_coppa" => $row['guadagno_crediti_coppa'],
            "rate" => $row['rate'],
            "vendita" => $row['vendita'],
            "prezzo" => $row['costo_iscrizione'],
        ];
    }

    http_response_code(200);
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode([
        'message' => 'Nessuna squadra trovata.',
        'pagination' => [
            'total_items' => 0,
            'current_page' => (int)$page,
            'items_per_page' => (int)$limit,
            'total_pages' => 0
        ]
    ]);
}
