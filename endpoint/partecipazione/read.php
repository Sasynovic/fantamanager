<?php

use component\database;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/partecipazione.php';

$database = new database();
$db = $database->getConnection();
$partecipazione = new partecipazione($db);

if(isset($_GET['id'])) {
    $id_competizione = $_GET['id'];
    $stmt = $partecipazione->readCompetizione($id_competizione);
} else {
    $stmt = $partecipazione->read();
}

$num = $stmt->rowCount();

if ($num > 0) {
    $partecipazioni_arr = array();
    $partecipazioni_arr["squadre"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $partecipazione_item = array(
            "id" => $id,
            "nome_squadra" => $nome_squadra,
            "presidente" => $nome_pres . ' ' . $cognome_pres,
            "vicepresidente" => $nome_vice . ' ' . $cognome_vice,
            "stadio" => $nome_stadio
        );

        $partecipazioni_arr["squadre"][] = $partecipazione_item;
    }

    http_response_code(200);
    echo json_encode($partecipazioni_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Nessuna partecipazione trovata."));
}
