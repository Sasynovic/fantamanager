<?php
session_start();
$timeout = 12000;
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset(); session_destroy(); header("Location: login.php?timeout=1"); exit();
}
$_SESSION['last_activity'] = time();
require_once 'heading.php';
$nomeSezione = "Gestione Associazioni";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $nomeSezione?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <style>
        .error { color: red; }
        .success { color: green; }
        .warning { color: orange; }
        #confirmation-dialog {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .button-group {
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        button {
            padding: 8px 16px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }
        .btn-confirm {
            background-color: #4CAF50;
            color: white;
        }
        .btn-cancel {
            background-color: #f44336;
            color: white;
        }
        #column-check-results {
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
        }
        #preview-container {
            max-height: 300px;
            overflow-y: auto;
            margin: 15px 0;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .http-method {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        .http-post {
            background-color: #49cc90;
            color: white;
        }
        .http-put {
            background-color: #fca130;
            color: white;
        }
    </style>
</head>
<body>

<div class="app-container">
    <h1><?php echo $nomeSezione?></h1>

    <button style="margin: 10px" class="btn btn-primary" onclick="resetAssociazioni()">Reset associazioni</button>

    <div id="istruzioni" style="background-color:#f0f8ff; border-left:5px solid #007acc; padding:16px 20px; margin-bottom:20px; border-radius:8px; font-family:'Segoe UI', sans-serif;">
        <h3>Nota Importante</h3>
        <p>
            Prima di procedere con qualsiasi aggiornamento delle associazioni dei calciatori, <strong>effettua un backup completo</strong> del database o del file CSV delle associazioni esistente.
        </p>
        <p>
            Segui sempre questo <strong>ordine consigliato</strong> delle operazioni per evitare errori:
        <ol>
            <li><strong>Svincolo dei vecchi calciatori</strong> (impostazione a NULL della squadra) <span class="http-method http-put">PUT</span></li>
            <li><strong>Aggiunta dei nuovi calciatori</strong> <span class="http-method http-post">POST</span></li>
            <li><strong>Scambio dei calciatori tra squadre</strong> <span class="http-method http-put">PUT</span></li>
        </ol>
        </p>
        <p>
            Assicurati che il file CSV contenga le colonne corrette per l'operazione selezionata:
        <ul>
            <li><strong>Svincolo:</strong> id_squadra, id_calciatore</li>
            <li><strong>Aggiunta:</strong> id_squadra, id_calciatore, costo_calciatore</li>
            <li><strong>Scambio:</strong> id_squadra_attuale, id_squadra_nuova, id_calciatore, costo_calciatore</li>
        </ul>
        </p>
        <p>
            Il file deve essere in formato CSV con delimitatore punto e virgola (<strong>;</strong>). Controlla che le intestazioni delle colonne siano corrette e corrispondano a quelle richieste.
        </p>
    </div>

    <div>
        <label>Seleziona Operazione:</label>
        <select id="operation-select">
            <option value="unbind">Svincolo <span class="http-method http-put">PUT</span></option>
            <option value="insert">Inserisci nuovi calciatori <span class="http-method http-post">POST</span></option>
            <option value="transfer">Scambio squadre <span class="http-method http-put">PUT</span></option>
        </select>
    </div>

    <div>
        <input type="file" id="file-input" accept=".csv">
    </div>

    <div id="column-check-results"></div>

    <div id="preview-container" style="display:none;">
        <h3>Anteprima dati (prime 5 righe)</h3>
        <table id="preview-table">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="confirmation-dialog">
        <h3>Conferma Operazione</h3>
        <p id="confirmation-message">Sei sicuro di voler procedere con l'operazione?</p>
        <div class="button-group">
            <button class="btn-cancel" id="cancel-operation">Annulla</button>
            <button class="btn-confirm" id="confirm-operation">Conferma</button>
        </div>
    </div>
    <div class="overlay" id="overlay"></div>

    <div id="results-container" style="display:none;">
        <h3>Risultati</h3>
        <div id="progress-text">0% completato</div>
        <table id="results-table" border="1">
            <thead><tr><th>id_calciatore</th><th>id_squadra</th><th>Stato</th><th>Dettagli</th></tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    let csvData = [];
    let currentOperation = '';
    let columnErrors = [];

    document.getElementById('operation-select').addEventListener('change', function() {
        // Reset quando cambia l'operazione
        document.getElementById('file-input').value = '';
        document.getElementById('column-check-results').innerHTML = '';
        document.getElementById('preview-container').style.display = 'none';
        document.getElementById('results-container').style.display = 'none';
        csvData = [];
    });

    document.getElementById('file-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        currentOperation = document.getElementById('operation-select').value;
        console.log(`Operazione selezionata: ${currentOperation}`);

        Papa.parse(file, {
            delimiter: ";",
            header: true,
            skipEmptyLines: true,
            complete: function(results) {
                console.log("File CSV analizzato:", results);

                if (results.errors.length > 0) {
                    console.error("Errori nel parsing CSV:", results.errors);
                }

                csvData = results.data.filter(r => Object.keys(r).length > 0);
                console.log(`Dati estratti: ${csvData.length} righe`, csvData);

                // Mostra anteprima
                showDataPreview(results.meta.fields, csvData);

                // Controlla le colonne
                checkColumns(results.meta.fields, currentOperation);
            },
            error: function(error) {
                console.error("Errore nel parsing del file:", error);
                document.getElementById('column-check-results').innerHTML =
                    `<p class="error">Errore nel parsing del file: ${error.message}</p>`;
            }
        });
    });

    function showDataPreview(headers, data) {
        const previewContainer = document.getElementById('preview-container');
        const previewTableHead = document.querySelector('#preview-table thead');
        const previewTableBody = document.querySelector('#preview-table tbody');

        // Pulisci la tabella
        previewTableHead.innerHTML = '';
        previewTableBody.innerHTML = '';

        // Aggiungi intestazioni
        let headerRow = document.createElement('tr');
        headers.forEach(header => {
            let th = document.createElement('th');
            th.textContent = header;
            headerRow.appendChild(th);
        });
        previewTableHead.appendChild(headerRow);

        // Aggiungi prime 5 righe di dati
        for (let i = 0; i < Math.min(5, data.length); i++) {
            let row = document.createElement('tr');
            headers.forEach(header => {
                let td = document.createElement('td');
                td.textContent = data[i][header] || '';
                row.appendChild(td);
            });
            previewTableBody.appendChild(row);
        }

        previewContainer.style.display = 'block';
    }

    function checkColumns(columns, operation) {
        const resultsContainer = document.getElementById('column-check-results');
        let requiredColumns = [];
        let optionalColumns = [];

        // Definisci le colonne richieste in base all'operazione
        switch(operation) {
            case 'unbind':
                requiredColumns = ['id_squadra', 'id_calciatore'];
                break;
            case 'insert':
                requiredColumns = ['id_squadra', 'id_calciatore', 'costo_calciatore'];
                break;
            case 'transfer':
                requiredColumns = ['id_squadra_attuale', 'id_squadra_nuova', 'id_calciatore', 'costo_calciatore'];
                break;
        }

        // Controlla colonne mancanti
        const missingColumns = requiredColumns.filter(col => !columns.includes(col));

        // Controlla colonne extra (non richieste)
        const allExpectedColumns = [...requiredColumns, ...optionalColumns];
        const extraColumns = columns.filter(col => !allExpectedColumns.includes(col));

        columnErrors = [];

        if (missingColumns.length > 0) {
            columnErrors.push(`Colonne mancanti: ${missingColumns.join(', ')}`);
        }

        if (extraColumns.length > 0) {
            columnErrors.push(`Colonne non riconosciute: ${extraColumns.join(', ')}`);
        }

        // Mostra risultati del controllo
        if (columnErrors.length === 0) {
            resultsContainer.innerHTML = `<p class="success">✓ Tutte le colonne sono corrette per l'operazione "${operation}"</p>`;

            // Mostra conferma per procedere
            showConfirmationDialog();
        } else {
            let html = `<p class="error">❌ Problemi riscontrati nelle colonne:</p><ul>`;
            columnErrors.forEach(error => {
                html += `<li class="error">${error}</li>`;
            });
            html += `</ul><p class="warning">Si consiglia di correggere il file prima di procedere.</p>`;
            resultsContainer.innerHTML = html;
        }
    }

    function showConfirmationDialog() {
        const dialog = document.getElementById('confirmation-dialog');
        const overlay = document.getElementById('overlay');
        const message = document.getElementById('confirmation-message');

        message.textContent = `Sei sicuro di voler procedere con l'operazione "${currentOperation}" su ${csvData.length} record?`;

        dialog.style.display = 'block';
        overlay.style.display = 'block';

        // Gestisci click su annulla
        document.getElementById('cancel-operation').onclick = function() {
            dialog.style.display = 'none';
            overlay.style.display = 'none';
        };

        // Gestisci click su conferma
        document.getElementById('confirm-operation').onclick = function() {
            dialog.style.display = 'none';
            overlay.style.display = 'none';
            startUpdateProcess(currentOperation);
        };
    }

    function startUpdateProcess(operation) {
        console.log(`Inizio processo: ${operation} con ${csvData.length} record`);

        const tbody = document.querySelector('#results-table tbody');
        tbody.innerHTML = '';
        document.getElementById('results-container').style.display = 'block';

        let completed = 0;
        const total = csvData.length;

        csvData.forEach((row, index) => {
            let endpoint = '';
            let method = 'POST'; // Default per insert
            let payload = {};

            // Prepara endpoint, metodo e payload in base all'operazione
            switch(operation) {
                case 'insert':
                    endpoint = '../endpoint/associazioni/create.php';
                    method = 'POST';
                    payload = {
                        id_squadra: row.id_squadra,
                        id_calciatore: row.id_calciatore,
                        costo_calciatore: row.costo_calciatore
                    };
                    break;
                case 'transfer':
                    endpoint = '../endpoint/associazioni/switch.php';
                    method = 'PUT';
                    payload = {
                        id_squadra_attuale: row.id_squadra_attuale,
                        id_squadra_nuova: row.id_squadra_nuova,
                        id_calciatore: row.id_calciatore,
                        costo_calciatore: row.costo_calciatore
                    };
                    break;
                case 'unbind':
                    endpoint = '../endpoint/associazioni/release.php';
                    method = 'PUT';
                    payload = {
                        id_squadra: row.id_squadra,
                        id_calciatore: row.id_calciatore
                    };
                    break;
            }

            console.log(`Invio richiesta ${index + 1}/${total}:`, {method, payload});

            // Invia la richiesta
            fetch(endpoint, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(res => {
                    console.log(`Risposta ricevuta per riga ${index + 1}:`, res);
                    return res.json();
                })
                .then(data => {
                    console.log(`Dati elaborati per riga ${index + 1}:`, data);

                    const tr = document.createElement('tr');
                    let statusClass = data.success ? 'success' : 'error';
                    let statusText = data.success ? 'success' : 'error';
                    let details = data.message || (data.success ? 'Operazione completata' : 'Errore sconosciuto');

                    tr.innerHTML = `
                    <td>${row.id_calciatore || '-'}</td>
                    <td>${row.id_squadra || row.id_squadra_nuova || '-'}</td>
                    <td class="${statusClass}">${statusText}</td>
                    <td>${details}</td>
                `;

                    tbody.appendChild(tr);
                })
                .catch(error => {
                    console.error(`Errore nella richiesta per riga ${index + 1}:`, error);

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                    <td>${row.id_calciatore || '-'}</td>
                    <td>${row.id_squadra || row.id_squadra_nuova || '-'}</td>
                    <td class="error">error</td>
                    <td>Errore di connessione: ${error.message}</td>
                `;

                    tbody.appendChild(tr);
                })
                .finally(() => {
                    completed++;
                    const progress = Math.round((completed / total) * 100);
                    document.getElementById('progress-text').textContent = `${progress}% completato`;

                    if (completed === total) {
                        console.log("Processo completato al 100%");
                    }
                });
        });
    }

    function resetAssociazioni(){
        if (!confirm("Sei sicuro di voler resettare tutte le associazioni dei calciatori? Questa operazione non può essere annullata.")) {
            return;
        }

        fetch('../endpoint/associazioni/update.php?id=0&reset=1', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Tutte le associazioni dei calciatori sono state resettate con successo.");
            } else {
                alert("Errore nel resettare le associazioni: " + (data.message || "Errore sconosciuto."));
            }
        })
        .catch(error => {
            console.error("Errore nella richiesta di reset:", error);
            alert("Errore di connessione: " + error.message);
        });
    }
</script>
</body>
</html>