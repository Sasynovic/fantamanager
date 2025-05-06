<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // reindirizza se non loggato
    exit;
}
require_once 'heading.php';
$nomeSezione = "squadra";
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
            Nuovo <?php echo $nomeSezione?>
        </button>
    </div>

    <div class="card-all">
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
            <h2 class="card-title">Aggiungi <?php echo $nomeSezione?></h2>
        </div>

        <div class="form-group">
            <label for="nome_squadra">Nome squadra</label>
            <input type="text" name="nome_squadra" placeholder="Inserisci nome squadra">
        </div>

        <div class="form-group">
            <label for="id_pres">Selezione presidente</label>
            <select id="id_pres" name="id_pres" class="form-control">
                <option value="" disabled selected>Seleziona un presidente</option>
            </select>
        </div>

        <div class="form-group">
            <label for="id_vice">Selezione presidente</label>
            <select id="id_vice" name="id_vice" class="form-control">
                <option value="NULL" selected>Nessun vice</option>
            </select>
        </div>

        <div class="form-group">
            <label for="id_stadio">Selezione uno stadio</label>
            <select id="id_stadio" name="id_stadio" class="form-control">
                <option value="" selected disabled>Seleziona uno stadio</option>
            </select>
        </div>

        <div class="form-group">
            <label for="valore_fvm">Valore fvm</label>
            <input type="text" name="valore_fvm" placeholder="Valore fvm squadra">
        </div>

        <div class="checkbox-label">
            <span>In vendita</span>
            <label class="toggle-switch">
                <input type="checkbox" name="vendita" id="vendita">
                <span class="toggle-slider"></span>
            </label>
        </div>

        <script>
            const presidentiUrl = `${window.location.protocol}//${window.location.host}/endpoint/presidenti/read.php`;
            const stadiUrl = `${window.location.protocol}//${window.location.host}/endpoint/stadio/read.php`;
            const selectP = document.getElementById('id_pres');
            const selectV = document.getElementById('id_vice');
            const selectS = document.getElementById('id_stadio');

            // Carica i presidenti
            function fetchPresidenti(select){
            fetch(presidentiUrl)
                .then(response => response.json())
                .then(data => {
                    const presidenti = data.presidenti;

                    if (Array.isArray(presidenti)) {
                        presidenti.forEach(presidente => {
                            const option = document.createElement('option');
                            option.value = presidente.id;
                            option.textContent = presidente.nome + ' ' + presidente.cognome;

                            select.appendChild(option);
                        });
                    } else {
                        console.error("Il campo 'president' non è un array:", presidenti);
                    }
                })
                .catch(error => console.error('Errore nel caricamento dei presidenti:', error));
            }
            function fetchStadi(select){
                fetch(stadiUrl)
                    .then(response => response.json())
                    .then(data => {
                        const stadi = data.stadio;

                        if (Array.isArray(stadi)) {
                            stadi.forEach(stadio => {
                                const option = document.createElement('option');
                                option.value = stadio.id;
                                option.textContent = stadio.nome_stadio;

                                select.appendChild(option);
                            });
                        } else {
                            console.error("Il campo 'stadio' non è un array:", stadi);
                        }
                    })
                    .catch(error => console.error('Errore nel caricamento degli stadi:', error));
            }

            // Carica i presidenti per il primo select
            fetchPresidenti(selectP);
            // Carica i presidenti per il secondo select
            fetchPresidenti(selectV);
            // Carica gli stadi
            fetchStadi(selectS);
        </script>

        <div class="btn-group">
            <button id="submit" class="btn btn-primary">Inserisci <?php echo $nomeSezione?></button>
            <button id="cancel-form" class="btn btn-outline">Annulla</button>
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
        crudManager = new CRUDManager('<?php echo $nomeSezione?>', 'https://barrettasalvatore.it', {
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
                    <span>ID: ${item.id}</span>
                    <span>Nome squadra: ${item.nome_squadra}</span>
                    <span>Presidente: ${item.presidente}<span>
                    <span>Vice: ${item.vicepresidente}</span>
                    <span>Stadio: ${item.stadio}</span>
                    <span>Valore fvm: ${item.fvm}</span>
                    <span>In vendita: ${item.vendita}</span>
                </div>
                <div class="card-actions">
                    <button class="btn btn-warning edit-btn" onclick="crudManager.editItem(${item.id})">
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
                //controlliamo se presidente e vice non sono uguali
                if (data.id_pres === data.id_vice) {
                    alert("Il presidente e il vice non possono essere la stessa persona.");
                    return false; // Non procedere con la creazione
                }
                // il valore fvm deve essere un numero positivo
                if (isNaN(data.valore_fvm) || data.valore_fvm <= 0) {
                    alert("Il valore fvm deve essere un numero positivo.");
                    return false; // Non procedere con la creazione
                }
                console.log('Dati prima della creazione:', data);
                // Qui è possibile validare o manipolare i dati prima dell'invio
                return true; // Procedi con la creazione
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

    // Funzione globale per editare un elemento (necessaria per i pulsanti di modifica)
    function editItem(id) {
        crudManager.editItem(id);
    }
</script>
</body>
</html>