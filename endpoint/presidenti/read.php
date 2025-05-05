<?php

use component\database;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/presidenti.php';

$database = new database();
$db = $database->getConnection();
$presidenti = new presidenti($db);

$limit = isset($_GET['limit']) ? $_GET['limit'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

$stmt = $presidenti->read($limit, $search);
$num = $stmt->rowCount();

if ($num > 0) {
    $presidenti_arr = array();
    $presidenti_arr["presidenti"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $presidente_item = array(
            "id" => $id,
            "nome" => $nome,
            "cognome" => $cognome
        );

        array_push($presidenti_arr["presidenti"], $presidente_item);
    }

    http_response_code(200);
    echo json_encode($presidenti_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessun presidente trovato."));
}
