<?php

use component\database;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/stagioni_sportive.php';

$database = new database();
$db = $database->getConnection();
$stagioni_sportive = new stagioni_sportive($db);


$stmt = $stagioni_sportive->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $stagioni_sportive = array();
    $stagioni_sportive["stagioni_sportive"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $stagioni_sportive_item = array(
            "id" => $id,
            "stagione" => $stagione
        );

        array_push($stagioni_sportive["stagioni_sportive"], $stagioni_sportive_item);
    }

    http_response_code(200);
    echo json_encode($stagioni_sportive);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna stagione trovata."));
}
