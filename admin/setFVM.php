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
$nomeSezione = "FVM";
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

        #istr p {
            margin: 0;
            line-height: 1.6;
            font-size: 16px;
        }

        #table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-family: 'Segoe UI', sans-serif;
        }

        #table th,
        #table td {
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

        #table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        #table tr:hover {
            background-color: #eef6ff;
        }
        </style>
</head>
<body>

<div class="app-container">
    <div class="header">
        <h1>Gestione <?php echo $nomeSezione?></h1>
    </div>

    <div id="istr">
        <p>
            <strong>Istruzioni:</strong> Questa pagina ti permette di gestire i valori FVM delle squadre. Puoi salvare i valori attuali come "vecchi" o aggiornare le squadre con i valori calcolati dalle associazioni.
            <br><br>
            <strong>Passo 1</strong> – Aggiorna gli FVM dei singoli calciatori. Devi inserire il file e cliccare su Avvia aggiornamento<br>
            <strong>Passo 2</strong> – Salva come vecchi valori quelli attuali, con il primo bottone<br>
            <strong>Passo 3</strong> – Procedi al calcolo del nuovo FVM con il secondo bottone<br>

            <br>Le squadre con diminuzione FVM >= 15% verranno mostrate in tabella<br>
        </p>
    </div>

    <div class="content">
        <button id="store-fvm" class="btn btn-primary" style="background-color: orange;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Salva valori in old FVM
        </button> <br>
        <button id="update-svm" class="btn btn-primary" style="margin-top: 20px">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Aggiorna FVM squadre
        </button>
    </div>

    <input type="file" id="file-input" accept=".csv" style="margin-top: 20px;"><div id="results-container" style="margin-top: 20px; display: none;">
        <h4>Risultati del caricamento</h4>
        <div id="progress-container" style="margin-bottom: 10px;">
            <div class="progress">
                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
            <small id="progress-text">0% completato</small>
        </div>
        <div id="summary" style="margin-bottom: 15px;"></div>
        <div id="results-table-container" style="max-height: 400px; overflow-y: auto;">
            <table id="results-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>FVM</th>
                    <th>Stato</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <script>
        let csvData = []; // Variabile globale per memorizzare i dati CSV

        document.getElementById('file-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Mostra lo stato di caricamento
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
            // Verifica colonne
            const firstRow = data[0] || {};
            const hasId = Object.keys(firstRow).some(k => k.toLowerCase() === 'id');
            const hasFvm = Object.keys(firstRow).some(k => k.toLowerCase() === 'fvm');

            if (!hasId || !hasFvm) {
                document.getElementById('summary').innerHTML =
                    '<div class="alert alert-danger">Colonne "id" o "fvm" non trovate nel file CSV</div>';
                return;
            }

            // Prepara i dati
            csvData = data.map(row => {
                const idKey = Object.keys(row).find(k => k.toLowerCase() === 'id');
                const fvmKey = Object.keys(row).find(k => k.toLowerCase() === 'fvm');

                return {
                    id: row[idKey]?.trim(),
                    fvm: row[fvmKey]?.trim(),
                    status: 'pending',
                    message: ''
                };
            }).filter(item => item.id && item.fvm);

            // Mostra anteprima
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

            // Reset tabella risultati
            document.querySelector('#results-table tbody').innerHTML = '';

            // Disabilita il pulsante durante l'aggiornamento
            document.getElementById('start-update').disabled = true;

            // Funzione per processare un batch di richieste
            async function processBatch(startIndex) {
                const batchSize = 5; // Numero di richieste simultanee
                const batch = csvData.slice(startIndex, startIndex + batchSize);

                if (batch.length === 0) {
                    // Aggiornamento completato
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
                    const id = row.id;
                    const fvm = parseFloat(row.fvm.replace(',', '.'));

                    if (isNaN(fvm)) {
                        row.status = 'error';
                        row.message = 'FVM non valido';
                        console.error(`ID ${id}: FVM non valido (${row.fvm})`);
                        return Promise.resolve();
                    }

                    return fetch(`${window.location.protocol}//${window.location.host}/endpoint/calciatori/update.php?id=${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ fvm: fvm })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                row.status = 'success';
                                row.message = 'Aggiornato';
                            } else {
                                row.status = 'error';
                                row.message = data.message || 'Errore sconosciuto';
                                console.error(`ID ${id}: ${row.message}`);
                            }
                        })
                        .catch(error => {
                            row.status = 'error';
                            row.message = 'Errore di rete';
                            console.error(`ID ${id}: Errore di rete`, error);
                        })
                        .finally(() => {
                            // Mostra solo le righe con successo
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

            // Avvia il processo
            processBatch(0);
        }

        function updateResultsUI() {
            const tbody = document.querySelector('#results-table tbody');
            tbody.innerHTML = '';

            // Mostra solo i record con successo
            const successData = csvData.filter(row => row.status === 'success');

            successData.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${row.id}</td>
                <td>${row.fvm}</td>
                <td><span class="badge bg-success">Successo</span></td>
            `;
                tbody.appendChild(tr);
            });

            if (successData.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="3" class="text-center">Nessun record aggiornato ancora</td>`;
                tbody.appendChild(tr);
            }
        }
    </script>

    <style>
        .progress {
            height: 20px;
            margin-bottom: 5px;
        }
        .progress-bar {
            transition: width 0.3s;
        }
        #results-table {
            font-size: 14px;
        }
        .badge {
            font-size: 12px;
        }
    </style>
    <div id="tabella-fvm-differenze">
    </div>



</body>
</html>

<script>

    // ===== VARIABILI GLOBALI =====
    // Array per memorizzare i dati delle associazioni (FVM calcolato da associazioni)
    let datiFvmOld = [];
    // Array per memorizzare i dati delle squadre con FVM attuale
    let datiCalcolaFVM = [];

    // ===== FETCH 1: RECUPERA DATI DALLE ASSOCIAZIONI =====
    // Questo fetch calcola l'FVM totale per squadra sommando i valori dalle associazioni
    fetch(`${window.location.protocol}//${window.location.host}/endpoint/associazioni/read.php?fuori_listone=0&prelazione=0`)
        .then(response => response.json())
        .then(data => {
            const fvmPerSquadra = {};
            const costiPerSquadra = {};

            // Somma tutti i valori FVM per ogni squadra dalle associazioni
            data.associazioni.forEach(associazione => {
                const id = associazione.id_squadra;
                const fvm = parseInt(associazione.fvm) || 0;
                const costo = parseInt(associazione.costo_calciatore) || 0;

                if (!fvmPerSquadra[id]) {
                    fvmPerSquadra[id] = 0;
                }

                if(!costiPerSquadra[id]) {
                    costiPerSquadra[id] = 0;
                }

                fvmPerSquadra[id] += fvm;
                costiPerSquadra[id] += costo;
            });

            // Converti l'oggetto in array per uso successivo
            datiFvmOld = Object.entries(fvmPerSquadra).map(([id_squadra, valore_fvm]) => ({
                id_squadra: parseInt(id_squadra),
                valore_fvm,
                costo_calciatore: costiPerSquadra[id_squadra] || 0
            }));

            console.log('FVM calcolato da associazioni:', datiFvmOld);
        })
        .catch(error => console.error('Errore durante il fetch associazioni:', error));

    // ===== FETCH 2: RECUPERA DATI DALLE SQUADRE =====
    // Questo fetch legge i dati attuali delle squadre e confronta con i vecchi valori
    fetch(`${window.location.protocol}//${window.location.host}/endpoint/squadra/read.php?limit=10000`)
        .then(response => response.json())
        .then(data => {
            const fvmPerSquadra = {};

            // Raggruppa i dati per squadra (nel caso ci siano duplicati)
            data.squadra.forEach(squadra => {
                const id = squadra.id;
                const fvm = parseInt(squadra.valore_fvm) || 0;
                const nome = squadra.nome_squadra || `Squadra ${id}`;
                const fvmOld = parseInt(squadra.fvm_old) || 0;

                if (!fvmPerSquadra[id]) {
                    fvmPerSquadra[id] = {
                        id: id,
                        nome: nome,
                        valore_fvm: 0,
                        fvm_old: fvmOld
                    };
                }

                fvmPerSquadra[id].valore_fvm += fvm;
            });

            // ===== POPOLAMENTO ARRAY datiCalcolaFVM =====
            // PROBLEMA RISOLTO: Popola l'array datiCalcolaFVM per il bottone "store-fvm"
            datiCalcolaFVM = Object.values(fvmPerSquadra);

            // Calcola le differenze percentuali
            const squadreConDifferenze = Object.entries(fvmPerSquadra).map(([id, dati]) => {
                const differenza = dati.valore_fvm - dati.fvm_old;
                // Calcola percentuale di diminuzione: se old > new, percentuale positiva
                const percentuale = dati.fvm_old !== 0 ?
                    ((dati.fvm_old - dati.valore_fvm) / dati.fvm_old) * 100 : 0;

                return {
                    id: parseInt(id),
                    nome: dati.nome,
                    valore_fvm: dati.valore_fvm,
                    fvm_old: dati.fvm_old,
                    differenza: differenza,
                    percentuale: percentuale.toFixed(2)
                };
            });

            // Filtra squadre con diminuzione >= 15%
            const squadreDaMostrare = squadreConDifferenze.filter(squadra =>
                parseFloat(squadra.percentuale) >= 15
            );

            console.log('Tutte le squadre:', squadreConDifferenze);
            console.log('Squadre con diminuzione >= 15%:', squadreDaMostrare);
            console.log('Dati per store FVM:', datiCalcolaFVM);

            // Crea tabella se ci sono squadre da mostrare
            if (squadreDaMostrare.length > 0) {
                creaTabella(squadreDaMostrare);
            } else {
                console.log('Nessuna squadra ha una diminuzione >= 15%');
            }
        })
        .catch(error => console.error('Errore durante il fetch squadre:', error));

    // ===== EVENT LISTENERS PER I BOTTONI =====
    document.addEventListener('DOMContentLoaded', function() {

        // Funzione helper per gestire le risposte API
        const handleApiResponse = (data, id) => {
            if (!data.success) {
                console.error(`Errore su squadra ${id}:`, data.message || 'Nessun messaggio di errore');
                return { success: false, id, error: data.message };
            }
            console.log(`Squadra ${id} aggiornata:`, data.message);
            return { success: true, id };
        };

        const storeFvmBtn = document.getElementById('store-fvm');
        const updateSvmBtn = document.getElementById('update-svm');

        // ===== BOTTONE STORE FVM (Salva FVM attuale come vecchio) =====
        if (storeFvmBtn) {
            storeFvmBtn.addEventListener('click', async function() {
                // CONTROLLO CRITICO: Verifica che datiCalcolaFVM sia popolato
                if (!datiCalcolaFVM || datiCalcolaFVM.length === 0) {
                    alert('❌ Errore: Nessun dato disponibile per il salvataggio.\n\nAssicurati che i dati delle squadre siano stati caricati correttamente.');
                    console.error('datiCalcolaFVM è vuoto:', datiCalcolaFVM);
                    return;
                }

                if (!confirm("⚠️ Attenzione! Stai per salvare i valori FVM correnti come 'vecchi valori'.\n\nVuoi procedere?")) {
                    return;
                }

                const btnOriginalText = storeFvmBtn.textContent;
                storeFvmBtn.disabled = true;
                storeFvmBtn.textContent = "⏳ Salvataggio in corso...";
                storeFvmBtn.style.cursor = 'wait';

                try {
                    console.log('Inizio salvataggio per:', datiCalcolaFVM);

                    const results = await Promise.all(
                        datiCalcolaFVM.map(async squadra => {
                            try {
                                const response = await fetch(
                                    `${window.location.protocol}//${window.location.host}/endpoint/squadra/update.php?id=${squadra.id}`,
                                    {
                                        method: 'PUT',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ fvm_old: squadra.valore_fvm })
                                    }
                                );
                                const data = await response.json();
                                return handleApiResponse(data, squadra.id);
                            } catch (error) {
                                console.error(`Errore di rete su squadra ${squadra.id}:`, error);
                                return { success: false, id: squadra.id, error: error.message };
                            }
                        })
                    );

                    const successful = results.filter(r => r.success).length;
                    const failed = results.filter(r => !r.success).length;

                    if (failed === 0) {
                        alert(`✅ Tutte le ${successful} squadre sono state aggiornate con successo!`);
                    } else {
                        const errorList = results.filter(r => !r.success)
                            .map(r => `• Squadra ${r.id}: ${r.error || 'Errore sconosciuto'}`)
                            .join('\n');

                        alert(`⚠️ Operazione completata con errori:\n\nSuccessi: ${successful}\nErrori: ${failed}\n\nDettagli errori:\n${errorList}`);
                    }
                } catch (error) {
                    console.error('Errore critico:', error);
                    alert('❌ Si è verificato un errore durante il salvataggio. Controlla la console per i dettagli.');
                } finally {
                    // Ripristina il bottone
                    storeFvmBtn.disabled = false;
                    storeFvmBtn.textContent = btnOriginalText;
                    storeFvmBtn.style.cursor = 'pointer';

                    // Ricarica la pagina per vedere i cambiamenti
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        }

        // ===== BOTTONE UPDATE SVM (Aggiorna FVM da associazioni) =====
        if (updateSvmBtn) {
            updateSvmBtn.addEventListener('click', async function() {
                // CONTROLLO CRITICO: Verifica che datiFvmOld sia popolato
                if (!datiFvmOld || datiFvmOld.length === 0) {
                    alert('❌ Errore: Nessun dato disponibile per l\'aggiornamento.\n\nAssicurati che i dati delle associazioni siano stati caricati correttamente.');
                    console.error('datiFvmOld è vuoto:', datiFvmOld);
                    return;
                }

                if (!confirm("⚠️ Attenzione! Stai per sovrascrivere i valori FVM delle squadre.\n\nQuesta operazione è irreversibile.\n\nVuoi procedere?")) {
                    return;
                }

                const btnOriginalText = updateSvmBtn.textContent;
                updateSvmBtn.disabled = true;
                updateSvmBtn.textContent = "⏳ Aggiornamento in corso...";
                updateSvmBtn.style.cursor = 'wait';

                try {
                    console.log('Inizio aggiornamento per:', datiFvmOld);

                    const results = await Promise.all(
                        datiFvmOld.map(async squadra => {
                            try {
                                const response = await fetch(
                                    `${window.location.protocol}//${window.location.host}/endpoint/squadra/update.php?id=${squadra.id_squadra}`,
                                    {
                                        method: 'PUT',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ valore_fvm: squadra.valore_fvm })
                                    }
                                );
                                const data = await response.json();
                                return handleApiResponse(data, squadra.id_squadra);
                            } catch (error) {
                                console.error(`Errore di rete su squadra ${squadra.id_squadra}:`, error);
                                return { success: false, id: squadra.id_squadra, error: error.message };
                            }
                        })
                    );

                    const successful = results.filter(r => r.success).length;
                    const failed = results.filter(r => !r.success).length;

                    if (failed === 0) {
                        alert(`✅ Tutti i valori FVM sono stati aggiornati con successo per ${successful} squadre!`);
                    } else {
                        const errorDetails = results.filter(r => !r.success)
                            .map(r => `• Squadra ${r.id}: ${r.error || 'Errore sconosciuto'}`)
                            .join('\n');

                        alert(`⚠️ Aggiornamento completato con errori:\n\nSuccessi: ${successful}\nErrori: ${failed}\n\nDettagli errori:\n${errorDetails}`);
                    }
                } catch (error) {
                    console.error('Errore critico:', error);
                    alert('❌ Si è verificato un errore durante l\'aggiornamento. Controlla la console per i dettagli.');
                } finally {
                    // Ripristina il bottone
                    updateSvmBtn.disabled = false;
                    updateSvmBtn.textContent = btnOriginalText;
                    updateSvmBtn.style.cursor = 'pointer';

                    // Ricarica la pagina per vedere i cambiamenti
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        }
    });

    // ===== FUNZIONE PER CREARE LA TABELLA =====
    function creaTabella(squadre) {
        // Rimuovi tabella esistente se presente
        const tabellaEsistente = document.getElementById('tabella-fvm-differenze');
        if (tabellaEsistente) {
            tabellaEsistente.remove();
        }

        // Crea container per la tabella
        const container = document.createElement('div');
        container.id = 'tabella-fvm-differenze';
        container.style.margin = '20px';
        container.style.fontFamily = 'Arial, sans-serif';

        // Crea titolo
        const titolo = document.createElement('h3');
        titolo.textContent = `Squadre con diminuzione FVM >= 15% (${squadre.length} squadre)`;
        titolo.style.color = '#333';
        container.appendChild(titolo);

        // Crea tabella
        const tabella = document.createElement('table');
        tabella.style.borderCollapse = 'collapse';
        tabella.style.width = '100%';
        tabella.style.border = '1px solid #ddd';

        // Crea header
        const header = tabella.createTHead();
        const headerRow = header.insertRow();
        const colonne = ['ID', 'Nome Squadra', 'FVM Attuale', 'FVM Precedente', 'Differenza', '% Diminuzione'];

        colonne.forEach(colonna => {
            const th = document.createElement('th');
            th.textContent = colonna;
            th.style.backgroundColor = '#f2f2f2';
            th.style.padding = '10px';
            th.style.border = '1px solid #ddd';
            th.style.textAlign = 'left';
            th.style.fontWeight = 'bold';
            headerRow.appendChild(th);
        });

        // Crea body della tabella
        const tbody = tabella.createTBody();

        squadre.forEach(squadra => {
            const row = tbody.insertRow();

            // Popola le celle
            const cells = [
                squadra.id,
                squadra.nome,
                squadra.valore_fvm.toLocaleString(),
                squadra.fvm_old.toLocaleString(),
                squadra.differenza > 0 ? `+${squadra.differenza.toLocaleString()}` : squadra.differenza.toLocaleString(),
                `${squadra.percentuale}%`
            ];

            cells.forEach((content, index) => {
                const cell = row.insertCell();
                cell.textContent = content;
                cell.style.padding = '8px';
                cell.style.border = '1px solid #ddd';

                if (index >= 2) cell.style.textAlign = 'right'; // Numeri a destra
                if (index === 4) { // Colora la differenza
                    cell.style.color = squadra.differenza > 0 ? '#28a745' : '#dc3545';
                    cell.style.fontWeight = 'bold';
                }
                if (index === 5) cell.style.fontWeight = 'bold'; // Percentuale in grassetto
            });

            // Colora la riga in base alla percentuale
            if (parseFloat(squadra.percentuale) >= 50) {
                row.style.backgroundColor = '#fff3cd';
            } else if (parseFloat(squadra.percentuale) >= 25) {
                row.style.backgroundColor = '#f8f9fa';
            }
        });

        container.appendChild(tabella);
        document.body.appendChild(container);
        console.log('Tabella creata con successo!');
    }
</script>
