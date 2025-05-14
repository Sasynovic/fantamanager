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
        <button id="toggle-add-form" class="btn btn-primary" style="display: none">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Nuova <?php echo $nomeSezione?>
        </button>
    </div>

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

    <div class="card hidden" id="add-form">
        <div class="card-header">
            <h2 class="card-title">Modifica <?php echo $nomeSezione?></h2>
        </div>

        <div class="form-group">
            <label for="editor-container">Descrizione</label>
            <div id="editor-container"></div>
            <input type="hidden" id="descrizione" name="descrizione">
        </div>

        <div class="checkbox-label">
            <span>Stato</span>
            <label class="toggle-switch">
                <input type="checkbox" name="ufficializzata" id="ufficializzata-edit" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="btn-group">
            <button id="submit" class="btn btn-primary">📰Pubblica</button>
            <button id="cancel-form" class="btn btn-outline">Annulla</button>
        </div>
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
        </div>
    </div>

    <div id="pagination" class="pagination"></div>

</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="CRUDManager.js" defer></script>

<script>
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

    let ArrayAssociazioni = [];

    let crudManager;
    document.addEventListener('DOMContentLoaded', function() {
        // Inizializza il gestore CRUD per i "trattative"
        crudManager = new CRUDManager('<?php echo $nomeSezione?>', 'https://barrettasalvatore.it', {
            // Override degli elementi DOM, personalizzando i nomi degli elementi
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
            // Callback personalizzati
            renderItem: function(item) {
                const data = formatDate(item.data_creazione);
                const uniqueId = `operations-${item.id}`;
                const creditoId = `credito-${item.id}`;

                setTimeout(() => {
                    caricaOperazioni(item.id, uniqueId);
                    caricaCrediti(item.id, creditoId);
                }, 0); // asincrono dopo il rendering

                return `
                            <div class="card-meta">
                                <h3>ID Trattativa: ${item.id}</h3>
                                <span>Squadra 1: ${item.nome_squadra1}</span>
                                <span>Squadra 2: ${item.nome_squadra2}</span>
                                <span>Data: ${data}</span>
                                <div>Descrizione: ${item.descrizione || 'Nessuna descrizione'}</div>
                                <div>Stato: ${item.ufficializzata ? '<span style="color:green">Ufficializzata</span>' : '<span style="color:orange">In corso</span>'}</div>
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
                console.log(`Caricati ${items.length} <?php echo $nomeSezione?>`);
            }
        });

    // Inizializza i pulsanti del form
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

    // Carica i dati all'avvio
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
        // Caricamento operazioni
        fetch('../endpoint/operazioni/read.php?id_trattativa=' + id)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById(containerId);
                container.innerHTML = '';

                const operazioni = data.operazioni;
                if (Array.isArray(operazioni) && operazioni.length > 0) {
                    operazioni.forEach(op => {
                        const {
                            trattativa,
                            calciatore,
                            scambio,
                            finestra_mercato
                        } = op;

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
                // Modifica qui: prendi il primo elemento dell'array news
                const trattative = data.trattative[0]; // Aggiungi [0] per accedere al primo elemento dell'array
                if (trattative) {
                    apriFormModifica(trattative);
                } else {
                    console.error('Elemento non trovato');
                }
            })
            .catch(error => console.error('Errore nel caricamento dell\'elemento:', error));
    }

    function apriFormModifica(dati) {
        // Popola i campi
        document.getElementById('id-edit').value = dati.id || '';
        document.getElementById('ufficializzata').checked = dati.ufficializzata === "1" || dati.ufficializzata === 1 || dati.ufficializzata === true;

        // // Mostra il form di modifica
        document.getElementById('card-all').classList.add('hidden');
        document.getElementById('pagination').classList.add('hidden');
        document.getElementById('edit-form').classList.remove('hidden');
    }

    function closeFormModifica() {
        document.getElementById('card-all').classList.remove('hidden');
        document.getElementById('pagination').classList.remove('hidden');
        document.getElementById('edit-form').classList.add('hidden');
    }

    let ArrayOperazioni = [];
    let credito1= [];
    let credito2= [];

    function updateNews() {
        const id = document.getElementById('id-edit').value;
        const ufficializzata = document.getElementById('ufficializzata').checked ? 1 : 0;
        const descrizione = quill.root.innerHTML;

        const dataTrattativa = {
            ufficializzata: ufficializzata,
            descrizione: descrizione
        };

        console.log('Dati da inviare:', dataTrattativa);

        // Prima aggiorniamo la trattativa
        fetch(`../endpoint/<?php echo $nomeSezione ?>/update.php?id=${id}`, {
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
            .then(responseData => {
                console.log('Aggiornamento avvenuto con successo:', responseData);

                // Solo dopo un aggiornamento riuscito e se ufficializzata è 1, procediamo con le operazioni
                if (ufficializzata === 1) {
                    console.log('Scarico le operazioni');

                    // Definiamo ArrayOperazioni qui dentro
                    let ArrayOperazioni = [];
                    // sommiamo i crediti delle operazioni di giugno per ogni squadra
                    let credito1 = 0;
                    let credito2 = 0;

                    return fetch(`../endpoint/operazioni/read.php?id_trattativa=${id}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Errore nella risposta del server');
                            }
                            return response.json();
                        })
                        .then(data => {
                            let Operazioni = data.operazioni || [];
                            Operazioni.forEach(op => {
                                const {
                                    trattativa,
                                    calciatore,
                                    scambio
                                } = op;

                                ArrayOperazioni.push({
                                    id_associazione: parseInt(scambio.id_associazione),
                                    id_squadra: trattativa.id_squadra_r,
                                    scambiato: true,
                                    n_movimenti: calciatore.n_movimenti + 1
                                });
                            });

                            console.log('Array operazioni:', ArrayOperazioni);

                            // Uso Promise.all per attendere che tutte le chiamate di aggiornamento siano completate
                            const updatePromises = ArrayOperazioni.map(operazione => {
                                const id_associazione = operazione.id_associazione;
                                const id_squadra = operazione.id_squadra;
                                const scambiato = operazione.scambiato;
                                const n_movimenti = operazione.n_movimenti;

                                console.log('Aggiorno:', operazione);

                                return fetch(`../endpoint/associazioni/update.php?id=${id_associazione}`, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ id_squadra, scambiato, n_movimenti })
                                })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Errore nella risposta del server');
                                        }
                                        return response.json();
                                    });
                            });

                            return Promise.all(updatePromises);
                        })
                        .then(results => {
                            console.log('Tutti gli aggiornamenti completati con successo', results);
                            alert('Operazione completata con successo!');
                            window.location.reload();
                        });
                }
            })
            .catch(error => {
                console.error('Errore durante l\'operazione:', error.message || error);
                alert('Si è verificato un errore: ' + (error.message || error));
            });
    }


</script>
</body>
</html>