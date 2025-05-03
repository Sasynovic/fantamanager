<?php

use component\database;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/settore_giovanile.php';

$database = new database();
$db = $database->getConnection();
$settore_giovanile = new settore_giovanile($db);

$id_squadra_filter = isset($_GET['id_squadra']) ? intval($_GET['id_squadra']) : null;

$stmt = $settore_giovanile->read($id_squadra_filter);


$num = $stmt->rowCount();

if ($num > 0) {
    $squadre_arr = array();
    $squadre_arr["settore_giovanile"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $squadra_item = array(
            "id" => $id,
            "nome_squadra" => $nome_squadra,
            "nome_calciatore" => $nome_calciatore,
            "stagione" => $anno_stagione,
            "fuori_listone" => $fuori_listone,
            "prima_squadra" => $prima_squadra
        );

        $squadre_arr["settore_giovanile"][] = $squadra_item;
    }

    http_response_code(200);
    echo json_encode($squadre_arr);

} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna squadra trovata."));
}
