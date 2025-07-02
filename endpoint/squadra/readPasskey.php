<?php
header("Content-Type: application/json; charset=UTF-8");
session_start();

require_once '../../config/database.php';

use component\database;

// Recupera i dati dalla richiesta
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->idSquadra1, $data->idSquadra2, $data->passkey)) {
    echo json_encode(["success" => false, "message" => "Id squadra o passkey mancanti"]);
    exit;
}

$id_squadra1 = trim($data->idSquadra1);
$id_squadra2 = trim($data->idSquadra2);
$passkey = trim($data->passkey);

try {
    $db = new database();
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception("Connessione al database fallita.");
    }

    // Recupera l'id del presidente associato alla passkey
    $stmt = $conn->prepare("SELECT id, nome, cognome FROM presidenti WHERE passkey = :passkey");
    $stmt->bindParam(':passkey', $passkey);
    $stmt->execute();

    $presidente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$presidente) {
        echo json_encode(["success" => false, "message" => "Passkey errata o non trovata"]);
        exit;
    }

    // Verifica se una delle due squadre appartiene al presidente e recupera anche il nome squadra
    $stmt = $conn->prepare("
        SELECT s.nome_squadra AS nome_squadra
        FROM squadre s
        WHERE s.id_pres = :id_pres
        AND (s.id = :s1 OR s.id = :s2)
        LIMIT 1
    ");
    $stmt->bindParam(':id_pres', $presidente['id']);
    $stmt->bindParam(':s1', $id_squadra1);
    $stmt->bindParam(':s2', $id_squadra2);
    $stmt->execute();

    $squadra = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($squadra) {
        echo json_encode([
            "success" => true,
            "message" => "Autenticazione riuscita",
            "presidente" => [
                "id" => $presidente['id'],
                "nome" => $presidente['nome'],
                "cognome" => $presidente['cognome']
            ],
            "squadra" => [
                "nome" => $squadra['nome_squadra']
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Nessuna delle due squadre appartiene al presidente."]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Errore interno: " . $e->getMessage()]);
}
