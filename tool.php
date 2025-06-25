<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FMPro</title>
    <link rel="icon" href="public/background/logo.png" type="image/png">

    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="js/showmenu.js" defer></script>

    <style>
        .tool-container{
            width: 90%;
        }

        .select-container{
            display: flex;
            justify-content: space-evenly;
            background: linear-gradient(135deg, var(--accento), var(--blu-scurissimo));
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
        }

        .select-container > select {
            width: auto ;
            margin: 0;
        }

        .inviaTrattativa {
            background-color: var(--blu-scuro);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .inviaTrattativa:hover {
            background-color: var(--blu-scurissimo);
        }

        .inviaTrattativa:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
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

        input:out-of-range {
            border-color: #f21a3c;
            background-color: #fde8eb;
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
            background: linear-gradient(135deg, var(--accento), var(--blu-scurissimo));
        }

        .credito-container{
            background: linear-gradient(135deg, var(--accento), var(--blu-scurissimo));
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            gap: 20px;
            flex-wrap: wrap;
        }

        .editor{
            height: 100%;
            width: 100%;
            background: white;
            color: black;
        }

        .ql-editor > p{
            color: black;
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

        input{
            width: 100%;
        }


        .credito-box select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
            color: var(--blu-scurissimo);
            width: 40%;
        }

        .calcola-container {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
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
            background-color: #3c763d;
            border-color: #d6e9c6;
            color: #3c763d;
            font-weight: bold;
        }

        .error {
            background-color: #a94442;
            border-color: #ebccd1;
            color: white;
            font-weight: bold;
        }

        .finestra-credito{
            display: flex        ;
            justify-content: space-between;
            align-items: center;
        }
        .main-body{
            height: 100%;
            overflow-y: scroll;
        }
        .main-body-content{
            margin-top: 20px;
        }

        @media (max-width: 1024px) {
            .player-select-container {
                flex-direction: column;
                gap: 10px;
            }
            .team-container {
                width: 100%;
            }
            .select-container{
                display: flex;
                flex-direction: column;
            }

            .select-container > select {
                width: auto ;
                margin-bottom: 15px;
            }

        }
    </style>
</head>

<body>
<div class="main-container">
    <aside class="main-menu">
        <div class="menu-header">
            <img src="public/background/logo.png" alt="Logo" class="logo" width="80px" height="80px">
            <h3>FMPro</h3>
        </div>

        <ul class="menu-list">
            <li class="menu-item">
                <a href="index.php">Dashboard</a>
            </li>
            <li class="menu-item">
                <a href="albo.php">Albo d'oro</a>
            </li>
            <li class="menu-item">
                <a href="vendita.php">Squadre in vendita</a>
            </li>
            <li class="menu-item">
                <a href="tool.php">Tool scambi</a>
            </li>
            <li class="menu-item">
                <a href="regolamento.php">Regolamento</a>
            </li>
            <li class="menu-item">
                <a href="ricerca.php">Ricerca</a>
            </li>
            <li class="menu-item">
                <a href="contatti.php">Contatti</a>
            </li>
        </ul>
    </aside>

    <div class="main-content">
        <header class="main-header">
            <div class="main-text-header">
                <button class="back-button" onclick="window.history.back();">
                    <img src="public/chevron/chevronL.svg" alt="Indietro" height="40px" width="40px">
                </button>
                <h1>Tool Scambi</h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
        </header>


        <div class="main-body">
            <div class="main-body-content" id="main-body-content">

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
                            <h4>Credito massimo disponibile: <span id="maxCreditoTeam1">0</span></h4>
                            <div class="finestra-credito">
                                <input type="hidden" id="subitoTeam1" value="8">
                                <input type="number" id="creditoTeam1_1" min="0" placeholder="Subito">
                            </div>
                            <div class="finestra-credito">
                                <input type="hidden" id="metaTeam1" value="9">
                                <input type="number" id="creditoTeam1_2" min="0" placeholder="Gennaio">
                            </div>
                            <div class="finestra-credito">
                                <input type="hidden" id="fineTeam1" value="10">
                                <input type="number" id="creditoTeam1_3" min="0" max="200" placeholder="Giugno">
                            </div>
                            <div class="credito-totale">
                                <label>Totale:</label>
                                <span id="totaleCreditoTeam1">0</span>
                            </div>
                        </div>

                        <div class="credito-box">
                            <h4>Credito massimo disponibile: <span id="maxCreditoTeam2">0</span></h4>
                            <div class="finestra-credito">
                                <input type="hidden" id="subitoTeam2" value="8">
                                <input type="number" id="creditoTeam2_1" min="0" placeholder="Subito">
                            </div>
                            <div class="finestra-credito">
                                <input type="hidden" id="metaTeam2" value="9">
                                <input type="number" id="creditoTeam2_2" min="0" placeholder="Gennaio">
                            </div>
                            <div class="finestra-credito">
                                <input type="hidden" id="fineTeam2" value="10">
                                <input type="number" id="creditoTeam2_3" min="0"  max="200" placeholder="Giugno">
                            </div>
                            <div class="credito-totale">
                                <label>Totale:</label>
                                <span id="totaleCreditoTeam2">0</span>
                            </div>
                        </div>
                        <div class="editor">
                            <div id="editor-container"></div>
                            <input type="hidden" id="contenuto" name="contenuto">
                        </div>
                    </div>

                    <div class="calcola-container">
                        <button id="calcolaTrattativa" class="calcola-button">Calcola Trattativa</button>
                        <div id="risultatoTrattativa" class="risultato-trattativa"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    const quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean'],
            ]
        },
        placeholder: 'Scrivi ulteriori dettagli qui...'
    });
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
    // Aggiungi queste variabili globali
    let maxCreditoTeam1 = 0;
    let maxCreditoTeam2 = 0;

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
                populateSelect(selects.squadra1, squadre, 'id_squadra', 'nome_squadra');
                populateSelect(selects.squadra2, squadre, 'id_squadra', 'nome_squadra');
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

            // Carica il credito massimo della squadra
            fetchData(`endpoint/squadra/read.php?id_squadra=${id}`)
                .then(data => {
                    const creditoMassimo = data.squadra[0]?.finanze.credito || 0;
                    if (squadraSelezionata === 'squadra1') {
                        maxCreditoTeam1 = creditoMassimo;
                        document.getElementById('maxCreditoTeam1').textContent = creditoMassimo;
                    } else {
                        maxCreditoTeam2 = creditoMassimo;
                        document.getElementById('maxCreditoTeam2').textContent = creditoMassimo;
                    }
                });
        }
    };
    function setupCreditoListeners(teamPrefix) {
        const inputs = [
            document.getElementById(`${teamPrefix}_1`),
            document.getElementById(`${teamPrefix}_2`),
            document.getElementById(`${teamPrefix}_3`)
        ];

        const maxCredito = teamPrefix === 'creditoTeam1' ? maxCreditoTeam1 : maxCreditoTeam2;
        const totaleElement = document.getElementById(`totale${teamPrefix.charAt(0).toUpperCase() + teamPrefix.slice(1)}`);

        inputs.forEach(input => {
            input.addEventListener('input', () => {
                let totale = 0;
                inputs.forEach(i => {
                    const value = parseFloat(i.value) || 0;
                    totale += value;
                });

                totaleElement.textContent = totale;

                // Validazione
                if (totale > maxCredito) {
                    totaleElement.style.fontWeight = 'bold';
                } else {
                    totaleElement.style.color = 'inherit';
                }
            });
        });
    }

    // Chiama la funzione di setup all'avvio
    document.addEventListener('DOMContentLoaded', () => {
        setupCreditoListeners('creditoTeam1');
        setupCreditoListeners('creditoTeam2');
    });

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
                    opt.dataset.n_movimenti = giocatore.n_movimenti;
                    opt.dataset.scambiato = giocatore.scambiato;
                    if(giocatore.scambiato) {
                        opt.disabled = true;
                    }
                    if(giocatore.n_movimenti > 2) {
                        opt.disabled = true;
                    }
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
        inputRiscatto.min = 1;
        inputRiscatto.className = 'riscatto-box';
        inputRiscatto.placeholder = 'Riscatto';
        inputRiscatto.id = `riscatto-input-${player.id}`; // Make sure the ID format matches what you look for later
        inputRiscatto.style.display = 'none';

        const creditoOptions = [
            {value: '', text: 'Seleziona data di credito', disabled: true, selected: true},
            {value: '9', text: 'Gennaio'},
            {value: '10', text: 'Giugno'}
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
    document.getElementById('calcolaTrattativa').addEventListener('click', function() {


        // Ottieni i dati di base
        const idCompetizione = selects.competizione.value;
        const idSquadra1 = selects.squadra1.value;
        const idSquadra2 = selects.squadra2.value;

        // Ottieni i dati dei crediti
        const creditoTeam1Subito = document.getElementById('creditoTeam1_1').value || '0';
        const creditoTeam2Subito = document.getElementById('creditoTeam2_1').value || '0';
        const creditoTeam1Meta = document.getElementById('creditoTeam1_2').value || '0';
        const creditoTeam2Meta = document.getElementById('creditoTeam2_2').value || '0';
        const creditoTeam1Fine = document.getElementById('creditoTeam1_3').value || '0';
        const creditoTeam2Fine = document.getElementById('creditoTeam2_3').value || '0';
        const creditoTeam1 = [creditoTeam1Subito, creditoTeam1Meta, creditoTeam1Fine];
        const creditoTeam2 = [creditoTeam2Subito, creditoTeam2Meta, creditoTeam2Fine];

        // Raccogli dettagli giocatori squadra 1
        function raccogliGiocatori(container) {
            return Array.from(container.querySelectorAll('.selected-player')).map(el => {
                const playerId = el.querySelector('.remove-player').dataset.id;

                // Gestione tipo trasferimento
                const tipoSelect = document.getElementById(`type-select-${playerId}`);
                let tipoTrasferimento = tipoSelect ? tipoSelect.value : '';
                const tipoTrasferimentoText = tipoSelect && tipoSelect.value !== '' ?
                    tipoSelect.options[tipoSelect.selectedIndex].text : '0';

                // Gestione date
                const dataPrestitoEl = document.getElementById(`data-fine-prestito-${playerId}`);
                const dataPrestito = dataPrestitoEl && dataPrestitoEl.style.display !== 'none' ? dataPrestitoEl.value : null;

                const dataCreditoEl = document.getElementById(`data-fine-credito-${playerId}`);
                const dataCredito = dataCreditoEl && dataCreditoEl.style.display !== 'none' ? dataCreditoEl.value : null;

                // Gestione valore riscatto
                const valoreRiscattoEl = document.getElementById(`riscatto-input-${playerId}`);
                const valoreRiscatto = valoreRiscattoEl ? parseFloat(valoreRiscattoEl.value) : null;

                // Determinazione valore trasferimento
                let valore = 1; // Default

                if (tipoTrasferimento) { // Solo se c'è un tipo selezionato
                    tipologieScambio.forEach(tipo => {
                        if (tipo.id_metodo === tipoTrasferimento) {
                            if (dataCredito !== null && tipo.id_finestra_mercato && tipo.id_finestra_mercato.toString() === dataCredito) {
                                valore = tipo.id_tipologia;
                            }
                            else if (dataPrestito !== null && tipo.id_finestra_mercato && tipo.id_finestra_mercato.toString() === dataPrestito) {
                                valore = tipo.id_tipologia;
                            }
                        }
                    });
                } else {
                    valore = 0; // Nessun tipo selezionato
                }

                return {
                    id: playerId,
                    tipoTrasferimento: valore,
                    tipoTrasferimentoText: tipoTrasferimentoText,
                    valoreRiscatto: valoreRiscatto,
                    dataPrestito: dataPrestito,
                    dataCredito: dataCredito
                };
            });
        }

// Utilizzo
        const giocatoriSquadra1 = raccogliGiocatori(containers.selected1);
        const giocatoriSquadra2 = raccogliGiocatori(containers.selected2);

        // Costruisci l'oggetto dati completo
        const datiTrattativa = {
            idCompetizione: idCompetizione,
            squadra1: {
                id: idSquadra1,
                giocatori: giocatoriSquadra1,
                credito: {
                    8: creditoTeam1Subito,
                    9: creditoTeam1Meta,
                    10: creditoTeam1Fine
                }

            },
            squadra2: {
                id: idSquadra2,
                giocatori: giocatoriSquadra2,
                credito: {
                    8: creditoTeam2Subito,
                    9: creditoTeam2Meta,
                    10: creditoTeam2Fine
                }
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

        // Verifica che le squadre scambino almeno un giocatore
        if (giocatoriSquadra1.length === 0 && giocatoriSquadra2.length === 0) {
            risultatoEl.className = 'risultato-trattativa error';
            risultatoEl.textContent = 'Seleziona almeno un giocatore';
            return;
        }

        // Verifica che i tipi di trasferimento siano stati selezionati per tutti i giocatori
        const verificaTipiTrasferimento = (giocatori) => {
            return giocatori.every(g => {
                return g.tipoTrasferimento && g.tipoTrasferimentoText &&
                    g.tipoTrasferimento !== '0' &&
                    g.tipoTrasferimentoText !== '0';
            });
        };

        if (!verificaTipiTrasferimento(giocatoriSquadra1) || !verificaTipiTrasferimento(giocatoriSquadra2)) {
            risultatoEl.className = 'risultato-trattativa error';
            risultatoEl.textContent = 'Seleziona il tipo di trasferimento per tutti i giocatori.';
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

        // Esegui la verifica della trattativa
        const isValid = verificaTrattativa(datiTrattativa);

        if (isValid) {
            // Rimuovi eventuali bottoni precedenti
            const existingButton = document.querySelector('.inviaTrattativa');
            if (existingButton) {
                existingButton.remove();
            }

            // Crea il nuovo bottone
            const inviaTrattativa = document.createElement('button');
            inviaTrattativa.className = 'inviaTrattativa';
            inviaTrattativa.textContent = 'Invia Trattativa';

            // Aggiungi l'event listener
            inviaTrattativa.addEventListener('click', async () => {
                inviaTrattativa.disabled = true;
                inviaTrattativa.textContent = 'Invio in corso...';

                try {
                    // 1. Crea la trattativa principale
                    const trattativaData = {
                        id_competizione: idCompetizione,
                        id_squadra1: idSquadra1,
                        id_squadra2: idSquadra2,
                        descrizione : quill.root.innerHTML
                    };

                    console.log('Dati trattativa:', trattativaData);

                    // Invio alla trattativa principale
                    const responseTrattativa = await fetch('endpoint/trattative/create.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(trattativaData)
                    });
                    // Converti la risposta in JSON
                    const resultTrattativa = await responseTrattativa.json();
                    let idTrattativa;
                    idTrattativa = resultTrattativa.id_trattativa;

                    // 2. Invio associazioni giocatori
                    const associazioniPromises = [];

                    // Processa giocatori squadra 1
                    giocatoriSquadra1.forEach(g => {
                        const associazioneData = {
                            id_trattativa: idTrattativa,
                            id_associazione: g.id,
                            id_tipologia_scambio: g.tipoTrasferimento,
                            valore_riscatto: g.valoreRiscatto || null,
                            id_squadra_c: idSquadra1,
                            id_squadra_r: idSquadra2
                        };

                        associazioniPromises.push(
                            fetch('endpoint/operazioni/create.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(associazioneData)
                            }).then(response => {
                                if (!response.ok) {
                                    throw new Error(`Errore HTTP associazione: ${response.status}`);
                                }
                                return response.json();
                            })
                        );
                    });

                    // Processa giocatori squadra 2
                    giocatoriSquadra2.forEach(g => {
                        const associazioneData = {
                            id_trattativa: idTrattativa,
                            id_associazione: g.id,
                            id_tipologia_scambio: g.tipoTrasferimento,
                            valore_riscatto: g.valoreRiscatto || null,
                            id_squadra_c: idSquadra2,
                            id_squadra_r: idSquadra1
                        };

                        associazioniPromises.push(
                            fetch('endpoint/operazioni/create.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(associazioneData)
                            }).then(response => {
                                if (!response.ok) {
                                    throw new Error(`Errore HTTP associazione: ${response.status}`);
                                }
                                return response.json();
                            })
                        );
                    });

                    // Processa i crediti
                    const creditiPromises = [];
                    const creditiTeam1 = creditoTeam1.map((credito, index) => ({
                        id_squadra: idSquadra1,
                        id_trattativa: idTrattativa,
                        id_fm: index + 8, // 9 per gennaio, 10 per giugno
                        credito: credito
                    }));
                    const creditiTeam2 = creditoTeam2.map((credito, index) => ({
                        id_squadra: idSquadra2,
                        id_trattativa: idTrattativa,
                        id_fm: index + 8, // 9 per gennaio, 10 per giugno
                        credito: credito
                    }));

                    creditiTeam1.forEach(credito => {
                        creditiPromises.push(
                            fetch('endpoint/credito/create.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(credito)
                            }).then(response => {
                                if (!response.ok) {
                                    throw new Error(`Errore HTTP credito: ${response.status}`);
                                }
                                return response.json();
                            })
                        );
                    });

                    creditiTeam2.forEach(credito => {
                        creditiPromises.push(
                            fetch('endpoint/credito/create.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(credito)
                            }).then(response => {
                                if (!response.ok) {
                                    throw new Error(`Errore HTTP credito: ${response.status}`);
                                }
                                return response.json();
                            })
                        );
                    });

                    try {
                        // Attendi tutte le risposte delle associazioni
                        const associazioniJson = await Promise.all(associazioniPromises);

                        // Verifica se tutte le associazioni sono andate a buon fine
                        const failedAssociations = associazioniJson.filter(r => !r.success);

                        if (failedAssociations.length > 0) {
                            console.error("Associazioni fallite:", failedAssociations);
                            throw new Error(`${failedAssociations.length} associazioni non sono state registrate correttamente`);
                        } else {
                            alert('Trattativa e associazioni registrate con successo! Il tuo id trattativa è ' + idTrattativa);
                            window.location.reload();
                        }
                    } catch (associationError) {
                        console.error('Errore nelle associazioni:', associationError);
                        alert('Errore durante il salvataggio delle associazioni: ' + associationError.message);
                    }

                } catch (err) {
                    console.error('Errore generale:', err);
                    alert('Errore durante il salvataggio: ' + err.message);
                } finally {
                    inviaTrattativa.disabled = false;
                    inviaTrattativa.textContent = 'Invia Trattativa';
                }
            });

            // Aggiungi il bottone al DOM (sotto il risultato della trattativa)
            const risultatoEl = document.getElementById('risultatoTrattativa');
            risultatoEl.appendChild(inviaTrattativa);
        }
    });

    function verificaTrattativa(datiTrattativa) {
        // 1. Verifica che tutti i giocatori abbiano un tipo di trasferimento selezionato
        const verificaTipiTrasferimento = (giocatori) => {
            return giocatori.every(g => {
                return g.tipoTrasferimento && g.tipoTrasferimentoText &&
                    g.tipoTrasferimento !== '0' &&
                    g.tipoTrasferimentoText !== '0';
            });
        };

        const risultatoEl = document.getElementById('risultatoTrattativa');
        risultatoEl.style.display = 'block';
        //
        if (!verificaTipiTrasferimento(datiTrattativa.squadra1.giocatori) ||
            !verificaTipiTrasferimento(datiTrattativa.squadra2.giocatori)) {
            risultatoEl.className = 'risultato-trattativa error';
            risultatoEl.innerHTML = 'Seleziona il tipo di trasferimento per tutti i giocatori';
            return false;
        }

        // 2. Verifica che i prestiti con obbligo di riscatto abbiano un valore di riscatto valido
        const verificaRiscatti = (giocatori) => {
            return giocatori.every(g => {
                if (g.tipoTrasferimentoText === 'Prestito con obbligo di riscatto') {
                    const riscattoInput = document.getElementById(`riscatto-input-${g.id}`);
                    const riscatto = riscattoInput ? parseFloat(riscattoInput.value) : null;

                    if (riscatto === null || isNaN(riscatto) || riscatto <= 0) {
                        return false;
                    }
                }
                return true;
            });
        };

        if (!verificaRiscatti(datiTrattativa.squadra1.giocatori) ||
            !verificaRiscatti(datiTrattativa.squadra2.giocatori)) {
            risultatoEl.className = 'risultato-trattativa error';
            risultatoEl.innerHTML = 'Inserisci un valore di riscatto valido per tutti i prestiti con obbligo di riscatto';
            return false;
        }

        // 3. Calcola i riscatti che ogni squadra riceverà
        const calcolaRiscattiRicevuti = (isTeam1) => {
            let riscatti = 0;
            const squadraOpposta = isTeam1 ? datiTrattativa.squadra2 : datiTrattativa.squadra1;

            squadraOpposta.giocatori.forEach(giocatore => {
                if (giocatore.tipoTrasferimentoText === 'Prestito con obbligo di riscatto') {
                    const riscattoInput = document.getElementById(`riscatto-input-${giocatore.id}`);
                    const riscatto = riscattoInput ? parseFloat(riscattoInput.value) : 0;
                    if (!isNaN(riscatto) && riscatto > 0) {
                        riscatti += riscatto;
                    }
                }
            });
            return riscatti;
        };

        // Calcola i riscatti per ogni squadra
        const riscattiTeam1 = calcolaRiscattiRicevuti(true);
        const riscattiTeam2 = calcolaRiscattiRicevuti(false);

        // 3.1 Calcola i crediti totali per ogni squadra includendo i riscatti nel credito3
        const calcolaCreditiTotali = (teamPrefix, riscatti) => {
            const credito1 = parseFloat(document.getElementById(`${teamPrefix}_1`).value) || 0;
            const credito2 = parseFloat(document.getElementById(`${teamPrefix}_2`).value) || 0;
            const credito3 = parseFloat(document.getElementById(`${teamPrefix}_3`).value) || 0;

            // Non aggiungiamo qui i riscatti al credito3, li manterremo separati per la verifica specifica
            return {
                credito1: credito1,
                credito2: credito2,
                credito3: credito3,
                riscatti: riscatti,
                totale: credito1 + credito2 + credito3 + riscatti
            };
        };

        // Calcola i crediti totali mantenendo i riscatti separati
        const creditiTeam1 = calcolaCreditiTotali('creditoTeam1', riscattiTeam1);
        const creditiTeam2 = calcolaCreditiTotali('creditoTeam2', riscattiTeam2);

        // 4. Verifica specifica per il credito di giugno (finestra 3) + riscatti di giugno
        const verificaCreditoGiugnoConRiscatti = (teamName, creditiTeam, isTeam1) => {
            let errore = false;
            const squadraOpposta = isTeam1 ? datiTrattativa.squadra2 : datiTrattativa.squadra1;
            const squadraOppostaMaxCredito = isTeam1 ? maxCreditoTeam2 : maxCreditoTeam1;
            const squadraMaxCredito = isTeam1 ? maxCreditoTeam1 : maxCreditoTeam2;

            // Calcoliamo i riscatti di giugno separatamente
            let riscattiGiugno = 0;

            squadraOpposta.giocatori.forEach(g => {
                if (g.tipoTrasferimentoText === 'Prestito con obbligo di riscatto') {
                    const riscattoInput = document.getElementById(`riscatto-input-${g.id}`);
                    const riscatto = riscattoInput ? parseFloat(riscattoInput.value) : 0;

                    const riscattoSelect = document.getElementById(`data-fine-credito-${g.id}`);
                    const riscattoPeriodo = riscattoSelect ? riscattoSelect.value : null;

                    // Somma solo i riscatti di giugno (value = "10")
                    if (riscattoPeriodo === "10" && riscatto > 0) {
                        riscattiGiugno += riscatto;
                    }
                }
            });

            // Ora verifichiamo se la somma del credito di giugno e i riscatti di giugno supera il massimo
            const somma = creditiTeam.credito3 + riscattiGiugno;

            if (somma > squadraMaxCredito) {
                errore = true;
                risultatoEl.className = 'risultato-trattativa error';
                risultatoEl.innerHTML = `La somma tra credito di Giugno e i riscatti ricevuti di Giugno per la ${teamName} supera ${squadraMaxCredito} (totale: ${somma.toFixed(2)}). Trattativa non valida.`;
            }

            return !errore;
        };

        // Verifica i crediti di giugno
        if (
            !verificaCreditoGiugnoConRiscatti('Squadra 1', creditiTeam1, true) ||
            !verificaCreditoGiugnoConRiscatti('Squadra 2', creditiTeam2, false)
        ) {
            return false;
        }

        // 5. Calcola il valore totale per ogni squadra (esclusi i riscatti che sono già nei crediti)
        const calcolaValoreSquadra = (squadra, isTeam1) => {
            let valoreTotale = 0;

            // Aggiungi il valore dei giocatori
            squadra.giocatori.forEach(giocatore => {
                const playerData = [...giocatori.squadra1, ...giocatori.squadra2].find(p => p.id == giocatore.id);
                if (!playerData) return;

                const fvm = parseFloat(playerData.fvm) || 0;
                const costo = parseFloat(playerData.costo_calciatore) || 0;

                switch(giocatore.tipoTrasferimentoText) {
                    case 'Vendita definitiva':
                        valoreTotale += costo + fvm;
                        break;
                    case 'Prestito':
                    case 'Prestito con diritto di riscatto':
                        valoreTotale += fvm;
                        break;
                    case 'Prestito con obbligo di riscatto':
                        // Il riscatto è già stato aggiunto ai crediti della squadra opposta
                        valoreTotale += costo + fvm;
                        break;
                    default:
                        valoreTotale += fvm;
                }
            });

            return valoreTotale;
        };

        // Calcola i valori base delle squadre (senza crediti)
        const valoreBaseSquadra1 = calcolaValoreSquadra(datiTrattativa.squadra1, true);
        const valoreBaseSquadra2 = calcolaValoreSquadra(datiTrattativa.squadra2, false);

        // Il valore totale include: valore base + crediti (che già contengono i riscatti)
        const valoreSquadra1 = valoreBaseSquadra1 + creditiTeam1.totale;
        const valoreSquadra2 = valoreBaseSquadra2 + creditiTeam2.totale;

        // 6. Determina quale squadra ha il valore più alto (target)
        const target = valoreSquadra1 > valoreSquadra2 ?
            { squadra: 'squadra1', valore: valoreSquadra1 } :
            { squadra: 'squadra2', valore: valoreSquadra2 };

        const other = target.squadra === 'squadra1' ?
            { squadra: 'squadra2', valore: valoreSquadra2 } :
            { squadra: 'squadra1', valore: valoreSquadra1 };

        // 7. Calcola il range accettabile (25% del valore target)
        const range = target.valore * 0.25;
        const minimoAccettabile = target.valore - range;
        const massimoAccettabile = target.valore + range;

        // 8. Verifica se il valore dell'altra squadra è nel range
        const isValido = other.valore >= minimoAccettabile && other.valore <= massimoAccettabile;

        // 9. Prepara il risultato per l'output
        if (isValido) {
            risultatoEl.className = 'risultato-trattativa success';
            risultatoEl.innerHTML = `
            <p>Trattativa VALIDA!</p>
            <p><strong>Squadra 1:</strong>
                Valore totale: ${valoreSquadra1.toFixed(2)}
                (Crediti: ${(creditiTeam1.credito1 + creditiTeam1.credito2 + creditiTeam1.credito3).toFixed(2)},
                 Riscatti: ${creditiTeam1.riscatti.toFixed(2)})
            </p>
            <p><strong>Squadra 2:</strong>
                Valore totale: ${valoreSquadra2.toFixed(2)}
                (Crediti: ${(creditiTeam2.credito1 + creditiTeam2.credito2 + creditiTeam2.credito3).toFixed(2)},
                 Riscatti: ${creditiTeam2.riscatti.toFixed(2)})
            </p>
        `;
        } else {
            risultatoEl.className = 'risultato-trattativa error';
            risultatoEl.innerHTML = `
            <p>Trattativa NON VALIDA!</p>
            <p><strong>Squadra 1:</strong>
                Valore totale: ${valoreSquadra1.toFixed(2)}
                (Crediti: ${(creditiTeam1.credito1 + creditiTeam1.credito2 + creditiTeam1.credito3).toFixed(2)},
                 Riscatti: ${creditiTeam1.riscatti.toFixed(2)})
            </p>
            <p><strong>Squadra 2:</strong>
                Valore totale: ${valoreSquadra2.toFixed(2)}
                (Crediti: ${(creditiTeam2.credito1 + creditiTeam2.credito2 + creditiTeam2.credito3).toFixed(2)},
                 Riscatti: ${creditiTeam2.riscatti.toFixed(2)})
            </p>
        `;
        }

        return isValido;
    }
</script>

</body>
</html>