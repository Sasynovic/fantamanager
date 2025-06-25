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
$nomeSezione = "presidenti";
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
        <button id="toggle-add-form" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Nuovo presidente
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
            <h2 class="card-title">Aggiungi presidente</h2>
        </div>

        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" placeholder="Nome">
        </div>

        <div class="form-group">
            <label for="cognome">Cognome</label>
            <input type="text" id="cognome" name="cognome" placeholder="Cognome">
        </div>

        <div class="btn-group">
            <button id="submit" class="btn btn-primary">Inserisci presidente</button>
            <button id="cancel-form" class="btn btn-outline">Annulla</button>
        </div>
    </div>

    <div class="card hidden" id="edit-form">
        <div class="card-header">
            <h2 class="card-title">Modifica presidente</h2>
        </div>

        <div class="form-group">
            <input type="hidden" id="id-edit" name="id-edit">
            <label for="nome_edit">Nome</label>
            <input type="text" id="nome_edit" name="nome_edit" placeholder="Nome">
        </div>

        <div class="form-group">
            <label for="cognome_edit">Cognome</label>
            <input type="text" id="cognome_edit" name="cognome_edit" placeholder="Cognome">
        </div>

        <div class="btn-group">
            <button id="submit-edit" class="btn btn-primary" onclick="updatePresidenti()">Modifica presidente</button>
            <button id="cancel-edit-form" class="btn btn-outline" onclick="closeFormModifica()">Annulla</button>
        </div>
    </div>

    <div id="pagination" class="pagination"></div>

</div>

<script src="CRUDManager.js" defer></script>

<script>

    /**
     * Implementazione per la gestione dei presidenti
     */
    let crudManager;

    document.addEventListener('DOMContentLoaded', function() {
        // Inizializza il gestore CRUD per i "presidenti"
        crudManager = new CRUDManager('<?php echo $nomeSezione?>', urlConnections, {
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

                // Personalizzazione del rendering di ciascun presidente
                return `
                <div class="card-meta">
                    <h4>${item.nome}  ${item.cognome}</h4>
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
                // Qui è possibile validare o manipolare i dati prima dell'invio
                return true; // Procedi con la creazione
            },
            afterLoad: function(items) {
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
        const urlSingle = `${window.location.protocol}//${window.location.host}/endpoint/presidenti/read.php?id=${id}`;
        console.log((urlSingle));
        fetch(urlSingle)
            .then(response => response.json())
            .then(data => {
                // Accedi alla squadra dai dati ricevuti
                const presidente = data.presidenti[0]; // Assumendo che i dati siano restituiti in un array 'presidente'
                if (presidente) {
                    apriFormModifica(presidente);
                } else {
                    console.error('Nessuna presidente trovata con questo ID');
                }
            })
            .catch(error => console.error('Errore nel caricamento della presidente:', error));
    }

    function apriFormModifica(dati) {
        // Nascondi il form finché tutto non è pronto
        document.getElementById('edit-form').classList.add('hidden');
        document.getElementById('card-all').classList.add('hidden');
        document.getElementById('pagination').classList.add('hidden');

        // Popola i campi base
        document.getElementById('id-edit').value = dati.id || '';
        document.getElementById('nome_edit').value = dati.nome || '';
        document.getElementById('cognome_edit').value = dati.cognome || '';

        console.log(`Popolamento del form di modifica con ID: ${dati.id}, Nome: ${dati.nome}, Cognome: ${dati.cognome}`);

        // Mostra il form quando tutto è stato popolato
        document.getElementById('edit-form').classList.remove('hidden');
    }


    function closeFormModifica() {
        document.getElementById('card-all').classList.remove('hidden');
        document.getElementById('pagination').classList.remove('hidden');
        document.getElementById('edit-form').classList.add('hidden');
    }

    function updatePresidenti() {
        const id = document.getElementById('id-edit').value;
        const nome = document.getElementById('nome_edit').value;
        const cognome = document.getElementById('cognome_edit').value;

        const data = {
           nome : nome,
           cognome : cognome
        };

        fetch(`${window.location.protocol}//${window.location.host}/endpoint/presidenti/update.php?id=${id}`, {
            method: 'PUT',
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
            .then(result => {
                alert("Presidente aggiornato con successo!");
                window.location.reload(); // Ricarica la pagina per mostrare le modifiche
            })
            .catch(error => {
                console.error('Errore:', (error.message || error));
                alert('Si è verificato un errore durante l\'aggiornamento: ' + (error.message || error));
            });
    }
</script>
</body>
</html>