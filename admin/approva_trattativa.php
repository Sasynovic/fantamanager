<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // reindirizza se non loggato
    exit;
}
require_once 'heading.php';
$nomeSezione = "trattative"
?>

<div class="app-container">
    <div class="header">
        <h1>Gestione <?php echo $nomeSezione?></h1>
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

        <div class="btn-group">
            <button id="submit" class="btn btn-primary">Inserisci <?php echo $nomeSezione?></button>
            <button id="cancel-form" class="btn btn-outline">Annulla</button>
        </div>
    </div>

    <div id="pagination" class="pagination"></div>

</div>

<script src="CRUDManager.js" defer></script>

<script>
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
                const data = formatDate(item.data_creazione);
                const uniqueId = `operations-${item.id}`;

                setTimeout(() => caricaOperazioni(item.id, uniqueId), 0); // asincrono dopo il rendering


                // Personalizzazione del rendering di ciascun presidente
                return ` <div class="card-meta">
                                <h3>ID Tratttiva: ${item.id}</h3>
                                <span>Squadra 1: ${item.nome_squadra1}</span>
                                <span>Squadra 2: ${item.nome_squadra2}</span>
                                <span>Data: ${data}</span>
                            </div>
                            <div class="operations" id="${uniqueId}">
                                <em>Caricamento operazioni...</em>
                            </div><div class="credito-container" id="credito-container">
                                <em>Caricamento crediti...</em>
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
                </div>`;


            },
            beforeCreate: function(data) {
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
                if (Array.isArray(operazioni)) {
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

        // Caricamento crediti
        fetch('../endpoint/credito/read.php?id_trattativa=' + id)
            .then(response => response.json())
            .then(data => {
                const creditoContainer = document.getElementById('credito-container');
                creditoContainer.innerHTML = '';

                if (Array.isArray(data.credito) && data.credito.length > 0) {
                    const creditiPerSquadra = {};

                    data.credito.forEach(row => {
                        if (!creditiPerSquadra[row.nome_squadra]) {
                            creditiPerSquadra[row.nome_squadra] = [];
                        }
                        creditiPerSquadra[row.nome_squadra].push({
                            finestra: row.fm_nome,
                            credito: row.credito
                        });
                    });

                    for (const [squadra, crediti] of Object.entries(creditiPerSquadra)) {
                        const div = document.createElement('div');
                        div.className = 'blocco-credito';
                        div.innerHTML = `<h4>${squadra}</h4>`;

                        crediti.forEach(c => {
                            div.innerHTML += `<p><b>${c.finestra}:</b> ${c.credito} crediti</p>`;
                        });

                        creditoContainer.appendChild(div);
                    }
                } else {
                    creditoContainer.textContent = "Nessun credito registrato per questa trattativa.";
                }
            })
            .catch(err => {
                console.error(err);
                const creditoContainer = document.getElementById('credito-container');
                creditoContainer.textContent = "Errore nel caricamento dei crediti.";
            });
    }


</script>
