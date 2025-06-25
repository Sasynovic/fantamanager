<?php

use component\database;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once '../../config/database.php';
require_once '../../models/associazioni.php';

$database = new database();
$db = $database->getConnection();
$associazioni = new associazioni($db);

// Filtro per ID squadra
$id_squadra_filter = isset($_GET['id_squadra']) ? intval($_GET['id_squadra']) : null;

// Richiama read con o senza filtro
$stmt = $associazioni->read($id_squadra_filter);

$num = $stmt->rowCount();

if ($num > 0) {
    $associazioni_arr = array();
    $associazioni_arr["associazioni"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $associazioni_item = array(
            "id" => $id,
            "id_squadra" => $id_squadra,
            "nome_squadra" => $nome_squadra,
            "nome_calciatore" => $nome_calciatore,
            "ruolo_calciatore" => $ruolo_calciatore,
            "costo_calciatore" => $costo_calciatore,
            "nome_squadra_calciatore" => $nome_squadra_calciatore,
            "eta" => $eta,
            "fvm" => $fvm,
            "n_movimenti" => $n_movimenti,
            "scambiato" => $scambiato
        );

        $associazioni_arr["associazioni"][] = $associazioni_item;
    }

    http_response_code(200);
    echo json_encode($associazioni_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna associazione trovata."));
}
