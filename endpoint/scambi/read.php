<?php

use component\database;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/scambi.php';

$database = new database();
$db = $database->getConnection();
$scambi = new scambi($db);
$squadra_coinvolta = isset($_GET['id']) ? intval($_GET['id']) : null;


$stmt = $scambi->read($squadra_coinvolta);
$num = $stmt->rowCount();

if ($num > 0) {
    $scambi_arr = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $scambi_arr["scambi"][] = array(
            "id" => $id,
            "id_trattativa" => $id_trattativa,
            "nome_calciatore" => $nome_calciatore,
            "debito_credito" => $debito_credito ?? 0,
            "nome_squadra_cedente" => $nome_squadra_cedente,
            "id_squadra_cedente" => $id_squadra_cedente,
            "nome_squadra_ricevente" => $nome_squadra_ricevente

        );
    }

    http_response_code(200);
    echo json_encode($scambi_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna scambi trovata."));
}
