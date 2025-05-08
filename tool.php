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

        .selected-players-container {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 10px;
            min-height: 100px;
            max-height: 300px;
            overflow-y: auto;
        }

        .selected-players-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .selected-player {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 8px;
            margin-bottom: 5px;
            border-radius: 5px;
        }

        .selected-playerP {
            background-color: #f8ab29;
        }

        .selected-playerD {
            background-color: #63c623;
        }

        .selected-playerC {
            background-color: #2e6be6;
        }

        .selected-playerA {
            background-color: #f21a3c;
        }

        .remove-player {
            background: none;
            border: none;
            color: #333;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
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

                <div class="select-container">
                    <select id="selectDivisione" name="selectDivisione" class="form-control">
                        <option value="" disabled selected>Seleziona una divisione</option>
                    </select>

                    <select id="selectCompetizione" name="selectCompetizione" class="form-control">
                        <option value="" disabled selected>Seleziona una competizione</option>
                    </select>

                    <select id="selectSquadra1" name="selectSquadra1" class="form-control">
                        <option value="" disabled selected>Seleziona squadra 1</option>
                    </select>

                    <select id="selectSquadra2" name="selectSquadra2" class="form-control">
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
    // Elementi DOM
    const selectDivisione = document.getElementById('selectDivisione');
    const selectCompetizione = document.getElementById('selectCompetizione');
    const selectSquadra1 = document.getElementById('selectSquadra1');
    const selectSquadra2 = document.getElementById('selectSquadra2');
    const playerSelect1 = document.getElementById('playerSelect1');
    const playerSelect2 = document.getElementById('playerSelect2');
    const selectedPlayers1 = document.getElementById('selectedPlayers1');
    const selectedPlayers2 = document.getElementById('selectedPlayers2');
    const team1Name = document.getElementById('team1-name');
    const team2Name = document.getElementById('team2-name');

    // Oggetti per memorizzare i dati dei giocatori e quelli selezionati
    let giocatoriSquadra1 = [];
    let giocatoriSquadra2 = [];
    const selectedPlayersData = {
        squadra1: new Set(),
        squadra2: new Set()
    };

    // Carica divisioni all'avvio
    fetch('endpoint/divisione/read.php')
        .then(response => response.json())
        .then(data => {
            const divisioni = Array.isArray(data) ? data : data.divisioni;
            if (Array.isArray(divisioni)) {
                divisioni.forEach(divisione => {
                    const option = document.createElement('option');
                    option.value = divisione.id;
                    option.textContent = divisione.nome_divisione;
                    selectDivisione.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Errore nel caricamento delle divisioni:', error));

    // Quando cambia la divisione, carica le competizioni
    selectDivisione.addEventListener('change', () => {
        // Pulisce selezioni successive
        selectCompetizione.innerHTML = '<option value="" disabled selected>Seleziona una competizione</option>';
        selectSquadra1.innerHTML = '<option value="" disabled selected>Seleziona squadra 1</option>';
        selectSquadra2.innerHTML = '<option value="" disabled selected>Seleziona squadra 2</option>';
        resetPlayerSelections();

        if (!selectDivisione.value) return;

        fetch(`endpoint/competizione/read.php?id_divisione=${selectDivisione.value}`)
            .then(response => response.json())
            .then(data => {
                const competizioni = Array.isArray(data) ? data : data.competizione;
                if (Array.isArray(competizioni)) {
                    competizioni
                        .sort((a, b) => a.nome_competizione.localeCompare(b.nome_competizione))
                        .forEach(competizione => {
                            const option = document.createElement('option');
                            option.value = competizione.id;
                            option.textContent = competizione.nome_competizione;
                            selectCompetizione.appendChild(option);
                        });
                }
            })
            .catch(error => console.error('Errore nel caricamento delle competizioni:', error));
    });

    // Quando cambia la competizione, carica le squadre
    selectCompetizione.addEventListener('change', () => {
        // Pulisce selezioni delle squadre
        selectSquadra1.innerHTML = '<option value="" disabled selected>Seleziona squadra 1</option>';
        selectSquadra2.innerHTML = '<option value="" disabled selected>Seleziona squadra 2</option>';
        resetPlayerSelections();

        if (!selectCompetizione.value) return;

        fetch(`endpoint/partecipazione/read.php?id_competizione=${selectCompetizione.value}`)
            .then(response => response.json())
            .then(data => {
                const squadre = data.squadre;
                if (Array.isArray(squadre)) {
                    squadre
                        .sort((a, b) => a.nome_squadra.localeCompare(b.nome_squadra))
                        .forEach(squadra => {
                            const option1 = document.createElement('option');
                            const option2 = document.createElement('option');
                            option1.value = option2.value = squadra.id;
                            option1.textContent = option2.textContent = squadra.nome_squadra;
                            selectSquadra1.appendChild(option1);
                            selectSquadra2.appendChild(option2);
                        });
                } else {
                    console.error("Il campo 'squadre' non è un array:", squadre);
                }
            })
            .catch(error => console.error('Errore nel caricamento delle squadre:', error));
    });

    // Evita che venga selezionata la stessa squadra nei due menu
    selectSquadra1.addEventListener('change', () => {
        const selectedId = selectSquadra1.value;

        // Resetta le selezioni per la squadra 1
        resetPlayerSelections('squadra1');

        // Riattiva tutte le opzioni in selectSquadra2
        Array.from(selectSquadra2.options).forEach(option => {
            option.disabled = false;
        });

        // Disattiva l'opzione selezionata in selectSquadra2
        if (selectedId) {
            const optionToDisable = selectSquadra2.querySelector(`option[value="${selectedId}"]`);
            if (optionToDisable) {
                optionToDisable.disabled = true;
            }

            // Carica i giocatori della squadra 1
            loadPlayers(selectedId, 'squadra1');
        }
    });

    selectSquadra2.addEventListener('change', () => {
        const selectedId = selectSquadra2.value;

        // Resetta le selezioni per la squadra 2
        resetPlayerSelections('squadra2');

        // Riattiva tutte le opzioni in selectSquadra1
        Array.from(selectSquadra1.options).forEach(option => {
            option.disabled = false;
        });

        // Disattiva l'opzione selezionata in selectSquadra1
        if (selectedId) {
            const optionToDisable = selectSquadra1.querySelector(`option[value="${selectedId}"]`);
            if (optionToDisable) {
                optionToDisable.disabled = true;
            }

            // Carica i giocatori della squadra 2
            loadPlayers(selectedId, 'squadra2');
        }
    });

    // Funzione per caricare i giocatori di una squadra
    function loadPlayers(squadraId, targetSquad) {
        fetch(`endpoint/associazioni/read.php?id_squadra=${squadraId}`)
            .then(response => response.json())
            .then(data => {
                const giocatori = data.associazioni;

                // Memorizza i giocatori in un array locale
                if (targetSquad === 'squadra1') {
                    giocatoriSquadra1 = giocatori || [];
                } else {
                    giocatoriSquadra2 = giocatori || [];
                }

                // Aggiorna il nome della squadra
                if (giocatori && giocatori.length > 0) {
                    if (targetSquad === 'squadra1') {
                        team1Name.textContent = giocatori[0].nome_squadra || 'Squadra 1';
                    } else {
                        team2Name.textContent = giocatori[0].nome_squadra || 'Squadra 2';
                    }
                }

                // Ottieni il menu a tendina di destinazione
                const selectPlayer = targetSquad === 'squadra1' ? playerSelect1 : playerSelect2;

                // Pulisce il menu a tendina
                selectPlayer.innerHTML = '<option value="" disabled selected>Seleziona un calciatore</option>';

                if (Array.isArray(giocatori)) {
                    // Ordina i giocatori per ruolo (P, D, C, A) e poi per cognome
                    giocatori.sort((a, b) => {
                        const ruoliOrdine = { 'P': 1, 'D': 2, 'C': 3, 'A': 4 };
                        if (a.ruolo_calciatore !== b.ruolo_calciatore) {
                            return ruoliOrdine[a.ruolo_calciatore] - ruoliOrdine[b.ruolo_calciatore];
                        }
                        return a.nome_calciatore.localeCompare(b.nome_calciatore);
                    }).forEach(giocatore => {
                        const option = document.createElement('option');
                        option.value = giocatore.id;
                        option.textContent = `${giocatore.nome_calciatore} (${giocatore.ruolo_calciatore})`;
                        option.classList = 'giocatore-item'+giocatore.ruolo_calciatore;

                        selectPlayer.appendChild(option);
                    });
                } else {
                    console.error("Il campo 'associazioni' non è un array:", giocatori);
                }
            })
            .catch(error => console.error(`Errore nel caricamento dei giocatori per ${targetSquad}:`, error));
    }

    // Event listener per la selezione dei giocatori
    playerSelect1.addEventListener('change', function() {
        if (this.value) {
            const playerIndex = giocatoriSquadra1.findIndex(g => g.id == this.value);
            if (playerIndex !== -1) {
                const player = giocatoriSquadra1[playerIndex];
                // Verifica se il giocatore è già stato selezionato
                if (!selectedPlayersData.squadra1.has(player.id)) {
                    addPlayerToSelected('squadra1', player);
                }
            }
            // Resetta la selezione dopo l'aggiunta
            this.selectedIndex = 0;
        }
    });

    playerSelect2.addEventListener('change', function() {
        if (this.value) {
            const playerIndex = giocatoriSquadra2.findIndex(g => g.id == this.value);
            if (playerIndex !== -1) {
                const player = giocatoriSquadra2[playerIndex];
                // Verifica se il giocatore è già stato selezionato
                if (!selectedPlayersData.squadra2.has(player.id)) {
                    addPlayerToSelected('squadra2', player);
                }
            }
            // Resetta la selezione dopo l'aggiunta
            this.selectedIndex = 0;
        }
    });

    // Funzione per aggiungere un giocatore all'elenco dei selezionati
    function addPlayerToSelected(squadra, player) {
        // Aggiungi l'ID del giocatore all'insieme dei selezionati
        selectedPlayersData[squadra].add(player.id);

        // Crea l'elemento del giocatore
        const playerElement = document.createElement('div');
        playerElement.className = `selected-player selected-player${player.ruolo_calciatore}`;
        playerElement.dataset.playerId = player.id;

        playerElement.innerHTML = `
        <span>${player.nome_calciatore} (${player.ruolo_calciatore})</span>
        <button class="remove-player" data-player-id="${player.id}" data-squad="${squadra}">×</button>
    `;

        // Aggiungi il gestore eventi per il pulsante di rimozione
        playerElement.querySelector('.remove-player').addEventListener('click', function() {
            const playerId = this.dataset.playerId;
            const squad = this.dataset.squad;
            removePlayerFromSelected(squad, playerId);
        });

        // Aggiungi l'elemento al container appropriato
        const container = squadra === 'squadra1' ? selectedPlayers1 : selectedPlayers2;
        container.appendChild(playerElement);
    }

    // Funzione per rimuovere un giocatore dall'elenco dei selezionati
    function removePlayerFromSelected(squadra, playerId) {
        // Rimuovi l'ID del giocatore dall'insieme dei selezionati
        selectedPlayersData[squadra].delete(playerId);

        // Rimuovi l'elemento dalla UI
        const container = squadra === 'squadra1' ? selectedPlayers1 : selectedPlayers2;
        const playerElement = container.querySelector(`[data-player-id="${playerId}"]`);
        if (playerElement) {
            playerElement.remove();
        }
    }

    // Funzione per resettare le selezioni dei giocatori
    function resetPlayerSelections(squadra = null) {
        if (!squadra || squadra === 'squadra1') {
            playerSelect1.innerHTML = '<option value="" disabled selected>Seleziona un calciatore</option>';
            selectedPlayers1.innerHTML = '';
            selectedPlayersData.squadra1.clear();
            team1Name.textContent = 'Squadra 1';
            giocatoriSquadra1 = [];
        }

        if (!squadra || squadra === 'squadra2') {
            playerSelect2.innerHTML = '<option value="" disabled selected>Seleziona un calciatore</option>';
            selectedPlayers2.innerHTML = '';
            selectedPlayersData.squadra2.clear();
            team2Name.textContent = 'Squadra 2';
            giocatoriSquadra2 = [];
        }
    }
</script>
</body>
</html>