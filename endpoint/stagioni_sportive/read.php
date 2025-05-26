<?php

use component\database;
header("Access-Control-Allow-Origin: *");
// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
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
        $stagioni_sportive["stagioni_sportive"][] = $stagioni_sportive_item;
    }

    http_response_code(200);
    echo json_encode($stagioni_sportive);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna stagione trovata."));
}
