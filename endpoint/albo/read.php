<?php

use component\database;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/albo.php';

$database = new database();
$db = $database->getConnection();
$albo = new albo($db);

// Filtro per ID squadra
$id_squadra_filter = isset($_GET['id_squadra']) ? intval($_GET['id_squadra']) : null;
// Filtro per ID competizione
$id_competizione_filter = isset($_GET['id_competizione']) ? intval($_GET['id_competizione']) : null;
// Filtro per anno
$anno_filter = isset($_GET['anno']) ? intval($_GET['anno']) : null;

// Richiama read con o senza filtro
$stmt = $albo->read($id_squadra_filter, $anno_filter, $id_competizione_filter);

$num = $stmt->rowCount();

if ($num > 0) {
    $albo_arr = array();
    $albo_arr["albo"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $albo_item = array(
            "id" => $id,
            "anno" => $anno,
            "id_competizione" => $id_competizione,
            "nome_competizione" => $nome_divisione .' '. $nome_competizione,
            "nome_squadra" => $nome_squadra,
            "id_squadra" => $id_squadra
        );

        $albo_arr["albo"][] = $albo_item;
    }

    http_response_code(200);
    echo json_encode($albo_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna associazione trovata."));
}
