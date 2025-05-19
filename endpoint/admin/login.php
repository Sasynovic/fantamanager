<?php
header("Content-Type: application/json; charset=UTF-8");
session_start();

require_once '../../config/database.php';

use component\database;

// Recupera i dati dalla richiesta
$data = json_decode(file_get_contents("php://input"));

// Controlla che username e password siano stati inviati
if (!isset($data->username, $data->password)) {
    echo json_encode(["success" => false, "message" => "Username o password mancanti"]);
    exit;
}

$username = trim($data->username);
$password = $data->password;

try {
    $db = new database();
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception("Connessione al database fallita.");
    }

    // Recupera l'admin dal database
    $stmt = $conn->prepare("SELECT id, password_hash FROM admin WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($admin = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $admin['password_hash'])) {
            // Login riuscito: imposta la sessione
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['last_activity'] = time(); // Timestamp per timeout

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Password errata"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Utente non trovato"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Errore interno: " . $e->getMessage()]);
}
