<?php

use component\database;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/squadra.php';

$database = new database();
$db = $database->getConnection();
$squadra = new squadra($db);

$vendita_filter = isset($_GET['vendita']) ? intval($_GET['vendita']) : null;
$stmt = $squadra->read($vendita_filter);

$num = $stmt->rowCount();

if ($num > 0) {
    $squadre_arr = array();
    $squadre_arr["squadre"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $squadra_item = array(
            "id" => $id,
            "nome_squadra" => $nome_squadra,
            "presidente" => $nome_pres . ' ' . $cognome_pres,
            "vicepresidente" => $nome_vice . ' ' . $cognome_vice,
            "stadio" => $nome_stadio,
            "vendita" => $vendita
        );

        $squadre_arr["squadre"][] = $squadra_item;
    }

    http_response_code(200);
    echo json_encode($squadre_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna squadra trovata."));
}
