<?php
// Legge i dati inviati dal JS
$input = json_decode(file_get_contents('php://input'), true);

$action = isset($input['action']) ? htmlspecialchars($input['action']) : 'Azione sconosciuta';
$description = isset($input['description']) ? $input['description'] : 'Nessuna descrizione';

// Subject e messaggio dinamici
$subject = "🔔 Notifica: $action";
$message = "<html><body>$description</body></html>";

$to = "info@fantamanagerpro.eu";
$headers = "From: no-reply@example.com\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "✅ Email inviata con successo!";
} else {
    echo "❌ Errore nell'invio dell'email.";
}
