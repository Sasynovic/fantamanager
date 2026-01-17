<?php
use component\database;
header("Access-Control-Allow-Origin: *");
// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/database.php';
require_once '../../models/calciatori.php';

$database = new database();
$db = $database->getConnection();
$calciatori = new calciatori($db);

$stmt = $calciatori->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $calciatori_arr["calciatori"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $calciatori_item = array(
            "id" => $id,
            "ruolo" => $ruolo,
            "nome" => $nome,
            "squadra" => $squadra,
            "fvm" => $fvm,
            "eta" => $eta,
            "fuori_listone" => $fuori_listone
        );
        $calciatori_arr["calciatori"][] = $calciatori_item;
    }

    http_response_code(200);
    echo json_encode($calciatori_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna calciatore trovato."));
}
