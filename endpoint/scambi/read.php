<?php

use component\database;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../models/scambi.php';

$database = new database();
$db = $database->getConnection();
$scambi = new scambi($db);
$squadra_coinvolta = isset($_GET['id']) ? intval($_GET['id']) : null;

$stmt = $scambi->read($squadra_coinvolta);
$num = $stmt->rowCount();

if ($num > 0) {
    $scambi_arr = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $id_trattativa_key = $id_trattativa;

        if (!isset($scambi_arr[$id_trattativa_key])) {
            // Inizializza il blocco della trattativa con le info principali
            $scambi_arr[$id_trattativa_key] = [
                "id_trattativa" => $id_trattativa_key,
                "descrizione" => $descrizione,
                "data_inizio" => $data_inizio,
                "data_fine" => $data_fine,
                "scambi" => []
            ];
        }

        // Aggiungi lo scambio sotto questa trattativa
        $scambi_arr[$id_trattativa_key]["scambi"][] = [
            "id" => $id,
            "nome_calciatore" => $nome_calciatore,
            "credito_debito" => $credito_debito ?? 0,
            "nome_squadra_cedente" => $nome_squadra_cedente,
            "nome_squadra_ricevente" => $nome_squadra_ricevente
        ];
    }

    // Riindicizza come array per output ordinato
    $scambi_arr = array_values($scambi_arr);

    http_response_code(200);
    echo json_encode(["trattive" => $scambi_arr], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Nessuno scambio trovato."]);
}
