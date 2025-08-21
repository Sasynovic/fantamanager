<?php
use component\database;
header("Access-Control-Allow-Origin: *");
// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
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
$search = $_GET['search'] ?? null;
$nome_presidente_filter = $_GET['nome_presidente'] ?? null;
$nome_squadra_filter = $_GET['nome_squadra'] ?? null;
$id_squadra_filter = isset($_GET['id_squadra']) ? intval($_GET['id_squadra']) : null;
$rate = isset($_GET['rate']) ? intval($_GET['rate']) : null;
$prezzo = isset($_GET['prezzo']) ? intval($_GET['prezzo']) : null;

//Conta il totale dei record
$total_records = $squadra->count($prezzo,$rate,$vendita_filter, $search, $nome_presidente_filter,$nome_squadra_filter, $id_squadra_filter);

// Recupera i record paginati
$stmt = $squadra->read($prezzo,$rate,$vendita_filter, $search, $nome_presidente_filter,$nome_squadra_filter, $id_squadra_filter, $limit, $offset);
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
            "rate" => $row['rate'],
            "vendita" => $row['vendita'],
            "prezzo" => $row['costo_iscrizione'],
            "valore_fvm" => $row['valore_fvm'],
            "fvm_old" => $row['fvm_old'],

            "dirigenza" => [
                "id_pres" => $row['id_pres'],
                "presidente" => $row['nome_pres'] . ' ' . $row['cognome_pres'],
                "id_vice" => $row['id_vice'],
                "vicepresidente" => $row['nome_vice'] . ' ' . $row['cognome_vice'],
                ],
            "finanze" => [
                "credito" => $row['credito'],
            ],

            "stadio" => [
                "id_stadio" => $row['id_stadio'],
                "nome_stadio" => $row['nome_stadio'],
                "livello_stadio" => $row['livello_stadio'],
                "costo_manutenzione" => $row['costo_manutenzione'],
                "costo_costruzione" => $row['costo_costruzione'],
                "bonus_casa_nazionale" => $row['bonus_casa_n'],
                "bonus_casa_uefa" => $row['bonus_casa_u'],
                "sold_out" => $row['sold_out'],
                "abbonati" => $row['abbonati'],
            ],
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
