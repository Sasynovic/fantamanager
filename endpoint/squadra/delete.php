<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");
require_once '../../config/database.php';
require_once '../../models/squadra.php';

use component\database;

// Recupera i dati JSON della richiesta
$data = json_decode(file_get_contents("php://input"));

// Verifica se l'ID è presente nella richiesta
if (!isset($data->id)) {
    echo json_encode(["success" => false, "message" => "ID della squadra mancante"]);
    exit;
}

$id = $data->id;
$database = new database();
$db = $database->getConnection();
$squadra = new squadra($db);

// Imposta l'ID della squadra
$squadra->id = $id;

// Esegui la cancellazione della squadra e gestisci il risultato
if ($squadra->delete()) {
    echo json_encode(["success" => true, "message" => "Squadra eliminata con successo", "data" => $squadra]);
} else {
    echo json_encode(["success" => false, "message" => "Errore nell'eliminazione della squadra"]);
}

exit;
