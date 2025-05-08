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

                <script>
                    const selectDivisione = document.getElementById('selectDivisione');
                    const selectCompetizione = document.getElementById('selectCompetizione');
                    const selectSquadra1 = document.getElementById('selectSquadra1');
                    const selectSquadra2 = document.getElementById('selectSquadra2');

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
                        selectSquadra1.innerHTML = '<option value="" selected disabled>Seleziona squadra 1</option>';
                        selectSquadra2.innerHTML = '<option value="" selected disabled >Seleziona squadra 2</option>';

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
                        }
                    });

                    selectSquadra2.addEventListener('change', () => {
                        const selectedId = selectSquadra2.value;

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
                        }
                    });

                </script>

                <div class="squad-container">
                    <div class="squad-container-content">
                        <div class="squad1">
                            <h2 id="squadra1-nome"></h2>
                            <select id="selectSquadra1" name="selectSquadra1" class="form-control">
                                <option value="" disabled selected>Seleziona calciatore</option>
                            </select>
                        </div>
                        <div class="squad2">
                            <h2 id="squadra2-nome"></h2>
                            <select id="selectSquadra2" name="selectSquadra2" class="form-control">
                                <option value="" disabled selected>Seleziona calciatori</option>
                            </select>
                        </div>
                    </div>
            </div>

                <script>
                    const squadra1Content = document.getElementById('squadra1Content');
                    const squadra2Content = document.getElementById('squadra2Content');
                    const squadra1Nome = document.getElementById('squadra1-nome');
                    const squadra2Nome = document.getElementById('squadra2-nome');

                    // Funzione per caricare i giocatori di una squadra
                    function loadPlayers(squadraId, contentDiv) {
                        fetch(`endpoint/associazioni/read.php?id_squadra=${squadraId}`)
                            .then(response => response.json())
                            .then(data => {
                                const giocatori = data.associazioni;
                                const squadraNome = giocatori.length > 0 ? giocatori[0].nome_squadra : 'Nome squadra sconosciuto';
                                if (contentDiv === squadra1Content) {
                                    squadra1Nome.textContent = squadraNome;
                                } else {
                                    squadra2Nome.textContent = squadraNome;
                                }
                                if (Array.isArray(giocatori)) {
                                    contentDiv.innerHTML = ''; // Pulisce il contenuto precedente
                                    giocatori.forEach(giocatore => {
                                        const item = document.createElement('div');
                                        item.className = `giocatore-item${giocatore.ruolo}`;
                                        item.textContent = `${giocatore.nome} ${giocatore.cognome}`;
                                        contentDiv.appendChild(item);
                                    });
                                } else {
                                    console.error("Il campo 'associazioni' non è un array:", giocatori);
                                }
                    }


                    // Aggiungi event listener per caricare i giocatori quando le squadre sono selezionate
                    selectSquadra1.addEventListener('change', () => {
                        if (selectSquadra1.value) {
                            loadPlayers(selectSquadra1.value, squadra1Content);
                        } else {
                            squadra1Content.innerHTML = ''; // Pulisce il contenuto se non è selezionata nessuna squadra
                        }
                    });

                    selectSquadra2.addEventListener('change', () => {
                        if (selectSquadra2.value) {
                            loadPlayers(selectSquadra2.value, squadra2Content);
                        } else {
                            squadra2Content.innerHTML = ''; // Pulisce il contenuto se non è selezionata nessuna squadra
                        }
                    });

                </script>



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
</body>