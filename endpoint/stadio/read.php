<?php
use component\database;
header("Access-Control-Allow-Origin: *");
// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once '../../config/database.php';
require_once '../../models/stadio.php';

$database = new database();
$db = $database->getConnection();
$stadio = new stadio($db);

$stmt = $stadio->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $stadio_arr["stadio"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $stadio_item = array(
            "id" => $id,
            "nome_stadio" => $nome_stadio,
            "livello_stadio" => $livello_stadio,
            "costo_manutenzione" => $costo_manutenzione,
            "bonus_casa_m" => $bonus_casa_n,
            "bonus_casa_u" => $bonus_casa_u
        );
        $stadio_arr["stadio"][] = $stadio_item;
    }

    http_response_code(200);
    echo json_encode($stadio_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna stadio trovata."));
}
