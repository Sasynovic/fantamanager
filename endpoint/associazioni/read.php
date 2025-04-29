<?php

use component\database;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/associazioni.php';

$database = new database();
$db = $database->getConnection();
$associazioni = new associazioni($db);

// Filtro per ID squadra
$id_squadra_filter = isset($_GET['id_squadra']) ? intval($_GET['id_squadra']) : null;

// Richiama read con o senza filtro
$stmt = $associazioni->read($id_squadra_filter);

$num = $stmt->rowCount();

if ($num > 0) {
    $associazioni_arr = array();
    $associazioni_arr["associazioni"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $associazioni_item = array(
            "id" => $id,
            "nome_squadra" => $nome_squadra,
            "nome_calciatore" => $nome_calciatore,
            "ruolo_calciatore" => $ruolo_calciatore,
            "costo_calciatore" => $costo_calciatore
        );

        $associazioni_arr["associazioni"][] = $associazioni_item;
    }

    http_response_code(200);
    echo json_encode($associazioni_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna associazione trovata."));
}
