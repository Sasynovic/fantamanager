<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // reindirizza se non loggato
    exit;
}
require_once 'heading.php';
$nomeSezione = "trattative"
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione <?php echo $nomeSezione?></title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .collapsible {
            display: none;
            margin-top: 10px;
            padding: 10px;
            border-left: 3px solid #007bff;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 0.95rem;
            color: #333;
            transition: all 0.3s ease-in-out;
            flex-direction: column;
            gap: 10px;
        }

        #edit-form {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        #edit-form .form-group {
            margin-bottom: 15px;
        }

        #edit-form .btn-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .collapsible.visible {
            display: flex;
        }

        .btn-toggle {
            background-color: #e9ecef;
            color: #333;
            border: 1px solid #ccc;
            margin: 6px 0;
            padding: 4px 8px;
            font-size: 0.85rem;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-toggle:hover {
            background-color: #dee2e6;
        }
        .operazione{
            display: flex
        ;
            flex-direction: column;
            border-radius: 10px;
            box-shadow: 2px 1px 3px  2px var(--secondary);
            padding: 10px;
        }

        /* Stile per l'editor Quill */
        #editor-container {
            height: 200px;
            margin-bottom: 15px;
        }
        .ql-editor {
            min-height: 150px;
        }
    </style>
</head>
<body>
<div class="app-container">
    <div class="header">
        <h1>Gestione <?php echo $nomeSezione?></h1>
        <button id="download-csv" class="btn btn-primary">
            Download <?php echo $nomeSezione?>
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        document.getElementById('download-csv').addEventListener('click', function() {
            // Recupera i dati delle operazioni e dei crediti in parallelo
            Promise.all([
                fetch('https://fantamanagerpro.eu/endpoint/operazioni/read.php').then(r => r.json()),
                fetch('https://fantamanagerpro.eu/endpoint/credito/read.php').then(r => r.json())
            ])
                .then(([operazioniData, creditoData]) => {
                    // 1. Foglio TRATTATIVE
                    const trattativeUniche = [...new Set(operazioniData.operazioni.map(op => op.trattativa.id))]
                        .map(id => {
                            const op = operazioniData.operazioni.find(o => o.trattativa.id === id);
                            return {
                                "ID Trattativa": id,
                                "Squadra 1": op.trattativa.nome_squadra_1,
                                "Squadra 2": op.trattativa.nome_squadra_2,
                                "Data": op.trattativa.data_creazione,
                                "Stato": op.trattativa.ufficializzata ? "Ufficiale" : "In attesa",
                                "Descrizione": op.trattativa.descrizione,
                                "Presidente richiedente": op.trattativa.nome_presidente
                            };
                        });

                    // 2. Foglio OPERAZIONI
                    const operazioniUniche = [...new Set(operazioniData.operazioni.map(op => op.id_operazione))]
                        .map(id => {
                            const op = operazioniData.operazioni.find(o => o.id_operazione === id);
                            return {
                                "ID Operazione": id,
                                "ID Trattativa": op.trattativa.id,
                                "Calciatore": op.calciatore.nome,
                                "Cartellino": op.calciatore.cartellino,
                                "FVM": op.calciatore.fvm,
                                "Squadra cedente": op.trattativa.nome_squadra_1,
                                "Squadra acquirente": op.trattativa.nome_squadra_2,
                                "Modalità": op.scambio.metodo,
                                "Finestra": op.finestra_mercato.nome ? op.finestra_mercato.nome : "",
                                "Riscatto": op.scambio.valore_riscatto ? `${op.scambio.valore_riscatto}` : ""
                            };
                        });

                    // 3. Foglio CREDITI (dall'endpoint dedicato)
                    const creditiUnici = [];
                    const chiaviUsate = new Set();

                    creditoData.credito.forEach(cr => {
                        const chiave = `${cr.fm_nome}_${cr.nome_squadra}_${cr.credito}_${cr.id_trattativa}`;

                        if (!chiaviUsate.has(chiave)) {
                            creditiUnici.push({
                                "Finestra": cr.fm_nome,
                                "Squadra": cr.nome_squadra,
                                "Importo": cr.credito,
                                "ID Trattativa": cr.id_trattativa
                            });
                            chiaviUsate.add(chiave);
                        }
                    });

                    // Creazione file Excel
                    const wb = XLSX.utils.book_new();

                    XLSX.utils.book_append_sheet(wb,
                        XLSX.utils.json_to_sheet(trattativeUniche),
                        "Trattative");

                    XLSX.utils.book_append_sheet(wb,
                        XLSX.utils.json_to_sheet(operazioniUniche),
                        "Operazioni");

                    XLSX.utils.book_append_sheet(wb,
                        XLSX.utils.json_to_sheet(creditiUnici),
                        "Crediti");

                    XLSX.writeFile(wb, `Operazioni_${new Date().toISOString().slice(0,10)}.xlsx`);
                })
                .catch(error => {
                    console.error("Errore durante l'esportazione:", error);
                    alert("Si è verificato un errore durante l'esportazione. Controlla la console per i dettagli.");
                });
        });

        function generateSummary(trattativaId, operazioniData, creditoData) {
            // Trova la trattativa
            const trattativa = operazioniData.operazioni.find(op => op.trattativa.id == trattativaId)?.trattativa;
            if (!trattativa) return "Trattativa non trovata";

            // Filtra operazioni e crediti per la trattativa
            const operazioni = operazioniData.operazioni.filter(op => op.trattativa.id == trattativaId);
            const crediti = creditoData.credito.filter(cr => cr.id_trattativa == trattativaId);

            // Costruisci il messaggio
            let message = `📋 *Riepilogo Trattativa ${trattativaId}*\n`;
            message += `📅 ${trattativa.data_creazione}\n`;
            message += `⚽ ${trattativa.nome_squadra_1} ↔ ${trattativa.nome_squadra_2}\n`;
            message += `📌 Stato: ${trattativa.ufficializzata ? "Ufficiale" : "In attesa"}\n\n`;
            message += `*Descrizione:*\n${trattativa.descrizione}\n\n`;
            message += `*Presidente richiedente:*\n${trattativa.nome_presidente}\n\n`;

            message += `*Operazioni:*\n`;
            operazioni.forEach(op => {
                message += `Squadra cedente: ${op.trattativa.nome_squadra_1}, Squadra acquirente: ${op.trattativa.nome_squadra_2}\n`;

                message += `- ${op.calciatore.nome} (${op.scambio.metodo})`;
                if(op.finestra_mercato.nome) {
                    message += ` - Finestra: ${op.finestra_mercato.nome}`;
                }
                if (op.scambio.valore_riscatto) {
                    message += ` - Riscatto: ${op.scambio.valore_riscatto}`;
                }
                message += ` - Cartellino: ${op.calciatore.cartellino}` ;
                message += ` - FVM: ${op.calciatore.fvm}\n`;
                message += `\n`;
            });

            message += `\n*Crediti:*\n`;
            crediti.forEach(cr => {
                message += `- ${cr.nome_squadra} - ${cr.fm_nome}:  ${cr.credito}\n`;
            });

            return message;
        }

        // Nuovo pulsante per WhatsApp
        function whtsappButton(trattativaId) {
            if (!trattativaId) {
                console.error('ID trattativa mancante');
                return Promise.reject('ID trattativa mancante');
            }

            return Promise.all([
                fetch('https://fantamanagerpro.eu/endpoint/operazioni/read.php').then(r => r.json()),
                fetch('https://fantamanagerpro.eu/endpoint/credito/read.php').then(r => r.json())
            ])
                .then(([operazioniData, creditoData]) => {
                    if (!operazioniData || !creditoData) {
                        throw new Error('Dati non validi');
                    }

                    const message = generateSummary(trattativaId, operazioniData, creditoData);
                    const phoneNumber = "+393371447208";
                    const encodedMessage = encodeURIComponent(message);
                    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;

                    // Apre in una nuova finestra
                    const newWindow = window.open(whatsappUrl, '_blank');
                    if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                        throw new Error('Impossibile aprire WhatsApp. Potrebbe essere bloccato dai popup?');
                    }

                    return true;
                })
                .catch(error => {
                    console.error("Errore in whtsappButton:", error);
                    throw error;
                });
        }
    </script>

    <div class="card-all" id="card-all">
        <div class="filter-section">
            <div class="form-group">
                <label for="filter-card"><?php echo $nomeSezione?> da mostrare</label>
                <select id="filter-card">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
            </div>

            <div class="form-group">
                <label for="search-card">Cerca</label>
                <input type="text" id="search-card" placeholder="Cerca <?php echo $nomeSezione?>...">
            </div>

            <div class="form-group" style="align-self: flex-end;">
                <button id="search-button" class="btn btn-primary">Applica filtri</button>
            </div>
        </div>

        <ul id="card-list" class="card-grid"></ul>
    </div>

    <div class="card hidden" id="edit-form">
        <div class="card-header">
            <h2 class="card-title">Modifica <?php echo $nomeSezione?></h2>
        </div>

        <div class="form-group">
            <label for="id-edit"></label>
            <input type="hidden" id="id-edit" name="id_edit" readonly>
        </div>

        <div class="checkbox-label">
            <span>Ufficializza</span>
            <label class="toggle-switch">
                <input type="checkbox" name="ufficializzata" id="ufficializzata" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group">
            <label for="editor-container">Descrizione</label>
            <div id="editor-container"></div>
            <input type="hidden" id="contenuto-edit" name="contenuto_edit">
        </div>

        <div class="btn-group">
            <button id="submit-edit" class="btn btn-primary" onclick="updateNews()">📰Modifica</button>
            <button id="cancel-edit-form" class="btn btn-outline" onclick="closeFormModifica()">Annulla</button>
            <button id="btn btn-primary" class="btn btn-success" onclick="whtsappButton(document.getElementById('id-edit').value)">📱 WhatsApp</button>
        </div>
    </div>

    <div id="pagination" class="pagination"></div>

</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="CRUDManager.js" defer></script>

<script>
    let ArrayAssociazioni = [];
    let ArrayOperazioni = [];
    let crudManager;

    const quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['link'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                ['blockquote', 'code-block']
            ]
        },
        placeholder: 'Scrivi la descrizione della trattativa qui...'
    });

    document.addEventListener('DOMContentLoaded', function() {
        crudManager = new CRUDManager('<?php echo $nomeSezione?>', `${window.location.protocol}//${window.location.host}`, {
            cardList: document.getElementById('card-list'),
            filterLimit: document.getElementById('filter-card'),
            searchInput: document.getElementById('search-card'),
            searchButton: document.getElementById('search-button'),
            toggleAddForm: document.getElementById('toggle-add-form'),
            addForm: document.getElementById('add-form'),
            toggleEditForm: document.getElementById('toggle-edit-form'),
            editForm: document.getElementById('edit-form'),
            cancelForm: document.getElementById('cancel-form'),
            submitForm: document.getElementById('submit'),
            pagination: document.getElementById('pagination'),
            cardAll: document.querySelector('.card-all')
        }, {
            renderItem: function(item) {
                const data = formatDate(item.data_creazione);
                const uniqueId = `operations-${item.id}`;
                const creditoId = `credito-${item.id}`;

                setTimeout(() => {
                    caricaOperazioni(item.id, uniqueId);
                    caricaCrediti(item.id, creditoId);
                }, 0);

                return `
                    <div class="card-meta">
                        <h3>ID Trattativa: ${item.id}</h3>
                        <span>Squadra 1: ${item.nome_squadra1}</span>
                        <span>Squadra 2: ${item.nome_squadra2}</span>
                        <span>Data: ${data}</span>
                        <div>Descrizione: ${item.descrizione || 'Nessuna descrizione'}</div>
                        <div>Stato: ${item.ufficializzata ? '<span style="color:green">Ufficializzata</span>' : '<span style="color:orange">In corso</span>'}</div>
                        <div>Presidente: ${item.nome_presidente}</div>
                    </div>

                    <button class="btn btn-toggle" onclick="toggleVisibility('${uniqueId}')">Mostra/Nascondi Operazioni</button>
                    <div class="operations collapsible" id="${uniqueId}">
                        <em>Caricamento operazioni...</em>
                    </div>

                    <button class="btn btn-toggle" onclick="toggleVisibility('${creditoId}')">Mostra/Nascondi Crediti</button>
                    <div class="credito-container collapsible" id="${creditoId}">
                        <em>Caricamento crediti...</em>
                    </div>

                    <div class="card-actions">
                        <button class="btn btn-warning edit-btn" onclick="editItem(${item.id})">
                            ✏️ Modifica
                        </button>
                        <button class="btn btn-danger" onclick="crudManager.deleteItem(${item.id})">
                            🗑️ Elimina
                        </button>
                    </div>`;
            },
            afterLoad: function(items) {
            }
        });

        if (document.getElementById('toggle-add-form')) {
            document.getElementById('toggle-add-form').addEventListener('click', function() {
                crudManager.showAddForm();
            });
        }

        if (document.getElementById('cancel-form')) {
            document.getElementById('cancel-form').addEventListener('click', function(e) {
                e.preventDefault();
                crudManager.hideAddForm();
            });
        }

        crudManager.loadData();
    });

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('it-IT', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function caricaOperazioni(id, containerId) {
        fetch('../endpoint/operazioni/read.php?id_trattativa=' + id)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById(containerId);
                container.innerHTML = '';

                const operazioni = data.operazioni;
                if (Array.isArray(operazioni) && operazioni.length > 0) {
                    operazioni.forEach(op => {
                        const { trattativa, calciatore, scambio, finestra_mercato } = op;

                        const scambiatoText = calciatore.scambiato
                            ? '<b style="color: green;">Già scambiato</b>'
                            : '<b style="color: red;">Mai scambiato in questa finestra di mercato</b>';

                        const valoreRiscattoText = scambio.valore_riscatto !== null
                            ? `<span><b>Valore riscatto:</b> ${scambio.valore_riscatto} crediti</span>`
                            : '';

                        const div = document.createElement('div');
                        div.className = 'operazione';
                        div.innerHTML = `
                            <h3>Operazione ID: ${op.id_operazione}</h3>
                            <p><b>${calciatore.nome}</b> si muove dalla squadra <b>${trattativa.nome_squadra_1}</b> alla squadra <b>${trattativa.nome_squadra_2}</b></p>
                            <p><b>Movimenti totali calciatore:</b> ${calciatore.n_movimenti}</p>
                            <p>${scambiatoText}</p>
                            <p><b>Tipo operazione:</b> ${scambio.metodo + ' ' + finestra_mercato.nome}</p>
                            ${valoreRiscattoText}
                        `;
                        container.appendChild(div);
                    });
                } else {
                    container.textContent = "Nessuna operazione trovata.";
                }
            })
            .catch(err => {
                const container = document.getElementById(containerId);
                container.textContent = "Errore nel caricamento delle operazioni.";
                console.error(err);
            });
    }

    function caricaCrediti(id, containerId) {
        fetch('../endpoint/credito/read.php?id_trattativa=' + id)
            .then(response => response.json())
            .then(data => {
                const creditoContainer = document.getElementById(containerId);
                creditoContainer.innerHTML = '';

                if (Array.isArray(data.credito) && data.credito.length > 0) {
                    data.credito.forEach(credito => {
                        const div = document.createElement('div');
                        div.className = 'credito';
                        div.innerHTML = `
                            <p><b>Squadra:</b> ${credito.nome_squadra}</p>
                            <p><b>Finestra di mercato:</b> ${credito.fm_nome}</p>
                            <p><b>Importo:</b> ${credito.credito} crediti</p>
                            <hr>`;
                        creditoContainer.appendChild(div);
                    });
                } else {
                    creditoContainer.textContent = "Nessun credito registrato.";
                }
            })
            .catch(err => {
                const creditoContainer = document.getElementById(containerId);
                creditoContainer.textContent = "Errore nel caricamento dei crediti.";
                console.error(err);
            });
    }

    function toggleVisibility(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.toggle('visible');
        }
    }

    function editItem(id) {
        const urlSingleTrattativa = `${window.location.protocol}//${window.location.host}/endpoint/<?php echo $nomeSezione ?>/read.php?id=${id}`;
        fetch(urlSingleTrattativa)
            .then(response => response.json())
            .then(data => {
                const trattative = data.trattative[0];
                if (trattative) {
                    apriFormModifica(trattative);
                } else {
                    console.error('Elemento non trovato');
                }
            })
            .catch(error => console.error('Errore nel caricamento dell\'elemento:', error));
    }

    function apriFormModifica(dati) {
        document.getElementById('id-edit').value = dati.id || '';
        document.getElementById('ufficializzata').checked = dati.ufficializzata === "1" || dati.ufficializzata === 1 || dati.ufficializzata === true;
        quill.root.innerHTML = dati.descrizione || '';
        document.getElementById('card-all').classList.add('hidden');
        document.getElementById('pagination').classList.add('hidden');
        document.getElementById('edit-form').classList.remove('hidden');
    }

    function closeFormModifica() {
        document.getElementById('card-all').classList.remove('hidden');
        document.getElementById('pagination').classList.remove('hidden');
        document.getElementById('edit-form').classList.add('hidden');
    }


    function updateNews() {
        const id = document.getElementById('id-edit').value;
        const ufficializzata = document.getElementById('ufficializzata').checked ? 1 : 0;
        const descrizione = quill.root.innerHTML;

        const dataTrattativa = {
            ufficializzata: ufficializzata,
            descrizione: descrizione
        };

        // 1. Recupera i dettagli della trattativa
        fetch(`../endpoint/<?php echo $nomeSezione ?>/read.php?id=${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Controllo robusto della struttura dati
                if (!data || !data.trattative || !Array.isArray(data.trattative) || data.trattative.length === 0) {
                    throw new Error('Dati trattativa non validi o mancanti');
                }

                const trattativa = data.trattative[0];
                if (!trattativa || typeof trattativa !== 'object') {
                    throw new Error('Oggetto trattativa non valido');
                }

                const idSquadra1 = parseInt(trattativa.id_squadra1);
                const idSquadra2 = parseInt(trattativa.id_squadra2);

                if (isNaN(idSquadra1) || isNaN(idSquadra2)) {
                    throw new Error('ID squadre non validi');
                }

                // 2. Aggiorna la trattativa
                return fetch(`../endpoint/<?php echo $nomeSezione ?>/update.php?id=${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dataTrattativa)
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(() => {
                        if (ufficializzata !== 1) {
                            alert('Modifica apportata con successo!');
                            window.location.reload();
                            // return Promise.resolve();
                        }

                        // 3. Se ufficializzata, processa operazioni e crediti
                        let creditoSquadra1 = 0;
                        let creditoSquadra2 = 0;
                        let ArrayOperazioni = [];

                        // 3.1 Recupera le operazioni
                        return fetch(`../endpoint/operazioni/read.php?id_trattativa=${id}`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Errore nel fetch delle operazioni trattativa: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                // Controllo struttura dati operazioni
                                if (!data || !Array.isArray(data.operazioni)) {
                                    console.warn('Nessuna operazione trattativa trovata o formato non valido');
                                    return [];
                                }
                                return data.operazioni;
                            })
                            .then(operazioni => {
                                // Processa ogni operazione
                                operazioni.forEach(op => {
                                    try {
                                        // Controllo completo della struttura dell'operazione
                                        if (!op || !op.trattativa || !op.calciatore || !op.scambio) {
                                            console.warn('Operazione con struttura incompleta:', op);
                                            return;
                                        }

                                        const { trattativa, calciatore, scambio } = op;

                                        // Controllo valori riscatto
                                        if (scambio.valore_riscatto > 0 && scambio.id_tipologia === 13) {
                                            const valore = parseFloat(scambio.valore_riscatto) || 0;
                                            if (trattativa.id_squadra_c === idSquadra1) {
                                                creditoSquadra2 += valore;
                                            } else if (trattativa.id_squadra_c === idSquadra2) {
                                                creditoSquadra1 += valore;
                                            }
                                        }

                                        // Aggiungi all'array operazioni
                                        if (scambio.id_associazione) {
                                            ArrayOperazioni.push({
                                                id_associazione: parseInt(scambio.id_associazione),
                                                id_squadra: trattativa.id_squadra_r,
                                                scambiato: true,
                                                n_movimenti: (parseInt(calciatore.n_movimenti) || 0) + 1
                                            });
                                        }
                                    } catch (e) {
                                        console.error('Errore nel caricare operazione trattativa nell array:', op, e);
                                    }
                                });

                                // 3.2 Recupera i crediti
                                return fetch(`../endpoint/credito/read.php?id_trattativa=${id}`)
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(`Errore nel fetch crediti: ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        // Controllo completo struttura dati crediti
                                        if (!data || !Array.isArray(data.credito)) {
                                            console.warn('Nessun credito trovato o formato non valido');
                                            return;
                                        }

                                        data.credito.forEach(credito => {
                                            try {
                                                // Controllo completo struttura credito
                                                if (!credito || typeof credito !== 'object') {
                                                    console.warn('Credito con struttura non valida:', credito);
                                                    return;
                                                }

                                                if (credito.id_fm === 10) {
                                                    const creditoVal = parseFloat(credito.credito) || 0;
                                                    if (credito.id_squadra === idSquadra1) {
                                                        creditoSquadra1 += creditoVal;
                                                    } else if (credito.id_squadra === idSquadra2) {
                                                        creditoSquadra2 += creditoVal;
                                                    }
                                                }
                                            } catch (e) {
                                                console.error('Errore nel processare credito:', credito, e);
                                            }
                                        });

                                        // 3.3 Recupera i crediti attuali delle squadre
                                        const promises = [
                                            fetch(`../endpoint/squadra/read.php?id_squadra=${idSquadra1}`)
                                                .then(response => {
                                                    if (!response.ok) {
                                                        throw new Error(`Errore nel fetch squadra1: ${response.status}`);
                                                    }
                                                    return response.json();
                                                })
                                                .then(data => {
                                                    // Log the exact data structure received

                                                    if (!data || !data.squadra) {
                                                        throw new Error('Struttura dati squadra 1 non valida');
                                                    }

                                                    // Handle both array and object formats
                                                    const squadraData = Array.isArray(data.squadra) ? data.squadra[0] : data.squadra;

                                                    if (!squadraData) {
                                                        throw new Error('Dati squadra 1 non disponibili');
                                                    }

                                                    // Handle different possible structures for financial data
                                                    let creditoValue = 0;

                                                    // Case 1: finanze as array
                                                    if (squadraData.finanze && Array.isArray(squadraData.finanze) && squadraData.finanze.length > 0) {
                                                        creditoValue = parseFloat(squadraData.finanze[0].credito) || 0;
                                                    }
                                                    // Case 2: finanze as object
                                                    else if (squadraData.finanze && typeof squadraData.finanze === 'object') {
                                                        creditoValue = parseFloat(squadraData.finanze.credito) || 0;
                                                    }
                                                    // Case 3: direct credito property
                                                    else if (squadraData.credito !== undefined) {
                                                        creditoValue = parseFloat(squadraData.credito) || 0;
                                                    }
                                                    // Case 4: no finance data found, use 0 as default
                                                    else {
                                                        console.warn('Nessun dato finanziario trovato per squadra 1, usando 0 come valore predefinito');
                                                    }

                                                    return creditoValue;
                                                }),
                                            fetch(`../endpoint/squadra/read.php?id_squadra=${idSquadra2}`)
                                                .then(response => {
                                                    if (!response.ok) {
                                                        throw new Error(`Errore nel fetch squadra2: ${response.status}`);
                                                    }
                                                    return response.json();
                                                })
                                                .then(data => {
                                                    // Log the exact data structure received

                                                    if (!data || !data.squadra) {
                                                        throw new Error('Struttura dati squadra 2 non valida');
                                                    }

                                                    // Handle both array and object formats
                                                    const squadraData = Array.isArray(data.squadra) ? data.squadra[0] : data.squadra;

                                                    if (!squadraData) {
                                                        throw new Error('Dati squadra 2 non disponibili');
                                                    }

                                                    // Handle different possible structures for financial data
                                                    let creditoValue = 0;

                                                    // Case 1: finanze as array
                                                    if (squadraData.finanze && Array.isArray(squadraData.finanze) && squadraData.finanze.length > 0) {
                                                        creditoValue = parseFloat(squadraData.finanze[0].credito) || 0;
                                                    }
                                                    // Case 2: finanze as object
                                                    else if (squadraData.finanze && typeof squadraData.finanze === 'object') {
                                                        creditoValue = parseFloat(squadraData.finanze.credito) || 0;
                                                    }
                                                    // Case 3: direct credito property
                                                    else if (squadraData.credito !== undefined) {
                                                        creditoValue = parseFloat(squadraData.credito) || 0;
                                                    }
                                                    // Case 4: no finance data found, use 0 as default
                                                    else {
                                                        console.warn('Nessun dato finanziario trovato per squadra 2, usando 0 come valore predefinito');
                                                    }

                                                    return creditoValue;
                                                })
                                        ];

                                        return Promise.all(promises)

                                            // 3.4 Prepara le promesse per aggiornare i crediti delle squadre
                                            .then(([creditoAttuale1, creditoAttuale2]) => {
                                                // Calcola i nuovi valori dei crediti
                                                const nuovoCreditoSquadra1 = creditoAttuale1 - creditoSquadra1;
                                                const nuovoCreditoSquadra2 = creditoAttuale2 - creditoSquadra2;

                                                // Debug: mostra i valori per verificare
                                                console.log('Crediti attuali:', creditoAttuale1, creditoAttuale2);
                                                console.log('Nuovi crediti:', nuovoCreditoSquadra1, nuovoCreditoSquadra2);

                                                let creditPromises = [];

                                                if(nuovoCreditoSquadra1 === creditoAttuale1 && nuovoCreditoSquadra2 === creditoAttuale2) {
                                                    console.log('Nessun aggiornamento necessario per i crediti');
                                                    creditPromises = [Promise.resolve('Nessun aggiornamento necessario')];
                                                } else {
                                                    console.log('Preparazione aggiornamento crediti...');
                                                    creditPromises = [
                                                        // Aggiorna il credito della squadra 1
                                                        fetch(`../endpoint/squadra/update.php?id=${idSquadra1}`, {
                                                            method: 'PUT',
                                                            headers: { 'Content-Type': 'application/json' },
                                                            body: JSON.stringify({
                                                                credito: nuovoCreditoSquadra1
                                                            })
                                                        }).then(response => {
                                                            if (!response.ok) {
                                                                const error = new Error(`Errore nell'aggiornamento del credito della squadra ${idSquadra1}`);
                                                                error.details = {
                                                                    status: response.status,
                                                                    statusText: response.statusText,
                                                                    url: response.url
                                                                };
                                                                throw error;
                                                            }
                                                            return response.json();
                                                        }),

                                                        // Aggiorna il credito della squadra 2
                                                        fetch(`../endpoint/squadra/update.php?id=${idSquadra2}`, {
                                                            method: 'PUT',
                                                            headers: { 'Content-Type': 'application/json' },
                                                            body: JSON.stringify({
                                                                credito: nuovoCreditoSquadra2
                                                            })
                                                        }).then(response => {
                                                            if (!response.ok) {
                                                                const error = new Error(`Errore nell'aggiornamento del credito della squadra ${idSquadra2}`);
                                                                error.details = {
                                                                    status: response.status,
                                                                    statusText: response.statusText,
                                                                    url: response.url
                                                                };
                                                                throw error;
                                                            }
                                                            return response.json();
                                                        })
                                                    ];
                                                }

                                                // 3.5 Aggiorna le associazioni
                                                console.log('Preparazione aggiornamento associazioni...');
                                                const updatePromises = ArrayOperazioni.map(operazione => {
                                                    console.log(`Aggiornamento associazione ${operazione.id_associazione}`);

                                                    return fetch(`../endpoint/associazioni/update.php?id=${operazione.id_associazione}`, {
                                                        method: 'PUT',
                                                        headers: { 'Content-Type': 'application/json' },
                                                        body: JSON.stringify({
                                                            id_squadra: operazione.id_squadra
                                                            // scambiato: operazione.scambiato,
                                                            // n_movimenti: operazione.n_movimenti
                                                        })
                                                    }).then(response => {
                                                        if (!response.ok) {
                                                            const error = new Error(`Errore nell'aggiornamento associazione ${operazione.id_associazione}`);
                                                            error.details = {
                                                                status: response.status,
                                                                statusText: response.statusText,
                                                                url: response.url
                                                            };
                                                            throw error;
                                                        }
                                                        return response.json();
                                                    });
                                                });

                                                // Esegui prima gli aggiornamenti dei crediti, poi le associazioni
                                                return Promise.all(creditPromises)
                                                    .then(results => {
                                                        console.log('Risultati aggiornamento crediti:', results);
                                                        return Promise.all(updatePromises);
                                                    })
                                                    .then(results => {
                                                        console.log('Risultati aggiornamento associazioni:', results);
                                                        return { success: true, message: 'Tutte le operazioni completate' };
                                                    });
                                            })
                                            .catch(error => {
                                                console.error('Errore durante le operazioni:', {
                                                    message: error.message,
                                                    details: error.details || 'Nessun dettaglio aggiuntivo'
                                                });
                                                throw error; // Rilancia l'errore per gestirlo a livello superiore
                                            });

                                    });
                            });
                    });

            })
            .then(() => {
                alert('Operazione completata con successo!');
                try {
                    if (typeof whtsappButton === 'function') {
                        whtsappButton(id);
                        // Ritarda il reload per dare tempo a WhatsApp di aprirsi
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        console.warn('whtsappButton non è una funzione');
                        window.location.reload();
                    }
                } catch (e) {
                    console.error('Errore in whtsappButton:', e);
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Errore durante l\'operazione:', error);
                alert('Si è verificato un errore: ' + (error.message || 'Dettagli non disponibili'));
            });
    }
</script>
</body>
</html>