<?php
session_start();

// Timeout in secondi
$timeout = 120;

// Controlla se l'admin è loggato
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Se esiste il timestamp dell'ultima attività
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    if ($elapsed_time > $timeout) {
        // Timeout superato: logout
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit();
    }
}

// Aggiorna il timestamp dell'ultima attività
$_SESSION['last_activity'] = time();
require_once 'heading.php';
$nomeSezione = "Gestione FVM";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione <?php echo $nomeSezione?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="app-container">
    <div class="header">
        <h1>Gestione <?php echo $nomeSezione?></h1>
    </div>

    <div class="content">
        <button id="store-fvm" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Salva valori in old FVM
        </button>
        <button id="update-svm" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Aggiorna FVM squadre
        </button>
    </div>



</body>
</html>

<script>

    let risultato = [];

    fetch(`${window.location.protocol}//${window.location.host}/endpoint/associazioni/read.php`)
        .then(response => response.json())
        .then(data => {
            const fvmPerSquadra = {};

            data.associazioni.forEach(associazione => {
                const id = associazione.id_squadra;
                const fvm = parseInt(associazione.fvm) || 0;

                if (!fvmPerSquadra[id]) {
                    fvmPerSquadra[id] = 0;
                }

                fvmPerSquadra[id] += fvm;
            });

            // Converti in array
            risultato = Object.entries(fvmPerSquadra).map(([id_squadra, valore_fvm]) => ({
                id_squadra: parseInt(id_squadra),
                valore_fvm
            }));

            // ✅ Ora il risultato è pronto
            console.log(risultato);
        })
        .catch(error => console.error('Errore durante il fetch:', error));


    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('store-fvm').addEventListener('click', function() {
            if (confirm("Sei sicuro di voler salvare i valori in old FVM?")) {
                risultato.forEach(squadra => {
                    fetch(`${window.location.protocol}//${window.location.host}/endpoint/squadre/update.php?id=${squadra.id_squadra}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            fvm_old: squadra.valore_fvm
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Aggiornato:', squadra.id_squadra, data);
                        })
                        .catch(error => console.error('Errore:', error));
                });
            }
        });


        document.getElementById('update-svm').addEventListener('click', function() {
            if (confirm("Sei sicuro di voler aggiornare il FVM delle squadre?")) {
                risultato.forEach(squadra => {
                    fetch(`${window.location.protocol}//${window.location.host}/endpoint/squadra/update.php?id=${squadra.id_squadra}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            valore_fvm: squadra.valore_fvm
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Aggiornato:', squadra.id_squadra, data);
                        })
                        .catch(error => console.error('Errore:', error));
                });
            }
        });
</script>
