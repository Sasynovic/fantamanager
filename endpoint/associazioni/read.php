<?php

use component\database;

header("Access-Control-Allow-Origin: *");
// Gli altri header CORS rimangono invariati
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");
require_once '../../config/database.php';
require_once '../../models/associazioni.php';

$database = new database();
$db = $database->getConnection();
$associazioni = new associazioni($db);

//Filtro id calciatore
$id_calciatore = isset($_GET['id_calciatore']) ? intval($_GET['id_calciatore']) : null;
// Filtro per ID squadra
$id_squadra_filter = isset($_GET['id_squadra']) ? intval($_GET['id_squadra']) : null;
// Filtro per fuori listone
$fuori_listone_filter = isset($_GET['fuori_listone']) ? intval($_GET['fuori_listone']) : null;
//Filtro prelazione
$prelazione_filter = isset($_GET['prelazione']) ? intval($_GET['prelazione']) : null;

// Richiama read con o senza filtro
$stmt = $associazioni->read($id_calciatore,$id_squadra_filter, $fuori_listone_filter, $prelazione_filter);

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
            "id_calciatore" => $id_calciatore,
            "nome_calciatore" => $nome_calciatore,
            "ruolo_calciatore" => $ruolo_calciatore,
            "costo_calciatore" => $costo_calciatore,
            "sett_giov" => $sett_giov ?? 0,
            "prestito" => $prestito ?? 0,
            "nome_squadra_calciatore" => $nome_squadra_calciatore,
            "eta" => $eta,
            "fvm" => $fvm,
            "n_movimenti" => $n_movimenti,
            "scambiato" => $scambiato,
            "fuori_listone" => $fuori_listone,
            "prelazione" => $prelazione,
            "Timestamp" => $timestamp,
            "fine_prelazione" => $fine_prelazione
        );

        $associazioni_arr["associazioni"][] = $associazioni_item;
    }

    http_response_code(200);
    echo json_encode($associazioni_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna associazione trovata."));
}
