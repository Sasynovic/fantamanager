<?php
session_start();

// Timeout in secondi
$timeout = 12000;

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
        <button id="download-csv" class="btn btn-primary"> Download rose</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        document.getElementById('download-csv').addEventListener('click', function() {
            fetch('https://fantamanagerpro.eu/endpoint/associazioni/read.php?fuori_listone=0')
                .then(r => r.json())
                .then(associazioni => {
                    const datiAssociazioni = associazioni.associazioni.map(assoc => ({
                        "ID Associazione": assoc.id,
                        "ID Squadra": assoc.id_squadra,
                        "Nome Squadra": assoc.nome_squadra,
                        "Nome Calciatore": assoc.nome_calciatore,
                        "Ruolo": assoc.ruolo_calciatore,
                        "Costo": assoc.costo_calciatore,
                        "Squadra di Appartenenza": assoc.nome_squadra_calciatore,
                        "FVM": assoc.fvm,
                        "Età": assoc.eta ?? "N/D",
                        "Numero Movimenti": assoc.n_movimenti ?? "N/D",
                        "Scambiato": assoc.scambiato === null ? "No" : "Sì"
                    }));

                    const wb = XLSX.utils.book_new();

                    XLSX.utils.book_append_sheet(wb,
                        XLSX.utils.json_to_sheet(datiAssociazioni),
                        "Associazioni");

                    XLSX.writeFile(wb, `Associazioni_${new Date().toISOString().slice(0,10)}.xlsx`);
                })
                .catch(error => {
                    console.error("Errore durante l'esportazione:", error);
                    alert("Errore durante l'esportazione. Controlla la console.");
                });
        });
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
            <label for="id_vice">Selezione vice presidente</label>
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

    <div class="card hidden" id="edit-form">
        <div class="card-header">
            <h2 class="card-title">Modifica <?php echo $nomeSezione?></h2>
        </div>

        <div class="form-group">
            <label for="id-edit"></label>
            <input type="hidden" id="id-edit" name="id_edit" readonly>
        </div>

        <div class="form-group">
            <label for="nome_squadra_edit">Nome squadra</label>
            <input type="text" id="nome_squadra_edit" name="nome_squadra_edit" placeholder="Inserisci nome squadra">
        </div>

        <div class="form-group">
            <label for="id_pres_edit">Selezione presidente</label>
            <select id="id_pres_edit" name="id_pres_edit" class="form-control">
                <option value="" disabled selected>Seleziona un presidente</option>
            </select>
        </div>

        <div class="form-group">
            <label for="id_vice_edit">Selezione vice presidente</label>
            <select id="id_vice_edit" name="id_vice_edit" class="form-control">
                <option value="NULL" selected>Nessun vice</option>
            </select>
        </div>

        <div class="form-group">
            <label for="id_stadio_edit">Selezione uno stadio</label>
            <select id="id_stadio_edit" name="id_stadio_edit" class="form-control">
                <option value="" selected disabled>Seleziona uno stadio</option>
            </select>
        </div>

        <div class="checkbox-label">
            <span>In vendita</span>
            <label class="toggle-switch">
                <input type="checkbox" id="vendita_edit" name="vendita_edit">
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="btn-group">
            <button id="submit-edit" class="btn btn-primary" onclick="updateSquadra()">Modifica <?php echo $nomeSezione?></button>
            <button id="cancel-edit-form" class="btn btn-outline" onclick="closeFormModifica()">Annulla</button>
        </div>
    </div>


    <div id="pagination" class="pagination"></div>

</div>

<script src="CRUDManager.js" defer></script>
<script>
    /**
     * Implementazione per la gestione delle squadre
     */
    let crudManager;

    document.addEventListener('DOMContentLoaded', function() {
        // Inizializza il gestore CRUD per le "squadre"
        crudManager = new CRUDManager('<?php echo $nomeSezione?>', `${window.location.protocol}//${window.location.host}`, {
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
                // Personalizzazione del rendering di ciascuna squadra
                return `
                <div class="card-meta">
                    <h4>${item.nome_squadra}</h4>
                    <span>Presidente: ${item.dirigenza.presidente}<span>
                    <span>Vice: ${item.dirigenza.vicepresidente}</span>
                    <span>Stadio: ${item.stadio.nome_stadio}</span>
                    <span>In vendita: ${item.vendita}</span>
                    <span>Credito: ${item.finanze.credito}</span>
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
                //controlliamo se presidente e vice non sono uguali
                if (data.id_pres === data.id_vice) {
                    alert("Il presidente e il vice non possono essere la stessa persona.");
                    return false; // Non procedere con la creazione
                }
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
        const urlSingle = `${window.location.protocol}//${window.location.host}/endpoint/squadra/read.php?id_squadra=${id}`;
        fetch(urlSingle)
            .then(response => response.json())
            .then(data => {
                // Accedi alla squadra dai dati ricevuti
                const squadra = data.squadra[0]; // Assumendo che i dati siano restituiti in un array 'squadra'
                if (squadra) {
                    apriFormModifica(squadra);
                } else {
                    console.error('Nessuna squadra trovata con questo ID');
                }
            })
            .catch(error => console.error('Errore nel caricamento della squadra:', error));
    }

    async function apriFormModifica(dati) {
        // Nascondi il form finché tutto non è pronto
        document.getElementById('edit-form').classList.add('hidden');
        document.getElementById('card-all').classList.add('hidden');
        document.getElementById('pagination').classList.add('hidden');

        // Popola i campi base
        document.getElementById('id-edit').value = dati.id || '';
        document.getElementById('nome_squadra_edit').value = dati.nome_squadra || '';
        document.getElementById('vendita_edit').checked = dati.vendita === "1" || dati.vendita === 1 || dati.vendita === true;

        // Attendi il caricamento dei dati delle select
        await loadSelectOptions();

        // Imposta i valori nei select
        if (dati.dirigenza && dati.dirigenza.id_pres) {
            document.getElementById('id_pres_edit').value = String(dati.dirigenza.id_pres);
            console.log('Presidente:', dati.dirigenza.id_pres);
        }

        if (dati.dirigenza && dati.dirigenza.id_vice) {
            document.getElementById('id_vice_edit').value = String(dati.dirigenza.id_vice);
            console.log('Vice:', dati.dirigenza.id_vice);
        } else {
            document.getElementById('id_vice_edit').value = "NULL";
        }

        if (dati.stadio && dati.stadio.id_stadio) {
            document.getElementById('id_stadio_edit').value = String(dati.stadio.id_stadio);
            console.log('Stadio:', dati.stadio.id_stadio);
        }

        // Mostra il form quando tutto è stato popolato
        document.getElementById('edit-form').classList.remove('hidden');
    }

    function loadSelectOptions() {
        const presUrl = `${window.location.protocol}//${window.location.host}/endpoint/presidenti/read.php`;
        const stadioUrl = `${window.location.protocol}//${window.location.host}/endpoint/stadio/read.php`;

        const selectPres = document.getElementById('id_pres_edit');
        const selectVice = document.getElementById('id_vice_edit');
        const selectStadio = document.getElementById('id_stadio_edit');

        // Pulisce le opzioni esistenti (tranne la prima)
        while (selectPres.options.length > 1) selectPres.remove(1);
        while (selectVice.options.length > 1) selectVice.remove(1);
        while (selectStadio.options.length > 1) selectStadio.remove(1);

        // Restituisce una Promise che aspetta entrambi i fetch
        return Promise.all([
            fetch(presUrl)
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data.presidenti)) {
                        data.presidenti.forEach(presidente => {
                            const optionP = document.createElement('option');
                            optionP.value = presidente.id;
                            optionP.textContent = presidente.nome + ' ' + presidente.cognome;
                            selectPres.appendChild(optionP);

                            const optionV = document.createElement('option');
                            optionV.value = presidente.id;
                            optionV.textContent = presidente.nome + ' ' + presidente.cognome;
                            selectVice.appendChild(optionV);
                        });
                    }
                })
                .catch(error => console.error('Errore nel caricamento dei presidenti:', error)),

            fetch(stadioUrl)
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data.stadio)) {
                        data.stadio.forEach(stadio => {
                            const option = document.createElement('option');
                            option.value = stadio.id;
                            option.textContent = stadio.nome_stadio;
                            selectStadio.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Errore nel caricamento degli stadi:', error))
        ]);
    }


    function closeFormModifica() {
        document.getElementById('card-all').classList.remove('hidden');
        document.getElementById('pagination').classList.remove('hidden');
        document.getElementById('edit-form').classList.add('hidden');
    }

    function updateSquadra() {
        const id = document.getElementById('id-edit').value;
        const nome_squadra = document.getElementById('nome_squadra_edit').value;
        const id_pres = document.getElementById('id_pres_edit').value;
        const id_vice = document.getElementById('id_vice_edit').value === "NULL" ? null : document.getElementById('id_vice_edit').value;
        const id_stadio = document.getElementById('id_stadio_edit').value;
        const vendita = document.getElementById('vendita_edit').checked ? 1 : 0;

        // Validazione
        if (id_pres === id_vice && id_vice !== "NULL" && id_vice !== null) {
            alert("Il presidente e il vice non possono essere la stessa persona.");
            return false;
        }

        const data = {
            nome_squadra: nome_squadra,
            id_pres: id_pres,
            id_vice: id_vice,
            id_stadio: id_stadio,
            vendita: vendita
        };

        fetch(`${window.location.protocol}//${window.location.host}/endpoint/squadra/update.php?id=${id}`, {
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
                alert("Squadra aggiornata con successo!");
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