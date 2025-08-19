<?php
// Legge i dati inviati dal JS
$input = json_decode(file_get_contents('php://input'), true);

$action = isset($input['action']) ? htmlspecialchars($input['action']) : 'Azione sconosciuta';
$description = isset($input['description']) ? htmlspecialchars($input['description']) : 'Nessuna descrizione';

// Subject e messaggio dinamici
$subject = "🔔 Notifica: $action";
$message = $description;

$to = "barrettasalvatore@outlook.it";
$headers = "From: no-reply@example.com\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "✅ Email inviata con successo!";
} else {
    echo "❌ Errore nell'invio dell'email.";
}
?>
