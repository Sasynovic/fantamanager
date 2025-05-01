<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
session_start();

header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';

use component\database;

$data = json_decode(file_get_contents("php://input"));

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

    $stmt = $conn->prepare("SELECT id, password_hash FROM admin WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($admin = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Password errata"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Utente non trovato"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Errore: " . $e->getMessage()]);
}
