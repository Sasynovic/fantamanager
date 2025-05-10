<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fantacalcio Manageriale</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="renderFooter.js" defer></script>
    <style>
        .select-container{
            display: flex;
            justify-content: space-evenly;
        }
        .giocatori-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            padding: 10px;
        }

        .giocatore-itemP,.giocatore-itemD, .giocatore-itemC, .giocatore-itemA {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
            transition: background-color 0.2s ease-in-out;
        }
        .giocatore-itemP {
            background-color: #f8ab29;
        }
        .giocatore-itemD {
            background-color: #63c623;
        }
        .giocatore-itemC {
            background-color: #2e6be6;
        }
        .giocatore-itemA {
            background-color: #f21a3c;
        }

        .data-prestito-container select, .data-credito-container select {
            width: 100%;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .data-input {
            width: 100%;
            padding: 6px;
            margin-top: 4px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        /* Nuovi stili per gli elementi di scambio */
        .player-select-container {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .team-container {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            height: 100%;
        }

        .team-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .player-select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .selected-players-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .selected-player {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .selected-player-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 8px;
            border-radius: 5px 5px 0 0;
        }

        .selected-playerP .selected-player-info {
            background-color: #f8ab29;
        }

        .selected-playerD .selected-player-info {
            background-color: #63c623;
        }

        .selected-playerC .selected-player-info {
            background-color: #2e6be6;
        }

        .selected-playerA .selected-player-info {
            background-color: #f21a3c;
        }

        .prestito-select-container {
            padding: 5px;
            border-radius: 0 0 5px 5px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-top: none;
        }

        .prestito-select-container select {
            width: 100%;
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        .remove-player {
            background: none;
            border: none;
            color: #333;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }
        .main-body{
            display: flex;
            justify-content: center;
        }
        @media (max-width: 768px) {
            .player-select-container {
                flex-direction: column;
                gap: 10px;
            }
            .team-container {
                width: 100%;
            }
        }

        .credito-container {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            gap: 20px;
            flex-wrap: wrap;
        }

        .riscatto-box{
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
            color: var(--blu-scurissimo);
        }

        .credito-box {
            flex: 1;
            min-width: 200px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .credito-box input,
        .credito-box select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .finalizza-container {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
        }

        .finalizza-button {
            background-color: #2e6be6;
            color: white;
            font-size: 16px;
            font-weight: bold;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .finalizza-button:hover {
            background-color: #1c4cad;
        }

        .risultato-trattativa {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            display: none;
        }

        .success {
            background-color: #dff0d8;
            border-color: #d6e9c6;
            color: #3c763d;
        }

        .error {
            background-color: #f2dede;
            border-color: #ebccd1;
            color: #a94442;
        }

    </style>
</head>

<body>
<div class="main-container">
    <aside class="main-menu">
        <div class="menu-header">
            <h1>Fantacalcio</h1>
            <h3>Manageriale</h3>
        </div>

        <ul class="menu-list">
            <li class="menu-item">
                <a href="index.php">Dashboard</a>
            </li>
            <li class="menu-item">
                <a href="albo.php">Albo d'oro</a>
            </li>
            <li class="menu-item">
                <a href="index.php">Squadre in vendita</a>
            </li>
            <li class="menu-item">
                <a href="tool.php">Tool scambi</a>
            </li>
            <li class="menu-item">
                <a href="index.php">Regolamento</a>
            </li>
            <li class="menu-item">
                <a href="index.php">Ricerca</a>
            </li>
            <li class="menu-item">
                <a href="index.php">News</a>
            </li>
            <li class="menu-item">
                <a href="index.php">Contatti</a>
            </li>
        </ul>
    </aside>

    <div class="main-content">
        <header class="main-header">
            <div class="main-text-header">
                <h1>Tool Scambi</h1>
                <a href="admin/login.php">Admin</a>
            </div>
            <div class="header-content">
                <p>Verifica la fattibilita' dello scambio</p>
            </div>
        </header>


        <div class="main-body">
            <div class="main-body-content" id="main-body-content" style="position: relative;">

                <div class="tool-container">

                <div class="select-container">
                    <select id="selectDivisione" name="selectDivisione" class="player-select">
                        <option value="" disabled selected>Seleziona una divisione</option>
                    </select>

                    <select id="selectCompetizione" name="selectCompetizione" class="player-select">
                        <option value="" disabled selected>Seleziona una competizione</option>
                    </select>

                    <select id="selectSquadra1" name="selectSquadra1" class="player-select">
                        <option value="" disabled selected>Seleziona squadra 1</option>
                    </select>

                    <select id="selectSquadra2" name="selectSquadra2" class="player-select">
                        <option value="" disabled selected>Seleziona squadra 2</option>
                    </select>
                </div>

                <!-- Area per la selezione e visualizzazione dei calciatori -->
                <div class="player-select-container">
                    <!-- Squadra 1 -->
                    <div class="team-container">
                        <div class="team-header" id="team1-name">Squadra 1</div>
                        <select id="playerSelect1" class="player-select">
                            <option value="" disabled selected>Seleziona un calciatore</option>
                        </select>
                        <div class="selected-players-title">Calciatori selezionati:</div>
                        <div id="selectedPlayers1" class="selected-players-container">
                            <!-- I calciatori selezionati verranno visualizzati qui -->
                        </div>
                    </div>

                    <!-- Squadra 2 -->
                    <div class="team-container">
                        <div class="team-header" id="team2-name">Squadra 2</div>
                        <select id="playerSelect2" class="player-select">
                            <option value="" disabled selected>Seleziona un calciatore</option>
                        </select>
                        <div class="selected-players-title">Calciatori selezionati:</div>
                        <div id="selectedPlayers2" class="selected-players-container">
                            <!-- I calciatori selezionati verranno visualizzati qui -->
                        </div>
                    </div>
                </div>

                <div class="credito-container">
                    <div class="credito-box">
                        <input min="0" type="number" id="creditoTeam1" placeholder="Credito Team 1" style="color: var(--blu-scurissimo)">
                        <select id="quandoCreditoTeam1">
                            <option value="" disabled selected>Quando</option>
                            <option value="10">Subito</option>
                            <option value="8">Metà stagione</option>
                            <option value="9">Fine stagione</option>
                        </select>
                    </div>

                    <div class="credito-box">
                        <input  min="0" type="number" id="creditoTeam2" placeholder="Credito Team 2" style="color: var(--blu-scurissimo)">
                        <select id="quandoCreditoTeam2">
                            <option value="" disabled selected>Quando</option>
                            <option value="10">Subito</option>
                            <option value="8">Metà stagione</option>
                            <option value="9">Fine stagione</option>
                        </select>
                    </div>
                </div>

                <div class="finalizza-container">
                    <button id="finalizzaTrattativa" class="finalizza-button">Finalizza Trattativa</button>
                    <div id="risultatoTrattativa" class="risultato-trattativa"></div>
                </div>
            </div>
        </div>
            </div>

        <footer class="main-footer">
            <div class="swiper-container footer-swiper">
                <div class="swiper-wrapper" id="footerList">
                    <!-- Gli elementi division-ball verranno inseriti qui tramite JavaScript -->
                </div>
                <!-- Aggiunti i pulsanti di navigazione -->
                <div class="swiper-button-prev footer-nav-prev">
                    <img src="chevronL.svg" alt="Indietro">
                </div>
                <div class="swiper-button-next footer-nav-next">
                    <img src="chevronR.svg" alt="Avanti">
                </div>
            </div>
        </footer>
    </div>
</div>

<script>
    /// DOM Elements
    const selects = {
        divisione: document.getElementById('selectDivisione'),
        competizione: document.getElementById('selectCompetizione'),
        squadra1: document.getElementById('selectSquadra1'),
        squadra2: document.getElementById('selectSquadra2'),
        player1: document.getElementById('playerSelect1'),
        player2: document.getElementById('playerSelect2')
    };

    const containers = {
        selected1: document.getElementById('selectedPlayers1'),
        selected2: document.getElementById('selectedPlayers2'),
        team1: document.getElementById('team1-name'),
        team2: document.getElementById('team2-name')
    };

    let giocatori = { squadra1: [], squadra2: [] };
    let selectedPlayers = { squadra1: new Set(), squadra2: new Set() };
    let selectedArrays = { squadra1: [], squadra2: [] };
    let tipologieScambio = []; // Memorizza le tipologie di scambio
    let finestreMercato = {}; // Memorizza le finestre di mercato per ogni tipologia

    // Utils
    const fetchData = (url) => fetch(url).then(res => res.json());

    const populateSelect = (select, items, valueKey, textKey) => {
        select.innerHTML = `<option value="" disabled selected>Seleziona</option>`;
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[valueKey];
            opt.textContent = item[textKey];
            select.appendChild(opt);
        });
    };

    const resetPlayerSelections = (squadra = null) => {
        if (!squadra || squadra === 'squadra1') {
            selects.player1.innerHTML = '';
            containers.selected1.innerHTML = '';
            giocatori.squadra1 = [];
            selectedPlayers.squadra1.clear();
            selectedArrays.squadra1 = [];
        }
        if (!squadra || squadra === 'squadra2') {
            selects.player2.innerHTML = '';
            containers.selected2.innerHTML = '';
            giocatori.squadra2 = [];
            selectedPlayers.squadra2.clear();
            selectedArrays.squadra2 = [];
        }
    };

    // Carica le tipologie di scambio
    fetchData('endpoint/tipologia_scambio/read.php')
        .then(data => {
            if (data && data.tipologia_scambio) {
                tipologieScambio = data.tipologia_scambio;

                // Organizza le finestre di mercato per tipo di prestito
                tipologieScambio.forEach(tipo => {
                    if (tipo.nome_metodo === "Prestito" && tipo.id_finestra_mercato) {
                        // Se non esiste ancora un array per questa tipologia, crealo
                        if (!finestreMercato["Prestito"]) {
                            finestreMercato["Prestito"] = [];
                        }
                        // Aggiungi questa finestra mercato all'array della tipologia
                        finestreMercato["Prestito"].push({
                            id: tipo.id_finestra_mercato,
                            nome: tipo.finestra_mercato.nome,
                            data_inizio: tipo.finestra_mercato.data_inizio,
                            data_fine: tipo.finestra_mercato.data_fine
                        });
                    }
                    // Aggiungi altre tipologie se necessario
                });

                console.log("Tipologie di scambio caricate:", tipologieScambio);
                console.log("Finestre di mercato organizzate:", finestreMercato);
            }
        })
        .catch(error => {
            console.error("Errore nel caricamento delle tipologie di scambio:", error);
        });

    // Load Divisioni on page load
    fetchData('endpoint/divisione/read.php')
        .then(data => {
            const divisioni = Array.isArray(data) ? data : data.divisioni;
            if (Array.isArray(divisioni)) {
                populateSelect(selects.divisione, divisioni, 'id', 'nome_divisione');
            }
        });

    selects.divisione.addEventListener('change', () => {
        resetPlayerSelections();
        populateSelect(selects.competizione, [], '', '');
        populateSelect(selects.squadra1, [], '', '');
        populateSelect(selects.squadra2, [], '', '');

        if (!selects.divisione.value) return;

        fetchData(`endpoint/competizione/read.php?id_divisione=${selects.divisione.value}`)
            .then(data => {
                const competizioni = Array.isArray(data) ? data : data.competizione;
                competizioni.sort((a, b) => a.nome_competizione.localeCompare(b.nome_competizione));
                populateSelect(selects.competizione, competizioni, 'id', 'nome_competizione');
            });
    });

    selects.competizione.addEventListener('change', () => {
        resetPlayerSelections();
        populateSelect(selects.squadra1, [], '', '');
        populateSelect(selects.squadra2, [], '', '');

        if (!selects.competizione.value) return;

        fetchData(`endpoint/partecipazione/read.php?id_competizione=${selects.competizione.value}`)
            .then(data => {
                const squadre = data.squadre;
                squadre.sort((a, b) => a.nome_squadra.localeCompare(b.nome_squadra));
                populateSelect(selects.squadra1, squadre, 'id', 'nome_squadra');
                populateSelect(selects.squadra2, squadre, 'id', 'nome_squadra');
            });
    });

    const handleSquadraChange = (squadraSelezionata, squadraOpposta, teamLabel, playerSelectKey) => {
        const id = selects[squadraSelezionata].value;
        resetPlayerSelections(squadraSelezionata);

        Array.from(selects[squadraOpposta].options).forEach(opt => opt.disabled = false);
        if (id) {
            const optToDisable = selects[squadraOpposta].querySelector(`option[value="${id}"]`);
            if (optToDisable) optToDisable.disabled = true;
            loadPlayers(id, squadraSelezionata, containers[teamLabel], selects[playerSelectKey]);
        }
    };

    selects.squadra1.addEventListener('change', () => {
        handleSquadraChange('squadra1', 'squadra2', 'team1', 'player1');
    });

    selects.squadra2.addEventListener('change', () => {
        handleSquadraChange('squadra2', 'squadra1', 'team2', 'player2');
    });

    const loadPlayers = (id, squadraKey, teamNameContainer, playerSelect) => {
        fetchData(`endpoint/associazioni/read.php?id_squadra=${id}`)
            .then(data => {
                const lista = data.associazioni || [];
                giocatori[squadraKey] = lista;

                if (lista.length > 0) {
                    teamNameContainer.textContent = lista[0].nome_squadra || `Squadra ${squadraKey === 'squadra1' ? '1' : '2'}`;
                }

                playerSelect.innerHTML = '<option value="" disabled selected>Seleziona un calciatore</option>';
                lista.sort((a, b) => {
                    const ordine = { 'P': 1, 'D': 2, 'C': 3, 'A': 4 };
                    return ordine[a.ruolo_calciatore] - ordine[b.ruolo_calciatore] || a.nome_calciatore.localeCompare(b.nome_calciatore);
                }).forEach(giocatore => {
                    const opt = document.createElement('option');
                    opt.value = giocatore.id;
                    opt.textContent = `${giocatore.nome_calciatore} (${giocatore.ruolo_calciatore}) - FVM: ${giocatore.fvm} - Costo: ${giocatore.costo_calciatore}`;
                    opt.className = 'giocatore-item' + giocatore.ruolo_calciatore;
                    opt.dataset.fvm = giocatore.fvm;
                    playerSelect.appendChild(opt);
                });
            });
    };

    // Funzione per creare il select di tipo trasferimento usando i dati dall'API
    const createTransferTypeSelect = (playerId) => {
        const typeSelect = document.createElement('select');
        typeSelect.className = 'player-select';
        typeSelect.id = `type-select-${playerId}`;

        // Prima opzione di default
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Seleziona tipo trasferimento';
        defaultOption.disabled = true;
        defaultOption.selected = true;
        typeSelect.appendChild(defaultOption);

        // Ottieni metodi unici (nome_metodo) e i loro ID
        const metodiUnici = [];
        const metodoToId = {};

        tipologieScambio.forEach(tipo => {
            if (!metodiUnici.includes(tipo.nome_metodo)) {
                metodiUnici.push(tipo.nome_metodo);
                metodoToId[tipo.nome_metodo] = tipo.id_metodo;
            }
        });

        // Per ogni metodo unico, crea un'opzione
        metodiUnici.forEach(metodo => {
            const option = document.createElement('option');
            option.value = metodoToId[metodo];
            option.textContent = metodo;
            typeSelect.appendChild(option);
        });

        typeSelect.addEventListener('change', () => toggleDataPrestito(typeSelect, playerId));
        return typeSelect;
    };

    // Funzione per creare il select della data prestito usando i dati dall'API
    const createPrestitoDateSelect = (playerId) => {
        const select = document.createElement('select');
        select.className = 'player-select';
        select.id = `data-fine-prestito-${playerId}`;

        // Opzione di default
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Seleziona data fine prestito';
        defaultOption.disabled = true;
        defaultOption.selected = true;
        select.appendChild(defaultOption);

        // Se abbiamo finestre di mercato per il prestito, aggiungiamole
        if (finestreMercato["Prestito"] && finestreMercato["Prestito"].length > 0) {
            finestreMercato["Prestito"].forEach(finestra => {
                const option = document.createElement('option');
                option.value = finestra.id;
                option.textContent = finestra.nome;
                select.appendChild(option);
            });
        }

        return select;
    };

    // Modify the createPlayerElement function to append the inputRiscatto to the DOM
    const createPlayerElement = (player, squadraKey, playerSelect, selectedContainer) => {
        selectedPlayers[squadraKey].add(player.id);
        selectedArrays[squadraKey].push({ ...player });

        // Crea elemento principale
        const el = document.createElement('div');
        el.className = `selected-player selected-player${player.ruolo_calciatore}`;
        el.dataset.fvm = player.fvm;

        // Informazioni del giocatore
        const playerInfo = document.createElement('div');
        playerInfo.className = 'selected-player-info';
        playerInfo.innerHTML = `
    ${player.nome_calciatore} (${player.ruolo_calciatore}) - FVM: ${player.fvm} - Costo: ${player.costo_calciatore}
    <button type="button" data-id="${player.id}" class="remove-player">X</button>
`;

        // Container per il select del tipo di trasferimento
        const prestitoContainer = document.createElement('div');
        prestitoContainer.className = 'prestito-select-container';

        // Select per il tipo di trasferimento (usando i dati dall'API)
        const typeSelect = createTransferTypeSelect(player.id);
        prestitoContainer.appendChild(typeSelect);

        // Container per la data prestito
        const dataPrestitoContainer = document.createElement('div');
        dataPrestitoContainer.id = `data-prestito-${player.id}`;
        dataPrestitoContainer.className = 'data-prestito-container';
        dataPrestitoContainer.style.display = 'none';

        // Select per la data di fine prestito (usando i dati dall'API)
        const dataPrestitoSelect = createPrestitoDateSelect(player.id);
        dataPrestitoContainer.appendChild(dataPrestitoSelect);
        prestitoContainer.appendChild(dataPrestitoContainer);

        // Container per la data credito
        const dataCreditoContainer = document.createElement('div');
        dataCreditoContainer.id = `data-credito-${player.id}`;
        dataCreditoContainer.className = 'data-credito-container';
        dataCreditoContainer.style.display = 'none';

        const dataCreditoSelect = document.createElement('select');
        dataCreditoSelect.className = 'player-select';
        dataCreditoSelect.id = `data-fine-credito-${player.id}`;

        // Crea input per riscatto
        const inputRiscatto = document.createElement('input');
        inputRiscatto.type = 'number';
        inputRiscatto.min = 0;
        inputRiscatto.className = 'riscatto-box';
        inputRiscatto.placeholder = 'Riscatto';
        inputRiscatto.id = `riscatto-input-${player.id}`; // Make sure the ID format matches what you look for later
        inputRiscatto.style.display = 'none';

        const creditoOptions = [
            {value: '', text: 'Seleziona data di credito', disabled: true, selected: true},
            {value: '8', text: 'Metà stagione'},
            {value: '9', text: 'Fine stagione'}
        ];

        creditoOptions.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.text;
            if (opt.disabled) option.disabled = true;
            if (opt.selected) option.selected = true;
            dataCreditoSelect.appendChild(option);
        });

        dataCreditoContainer.appendChild(dataCreditoSelect);
        dataCreditoContainer.appendChild(inputRiscatto);
        prestitoContainer.appendChild(dataCreditoContainer);

        // Assembla tutto
        el.appendChild(playerInfo);
        el.appendChild(prestitoContainer);
        selectedContainer.appendChild(el);

        // Disabilita l'opzione selezionata
        const opt = playerSelect.querySelector(`option[value="${player.id}"]`);
        if (opt) opt.disabled = true;
    };

    const setupPlayerSelectHandler = (playerSelect, squadraKey, selectedContainer) => {
        playerSelect.addEventListener('change', function () {
            const selectedId = this.value;
            const playerList = giocatori[squadraKey];
            const player = playerList.find(g => g.id == selectedId);

            if (player && !selectedPlayers[squadraKey].has(player.id)) {
                createPlayerElement(player, squadraKey, playerSelect, selectedContainer);
            }

            this.selectedIndex = 0;
        });

        // Listener per la rimozione del giocatore
        selectedContainer.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-player')) {
                const idToRemove = parseInt(e.target.dataset.id);
                selectedPlayers[squadraKey].delete(idToRemove);
                selectedArrays[squadraKey] = selectedArrays[squadraKey].filter(p => p.id !== idToRemove);

                // Rimuove il div dal DOM
                e.target.closest('.selected-player').remove();

                // Riabilita l'opzione nel select
                const opt = playerSelect.querySelector(`option[value="${idToRemove}"]`);
                if (opt) opt.disabled = false;
            }
        });
    };

    // Make sure the toggleDataPrestito function handles null elements gracefully
    window.toggleDataPrestito = function(selectElement, playerId) {
        const dataPrestito = document.getElementById(`data-prestito-${playerId}`);
        const dataCredito = document.getElementById(`data-credito-${playerId}`);
        const inputRiscatto = document.getElementById(`riscatto-input-${playerId}`);
        const selectedValue = selectElement.value;
        const selectedText = selectElement.options[selectElement.selectedIndex].text;

        // Nascondi entrambi i campi inizialmente
        if (dataPrestito) dataPrestito.style.display = 'none';
        if (dataCredito) dataCredito.style.display = 'none';
        if (inputRiscatto) inputRiscatto.style.display = 'none';

        // Mostra solo il campo appropriato
        if (selectedText === 'Prestito' || selectedText === 'Prestito con diritto di riscatto') {
            if (dataPrestito) dataPrestito.style.display = 'block';
        } else if (selectedText === 'Prestito con obbligo di riscatto') {
            if (dataCredito) dataCredito.style.display = 'block';
            if (inputRiscatto) inputRiscatto.style.display = 'block';
        }
    };

    // Inizializza gli handler per i select dei giocatori
    setupPlayerSelectHandler(selects.player1, 'squadra1', containers.selected1);
    setupPlayerSelectHandler(selects.player2, 'squadra2', containers.selected2);

    // Gestione del pulsante "Finalizza Trattativa"
    document.getElementById('finalizzaTrattativa').addEventListener('click', function() {
        // Ottieni i dati di base
        const idCompetizione = selects.competizione.value;
        const idSquadra1 = selects.squadra1.value;
        const idSquadra2 = selects.squadra2.value;

        // Ottieni i dati dei crediti
        const creditoTeam1 = document.getElementById('creditoTeam1').value|| 'null';
        const quandoCreditoTeam1 = document.getElementById('quandoCreditoTeam1').value || 'null';
        const creditoTeam2 = document.getElementById('creditoTeam2').value|| 'null';
        const quandoCreditoTeam2 = document.getElementById('quandoCreditoTeam2').value|| 'null';

        // Raccogli dettagli giocatori squadra 1
        const giocatoriSquadra1 = Array.from(containers.selected1.querySelectorAll('.selected-player')).map(el => {
            const playerId = el.querySelector('.remove-player').dataset.id;
            const tipoSelect = document.getElementById(`type-select-${playerId}`);
            const tipoTrasferimento = tipoSelect.value;
            const tipoTrasferimentoText = tipoSelect.options[tipoSelect.selectedIndex].text;

            const dataPrestitoEl = document.getElementById(`data-fine-prestito-${playerId}`);
            const dataPrestito = dataPrestitoEl && dataPrestitoEl.style.display !== 'none' ? dataPrestitoEl.value : null;

            const dataCreditoEl = document.getElementById(`data-fine-credito-${playerId}`);
            const dataCredito = dataCreditoEl && dataCreditoEl.style.display !== 'none' ? dataCreditoEl.value : null;

            return {
                id: playerId,
                tipoTrasferimento: tipoTrasferimento,
                tipoTrasferimentoText: tipoTrasferimentoText,
                dataPrestito: dataPrestito,
                dataCredito: dataCredito
            };
        });

        // Raccogli dettagli giocatori squadra 2
        const giocatoriSquadra2 = Array.from(containers.selected2.querySelectorAll('.selected-player')).map(el => {
            const playerId = el.querySelector('.remove-player').dataset.id;
            const tipoSelect = document.getElementById(`type-select-${playerId}`);
            const tipoTrasferimento = tipoSelect.value;
            const tipoTrasferimentoText = tipoSelect.options[tipoSelect.selectedIndex].text;

            const dataPrestitoEl = document.getElementById(`data-fine-prestito-${playerId}`);
            const dataPrestito = dataPrestitoEl && dataPrestitoEl.style.display !== 'none' ? dataPrestitoEl.value : null;

            const dataCreditoEl = document.getElementById(`data-fine-credito-${playerId}`);
            const dataCredito = dataCreditoEl && dataCreditoEl.style.display !== 'none' ? dataCreditoEl.value : null;

            return {
                id: playerId,
                tipoTrasferimento: tipoTrasferimento,
                tipoTrasferimentoText: tipoTrasferimentoText,
                dataPrestito: dataPrestito,
                dataCredito: dataCredito
            };
        });

        // Costruisci l'oggetto dati completo
        const datiTrattativa = {
            idCompetizione: idCompetizione,
            squadra1: {
                id: idSquadra1,
                giocatori: giocatoriSquadra1,
                credito: creditoTeam1,
                quandoCredito: quandoCreditoTeam1
            },
            squadra2: {
                id: idSquadra2,
                giocatori: giocatoriSquadra2,
                credito: creditoTeam2,
                quandoCredito: quandoCreditoTeam2
            }
        };





        // Visualizza i dati raccolti per debug o conferma
        const risultatoEl = document.getElementById('risultatoTrattativa');
        risultatoEl.style.display = 'block';

        // Verifica se ci sono dati sufficienti per procedere
        if (!idSquadra1 || !idSquadra2) {
            risultatoEl.className = 'risultato-trattativa error';
            risultatoEl.textContent = 'Seleziona entrambe le squadre per procedere.';
            return;
        }

        if (giocatoriSquadra1.length === 0 && giocatoriSquadra2.length === 0 && !creditoTeam1 && !creditoTeam2) {
            risultatoEl.className = 'risultato-trattativa error';
            risultatoEl.textContent = 'Seleziona almeno un giocatore o inserisci un credito per procedere.';
            return;
        }

        // Verifica che le date siano state inserite dove necessario
        const verificaDate = (giocatori) => {
            return giocatori.every(g => {
                const tipoText = g.tipoTrasferimentoText;

                if ((tipoText === 'Prestito' || tipoText === 'Prestito con diritto di riscatto') && !g.dataPrestito) {
                    return false;
                }
                if (tipoText === 'Prestito con obbligo di riscatto' && !g.dataCredito) {
                    return false;
                }
                return true;
            });
        };

        if (!verificaDate(giocatoriSquadra1) || !verificaDate(giocatoriSquadra2)) {
            risultatoEl.className = 'risultato-trattativa error';
            risultatoEl.textContent = 'Inserisci tutte le date richieste per i trasferimenti.';
            return;
        }

        /*
         Per ogni calciatore della squadra 1 e 2, verifica se il tipo di trasferimento.
             Se il trasferimento e' Vendita definitva somma Cartellino ed FVM del calciatore, il risultato e' il valore del singolo calciatore
             Se il trasferimento e' Prestito o Prestito con diritto di riscatto l' FVM e' il valore del singolo calciatore
             Se il trasferimento e' Prestito con obbligo di riscatto somma Cartellino, FVM del calciatore ed Valore Obbligo di riscatto, il risultato e' il valore del singolo calciatore

             Per ogni squadra somma i valori dei singoli calciatori + il campo credito squadra
             La squadra con il valore totale e' il target. Il target genera un range del 25%, negativo e positivo. Se il valore della squadra con valore totale e' minore e' compreso nel range la trattiva e' ok
          */
        function verificaTrattiva(datiTrattativa) {

        }

    });
</script>

</body>
</html>