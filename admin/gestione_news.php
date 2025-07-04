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
$nomeSezione = "news";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione <?php echo $nomeSezione?></title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="app-container">
    <div class="header">
        <h1>Gestione <?php echo $nomeSezione?></h1>
        <button id="toggle-add-form" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Nuova <?php echo $nomeSezione?>
        </button>
    </div>

    <div class="card-all" id="card-all">
        <div class="filter-section">
            <div class="form-group">
                <label for="filter-competizione" >Competizione</label>
                <select id="filter-competizione" name="id_competizione" class="form-control">
                    <option value="">Tutte</option>
                </select>
            </div>
            <script>
                let formFilterCompetizione = document.getElementById('filter-competizione');
                let addCompetizioneToForm = document.getElementById('id-competizione');
                    function loadCompetizioni(select){
                    let urlDiv = `${window.location.protocol}//${window.location.host}/endpoint/competizione/read.php?limit=100`;

                    fetch(urlDiv)
                        .then(response => response.json())
                        .then(data => {
                            const competizione = data.competizione;

                            if (Array.isArray(competizione)) {
                                competizione.forEach(competizione => {
                                    const option = document.createElement('option');
                                    option.value = competizione.id;
                                    option.textContent = competizione.nome_competizione;
                                    select.appendChild(option);
                                });
                            } else {
                                console.error("Il campo 'competizione' non è un array:", competizione);
                            }
                        })
                        .catch(error => console.error('Errore nel caricamento delle divisioni:', error));
                    }
                    loadCompetizioni(formFilterCompetizione);

            </script>

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
            <h2 class="card-title">Aggiungi nuova <?php echo $nomeSezione?></h2>
        </div>

        <div class="form-group">
            <label for="titolo">Titolo</label>
            <input type="text" id="titolo" name="titolo" placeholder="Inserisci il titolo della <?php echo $nomeSezione?>">
        </div>

        <div class="form-group">
            <label for="autore">Autore</label>
            <input type="text" id="autore" name="autore" placeholder="Nome dell'autore">
        </div>

        <div class="checkbox-label">
            <span>Visibile</span>
            <label class="toggle-switch">
                <input type="checkbox" name="visibile" id="visibile" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group">
            <label for="id-competizione">Competizione</label>
            <select id="id-competizione" name="id_competizione" class="form-control">
                <option value="" disabled selected>Seleziona una competizione</option>
            </select>
        </div>
        <script>
            let formCompetizione = document.getElementById('id-competizione');
            loadCompetizioni(formCompetizione);
        </script>

        <div class="form-group">
            <label for="editor-container">Contenuto</label>
            <div id="editor-container"></div>
                <input type="hidden" id="contenuto" name="contenuto">
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

        <div class="form-group">
            <label for="titolo-edit">Titolo</label>
            <input type="text" id="titolo-edit" name="titolo_edit" placeholder="Inserisci il titolo della <?php echo $nomeSezione?>">
        </div>

        <div class="form-group">
            <label for="autore-edit">Autore</label>
            <input type="text" id="autore-edit" name="autore_edit" placeholder="Nome dell'autore">
        </div>

        <div class="checkbox-label">
            <span>Visibile</span>
            <label class="toggle-switch">
                <input type="checkbox" name="visibile_edit" id="visibile-edit" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group">
            <label for="id-competizione-edit">Competizione</label>
            <select id="id-competizione-edit" name="id_competizione_edit" class="form-control">
                <option value="" disabled selected>Seleziona una competizione</option>
                <script>
                    let formCompetizioneEdit = document.getElementById('id-competizione-edit');
                    loadCompetizioni(formCompetizioneEdit);
                </script>

            </select>
        </div>

        <div class="form-group">
            <label for="editor-container-edit">Contenuto</label>
            <div id="editor-container-edit"></div>
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
        placeholder: 'Scrivi il contenuto della notizia qui...'
    });

    const quillEdit = new Quill('#editor-container-edit', {
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
        placeholder: 'Modifica il contenuto della notizia qui...'
    });

    /**
     * Implementazione per la gestione dei presidenti
     */
    let crudManager;

    document.addEventListener('DOMContentLoaded', function() {
        // Inizializza il gestore CRUD per i "presidenti"
        crudManager = new CRUDManager('<?php echo $nomeSezione?>',`${window.location.protocol}//${window.location.host}`, {
            // Override degli elementi DOM, personalizzando i nomi degli elementi
            cardList: document.getElementById('card-list'),
            filterLimit: document.getElementById('filter-card'),
            searchInput: document.getElementById('search-card'),
            searchButton: document.getElementById('search-button'),
            toggleAddForm: document.getElementById('toggle-add-form'),
            addForm: document.getElementById('add-form'),
            cancelForm: document.getElementById('cancel-form'),
            submitForm: document.getElementById('submit'),
            pagination: document.getElementById('pagination'),
            cardAll: document.querySelector('.card-all')
        }, {
            // Callback personalizzati
            renderItem: function(item) {
                const data_pub = formatDate(item.data_pubblicazione);
                // Personalizzazione del rendering di ciascun presidente
                return `
                <div class="card-meta">
                    <span>Titolo: ${item.titolo}</span>
                    <span>Contenuto: ${item.contenuto}</span>
                    <span>Autore: ${item.autore}</span>
                    <span>Competizione: ${item.nome_competizione}</span>
                    <span>Visibile: ${item.visibile ? 'Sì' : 'No'}</span>
                    <span>Data: ${data_pub}</span>


                </div>
                <div class="card-actions">
                    <button class="btn btn-warning edit-btn" onclick="editItem(${item.id})">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 6px;">
                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                        </svg>
                        Modifica
                    </button>
                    <button class="btn btn-danger" onclick="crudManager.deleteItem(${item.id})">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 6px;">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                        </svg>
                        Elimina
                    </button>
                </div>
            `;
            },
            beforeCreate: function(data) {
                data.contenuto = quill.root.innerHTML;
                document.getElementById('contenuto').value = data.contenuto;
                // Qui è possibile validare o manipolare i dati prima dell'invio
                return true; // Procedi con la creazione
            },
            afterLoad: function(items) {
            },
            afterCreate: function(response) {
                // Qui puoi gestire la risposta dopo la creazione
                window.location.reload(); // Ricarica la pagina per mostrare le modifiche
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

    function editItem(id) {
        const urlSingleNews = `${window.location.protocol}//${window.location.host}/endpoint/<?php echo $nomeSezione ?>/read.php?id=${id}`;
        fetch(urlSingleNews)
            .then(response => response.json())
            .then(data => {
                // Modifica qui: prendi il primo elemento dell'array news
                const news = data.news[0]; // Aggiungi [0] per accedere al primo elemento dell'array
                if (news) {
                    apriFormModifica(news);
                } else {
                }
            })
            .catch(error => console.error('Errore nel caricamento dell\'elemento:', error));
    }

    function apriFormModifica(dati) {
        // Popola i campi
        document.getElementById('id-edit').value = dati.id || '';
        document.getElementById('titolo-edit').value = dati.titolo || '';
        document.getElementById('autore-edit').value = dati.autore || '';
        document.getElementById('visibile-edit').checked = dati.visibile === "1" || dati.visibile === 1 || dati.visibile === true;
        document.getElementById('id-competizione-edit').value = dati.id_competizione || '';

        // Popola Quill
        quillEdit.setContents(quillEdit.clipboard.convert(dati.contenuto || ''));

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

    function updateNews() {
        const id = document.getElementById('id-edit').value;
        const titolo = document.getElementById('titolo-edit').value;
        const autore = document.getElementById('autore-edit').value;
        const visibile = document.getElementById('visibile-edit').checked ? 1 : 0;
        const id_competizione = document.getElementById('id-competizione-edit').value;
        const contenuto = quillEdit.root.innerHTML;

        const data = {
            titolo: titolo,
            contenuto: contenuto,
            autore: autore,
            id_competizione: id_competizione,
            visibile: visibile
        };

        fetch(`../endpoint/<?php echo $nomeSezione ?>/update.php?id=${id}`, {
                method: 'PUT', // o 'POST' se necessario
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)

        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                window.location.reload(); // Ricarica la pagina per mostrare le modifiche
                // Aggiorna la lista delle news o fai un redirect se necessario
            })
            .catch(error => {
                console.error('Errore:',  (error.message || error));
                alert('Si è verificato un errore durante l\'aggiornamento: ' + (error.message || error));

            });
    }

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
</script>
</body>
</html>