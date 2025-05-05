<?php

use component\database;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/news.php';

$database = new database();
$db = $database->getConnection();
$news = new news($db);

$id_competizione_filter = isset($_GET['id_competizione']) ? intval($_GET['id_competizione']) : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;

$stmt = $news->read($id_competizione_filter, $limit, $search);
$num = $stmt->rowCount();

if ($num > 0) {
    $squadre_arr = array();
    $squadre_arr["news"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $squadra_item = array(
            "id" => $id,
            "titolo" => $titolo,
            "contenuto" => $contenuto,
            "data_pubblicazione" => $data_pubblicazione,
            "autore" => $autore,
            "id_competizione" => $id_competizione,
            "visibile" => $visibile
        );

        $squadre_arr["news"][] = $squadra_item;
    }

    http_response_code(200);
    echo json_encode($squadre_arr);

} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna squadra trovata."));
}
