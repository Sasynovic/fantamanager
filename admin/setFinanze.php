<?php
session_start();

// Timeout in secondi
$timeout = 12000;

// Controllo accesso admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}

$_SESSION['last_activity'] = time();
require_once 'heading.php';
$nomeSezione = "Finanze Squadra";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione <?php echo $nomeSezione?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        #istr {
            background-color: #f0f8ff;
            border-left: 5px solid #007acc;
            padding: 16px 20px;
            margin-bottom: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        #istr p { margin: 0; line-height: 1.6; font-size: 16px; }

        #table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-family: 'Segoe UI', sans-serif;
        }

        #table th, #table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        #table th {
            background-color: #007acc;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }

        #table tr:nth-child(even) { background-color: #f9f9f9; }
        #table tr:hover { background-color: #eef6ff; }
    </style>
</head>
<body>

<div class="app-container">
    <div class="header">
        <h1>Gestione <?php echo $nomeSezione?></h1>
    </div>

    <div id="istr">
        <p>
            <strong>Istruzioni:</strong> Carica un file CSV contenente le colonne richieste per aggiornare le finanze delle squadre.<br><br>
            <strong>Colonne richieste:</strong></p>
        <br>
                <p>id</p>
                <p>guadagno_crediti_stadio_league</p>
                <p>guadagno_crediti_stadio_cup</p>
                <p>premi_league</p>
                <p>premi_cup</p>
                <p>prequapfiche_uefa_stadio</p>
                <p>prequapfiche_uefa_premio</p>
                <p>competizioni_uefa_stadio</p>
                <p>competizioni_uefa_premio</p>
                <p>totale_crediti_bilancio <strong>(opzionale)</strong></p>
                <p>punteggio_ranking <strong>(opzionale)</strong></p>
        <br>
        <p>Il file deve essere in formato CSV con delimitatore punto e virgola (;). Assicurati che le intestazioni delle colonne corrispondano esattamente a quelle richieste.</p>
    </div>

    <div class="content">
        <input type="file" id="file-input" accept=".csv" style="margin-top: 20px;">
        <div id="results-container" style="margin-top: 20px; display: none;">
            <h4>Risultati del caricamento</h4>
            <div id="progress-container" style="margin-bottom: 10px;">
                <div class="progress">
                    <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <small id="progress-text">0% completato</small>
            </div>
            <div id="summary"></div>
            <table id="results-table">
                <thead>
                <tr>
                    <th>ID Squadra</th>
                    <th>Totale Crediti Bilancio</th>
                    <th>Guadagno Crediti Stadio League</th>
                    <th>Guadagno Crediti Stadio Cup</th>
                    <th>Premi League</th>
                    <th>Premi Cup</th>
                    <th>Prequalifiche UEFA Stadio</th>
                    <th>Prequalifiche UEFA Premio</th>
                    <th>Competizioni UEFA Stadio</th>
                    <th>Competizioni UEFA Premio</th>
                    <th>Punteggio Ranking</th>
                    <th>Stato</th>
                </tr>

                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <script>
        let csvData = [];

        document.getElementById('file-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            document.getElementById('results-container').style.display = 'block';
            document.getElementById('summary').innerHTML = '<div class="alert alert-info">Sto processando il file CSV...</div>';

            Papa.parse(file, {
                delimiter: ";",
                header: true,
                skipEmptyLines: true,
                complete: function(results) {
                    processCSV(results.data);
                },
                error: function(error) {
                    console.error("Errore nel parsing del CSV:", error);
                    document.getElementById('summary').innerHTML =
                        '<div class="alert alert-danger">Errore durante la lettura del file CSV</div>';
                }
            });
        });

        function processCSV(data) {
            const firstRow = data[0] || {};
            const requiredCols = [
                'id',
                'guadagno_crediti_stadio_league', 'guadagno_crediti_stadio_cup',
                'premi_league', 'premi_cup',
                'prequalifiche_uefa_stadio', 'prequalifiche_uefa_premio',
                'competizioni_uefa_stadio', 'competizioni_uefa_premio'];

            const optionalCols = ['totale_crediti_bilancio', 'punteggio_ranking'];

            const missing = requiredCols.filter(col => !Object.keys(firstRow).some(k => k.toLowerCase() === col));
            if (missing.length > 0) {
                document.getElementById('summary').innerHTML =
                    `<div class="alert alert-danger">Colonne mancanti: ${missing.join(', ')}</div>`;
                return;
            }

            csvData = data.map(row => {
                const mapped = {};
                requiredCols.forEach(col => {
                    const key = Object.keys(row).find(k => k.toLowerCase() === col);
                    mapped[col] = row[key]?.trim();
                });
                mapped.status = 'pending';
                mapped.message = '';
                return mapped;
            }).filter(item => item.id);

            updateResultsUI();
            document.getElementById('summary').innerHTML =
                `<div class="alert alert-success">File CSV caricato correttamente. ${csvData.length} record trovati.</div>
         <button id="start-update" class="btn btn-primary">Avvia Aggiornamento</button>`;

            document.getElementById('start-update').addEventListener('click', startUpdateProcess);
        }

        function startUpdateProcess() {
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            let completed = 0;
            const total = csvData.length;
            document.getElementById('start-update').disabled = true;

            async function processBatch(startIndex) {
                const batchSize = 5;
                const batch = csvData.slice(startIndex, startIndex + batchSize);

                if (batch.length === 0) {
                    const successCount = csvData.filter(row => row.status === 'success').length;
                    const errorCount = total - successCount;
                    let summaryText = `<div class="alert alert-success mt-2">
            Aggiornamento completato! ${successCount} record aggiornati con successo.`;
                    if (errorCount > 0) {
                        summaryText += `<br>${errorCount} record non aggiornati (controlla la console per i dettagli).`;
                    }
                    summaryText += `</div>`;
                    document.getElementById('summary').innerHTML += summaryText;
                    return;
                }

                const promises = batch.map(row => {
                    // Prepara il payload
                    const payload = {};
                    Object.keys(row).forEach(k => {
                        if (k !== 'status' && k !== 'message') {
                            const num = parseFloat(row[k].replace(',', '.'));
                            payload[k] = isNaN(num) ? row[k] : num;
                        }
                    });

                    return fetch(`${window.location.protocol}//${window.location.host}/endpoint/finanze_squadra/update.php?id=${row.id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                row.status = 'success';
                                row.message = 'Aggiornato';
                            } else {
                                row.status = 'error';
                                row.message = data.message || 'Errore sconosciuto';
                                console.error(`ID ${row.id}: ${row.message}`);
                            }
                        })
                        .catch(error => {
                            row.status = 'error';
                            row.message = 'Errore di rete';
                            console.error(`ID ${row.id}: Errore di rete`, error);
                        })
                        .finally(() => {
                            if (row.status === 'success') {
                                updateResultsUI();
                            }
                            completed++;
                            const progress = Math.round((completed / total) * 100);
                            progressBar.style.width = `${progress}%`;
                            progressText.textContent = `${progress}% completato (${completed}/${total})`;
                        });
                });

                await Promise.all(promises);
                processBatch(startIndex + batchSize);
            }

            processBatch(0);
        }

        function updateResultsUI() {
            const tbody = document.querySelector('#results-table tbody');
            tbody.innerHTML = '';
            const successData = csvData.filter(row => row.status === 'success');

            successData.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
            <td>${row.id}</td>
            <td>${row.totale_crediti_bilancio || '-'}</td>
            <td>${row.guadagno_crediti_stadio_league}</td>
            <td>${row.guadagno_crediti_stadio_cup}</td>
            <td>${row.premi_league}</td>
            <td>${row.premi_cup}</td>
            <td>${row.prequalifiche_uefa_stadio}</td>
            <td>${row.prequalifiche_uefa_premio}</td>
            <td>${row.competizioni_uefa_stadio}</td>
            <td>${row.competizioni_uefa_premio}</td>
            <td>${row.punteggio_ranking || '-'}</td>
            <td><span class="badge bg-success">Successo</span></td>
        `;
                tbody.appendChild(tr);
            });

            if (successData.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="12" class="text-center">Nessun record aggiornato ancora</td>`;
                tbody.appendChild(tr);
            }
        }

    </script>

</body>
</html>
