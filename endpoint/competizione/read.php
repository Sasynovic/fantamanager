<?php
use component\database;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/competizione.php';

$database = new database();
$db = $database->getConnection();

$competizione = new competizione($db);
if (isset($_GET['id_divisione'])) {
    $id_divisione = $_GET['id_divisione'];
    $stmt = $competizione->readDivision($id_divisione);
} else {
    $stmt = $competizione->read();
}
$num = $stmt->rowCount();

if ($num > 0) {
    $competizioni_arr = array();
    $competizioni_arr["competizioni"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $competizione_item = array(
            "id" => $id,
            "nome_competizione" => $nome_competizione,
            "id_divisione" => $id_divisione,
            "nome_divisione" => $nome_divisione
        );

        $competizioni_arr["competizioni"][] = $competizione_item;
    }

    http_response_code(200);
    echo json_encode($competizioni_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna competizione trovata."));
}