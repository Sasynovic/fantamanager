<?php

use component\database;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/divisione.php';

$database = new database();
$db = $database->getConnection();
$divisione = new divisione($db);

$stmt = $divisione->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $divisioni_arr = array();
    $divisioni_arr["divisioni"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $divisione_item = array(
            "id" => $id,
            "nome_divisione" => $nome_divisione,
            "bandiera" => $bandiera
        );

        $divisioni_arr["divisioni"][] = $divisione_item;
    }

    http_response_code(200);
    echo json_encode($divisioni_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna divisione trovata."));
}
