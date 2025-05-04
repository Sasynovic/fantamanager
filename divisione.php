<?php
header('Content-Type: text/html; charset=utf-8');
$id_divisione = isset($_GET['id_divisione']) ? intval($_GET['id_divisione']) : 3;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divisione - Dettaglio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
            padding: 20px;
        }
        .competition-item {
            cursor: pointer;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .competition-item:hover {
            background-color: #f8f9fa;
        }
        .competition-item.active {
            background-color: #0d6efd;
            color: white;
        }
        .team-card {
            margin-bottom: 15px;
            transition: transform 0.3s;
        }
        .team-card:hover {
            transform: translateY(-5px);
        }
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
        }
        .division-title {
            padding: 15px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        #modaleDettagli {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
        }
        #contenutoModale {
            background: white;
            padding: 20px;
            width: 90%;
            max-width: 800px;
            max-height: 90%;
            overflow-y: auto;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 sidebar">
            <h3 class="mb-4">Competizioni</h3>
            <div id="competitions-list">
                <div class="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Caricamento...</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9 p-4">
            <div id="division-title" class="division-title">
                <h2>Seleziona una competizione</h2>
            </div>
            <div id="teams-container" class="row text-center">
                <p>Seleziona una competizione dalla barra laterale per visualizzare le squadre partecipanti.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modale Dettagli -->
<div id="modaleDettagli" onclick="this.style.display='none'">
    <div id="contenutoModale" onclick="event.stopPropagation();">
        <!-- Dettagli caricati dinamicamente -->
    </div>
</div>

<script>
    let divisione = "";
    let idDivisione = <?= $id_divisione ?>;
    let activeCompetitionId = null;
    const SQUADRE_CACHE = [];

    document.addEventListener('DOMContentLoaded', () => {
        loadCompetitions();
    });

    async function loadCompetitions() {
        try {
            const res = await fetch(`https://barrettasalvatore.it/endpoint/competizione/read.php?id_divisione=${idDivisione}`);
            const data = await res.json();
            const competitions = Array.isArray(data) ? data : (data.competizioni || []);
            if (competitions.length > 0) {
                divisione = competitions[0].nome_divisione || "Divisione";
                document.getElementById("division-title").innerHTML = `<h2>Divisione: ${divisione}</h2>`;
                displayCompetitions(competitions);
            } else {
                showError("competitions-list", "Nessuna competizione trovata.");
            }
        } catch (err) {
            showError("competitions-list", err.message);
        }
    }

    function displayCompetitions(competitions) {
        const list = document.getElementById("competitions-list");
        list.innerHTML = competitions.map(c => `
            <div class="competition-item" id="comp-${c.id}" onclick="loadTeams(${c.id}, '${c.nome_competizione}')">
                ${c.nome_competizione}
            </div>
        `).join('');
    }

    async function loadTeams(idComp, nomeComp) {
        if (activeCompetitionId) {
            document.getElementById(`comp-${activeCompetitionId}`).classList.remove("active");
        }
        document.getElementById(`comp-${idComp}`).classList.add("active");
        activeCompetitionId = idComp;
        document.getElementById("teams-container").innerHTML = loadingSpinner();
        document.getElementById("division-title").innerHTML = `<h2>Divisione: ${divisione}</h2><h4>Competizione: ${nomeComp}</h4>`;

        try {
            const res = await fetch(`https://barrettasalvatore.it/endpoint/partecipazione/read.php?id_competizione=${idComp}`);
            const data = await res.json();
            const teams = data.squadre || [];
            SQUADRE_CACHE.length = 0;
            SQUADRE_CACHE.push(...teams);
            displayTeams(teams);
        } catch (err) {
            showError("teams-container", err.message);
        }
    }

    function displayTeams(teams) {
        const container = document.getElementById("teams-container");
        if (teams.length === 0) {
            container.innerHTML = `<div class="alert alert-info">Nessuna squadra partecipante.</div>`;
            return;
        }

        container.innerHTML = teams.map(team => `
            <div class="col-md-4">
                <div class="card team-card">
                    <div class="card-body">
                        <h5 class="card-title">${team.nome_squadra}</h5>
                        <p><strong>Presidente:</strong> ${team.presidente || 'N/A'}</p>
                        ${team.vicepresidente ? `<p><strong>Vicepresidente:</strong> ${team.vicepresidente}</p>` : ''}
                        <button class="btn btn-primary btn-sm" onclick="visualizzaDettagli(${team.id})">Dettagli</button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function loadingSpinner() {
        return `<div class="loading"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Caricamento...</span></div></div>`;
    }

    function showError(containerId, msg) {
        document.getElementById(containerId).innerHTML = `<div class="alert alert-danger">Errore: ${msg}</div>`;
    }

    async function visualizzaDettagli(id) {
        const modale = document.getElementById('modaleDettagli');
        const contenuto = document.getElementById('contenutoModale');
        contenuto.innerHTML = "<p>Caricamento dettagli...</p>";
        modale.style.display = 'block';

        // Trova i dati della squadra salvati prima
        const squadra = SQUADRE_CACHE.find(sq => sq.id == id);


        Promise.all([
            fetch(`https://barrettasalvatore.it/endpoint/associazioni/read.php?id_squadra=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/albo/read.php?id_squadra=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/scambi/read.php?id=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/settore_giovanile/read.php?id_squadra=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/finanze_squadra/read.php?id_squadra=${id}`).then(res => res.json())
        ])
            .then(([calciatori, albo, scambi, settoreGiovanile, finanzeData]) => {
                let html = `<h2>Dettagli Squadra: ${squadra.nome_squadra}</h2>`;

                html += `<p><strong>Valore FVM rosa:</strong> ${squadra.valore_fvm} </p>`;

                html += "<h3>💰 Finanze del Club:</h3>";
                const finanze = finanzeData.finanze_squadra?.[0];
                if (finanze) {
                    html += `<ul style='list-style: none; padding-left: 0;'>
        <li><strong>Stadio League:</strong> ${finanze.guadagno_crediti_stadio_league} 💰</li>
        <li><strong>Stadio Cup:</strong> ${finanze.guadagno_crediti_stadio_cup} 💰</li>
        <li><strong>Premi League:</strong> ${finanze.premi_league} 💰</li>
        <li><strong>Premi Cup:</strong> ${finanze.premi_cup} 💰</li>
        <li><strong>Prequalifiche UEFA (Stadio):</strong> ${finanze.prequalifiche_uefa_stadio} 💰</li>
        <li><strong>Prequalifiche UEFA (Premio):</strong> ${finanze.prequalifiche_uefa_premio} 💰</li>
        <li><strong>Competizioni UEFA (Stadio):</strong> ${finanze.competizioni_uefa_stadio} 💰</li>
        <li><strong>Competizioni UEFA (Premio):</strong> ${finanze.competizioni_uefa_premio} 💰</li>
        <li><strong>Crediti Residui in Cassa:</strong> ${finanze.crediti_residui_cassa} 💰</li>
        <li><strong>Totale Bilancio:</strong> <strong>${finanze.totale_crediti_bilancio} 💰</strong></li>
        <li><strong>Punteggio Ranking:</strong> ${finanze.punteggio_ranking}</li>
    </ul>`;
                } else {
                    html += "<p>Dati finanziari non disponibili.</p>";
                }


                html += "<h3>🏟️ Informazioni Stadio:</h3>";
                html += `<ul style='list-style: none; padding-left: 0;'>
                    <li><strong>Nome:</strong> ${squadra.stadio?.trim() || "N/D"}</li>
                    <li><strong>Livello:</strong> ${squadra.livello_stadio}</li>
                    <li><strong>Manutenzione:</strong> ${squadra.costo_manutenzione} 💰</li>
                    <li><strong>Bonus casa Nazionale:</strong> ${squadra.bonus_casa_nazionale} 💰</li>
                    <li><strong>Bonus casa Uefa:</strong> ${squadra.bonus_casa_uefa} 💰</li>
                 </ul>`;

                const ruoli = { P: [], D: [], C: [], A: [], Altro: [] };
                if (calciatori.associazioni?.length) {
                    calciatori.associazioni.forEach(a => {
                        const ruolo = a.ruolo_calciatore || "Altro";
                        (ruoli[ruolo] || ruoli.Altro).push(a);
                    });
                }

                html += "<h3>⚽ Giocatori:</h3>";
                for (const [ruolo, lista] of Object.entries(ruoli)) {
                    if (!lista.length) continue;
                    html += `<h4>${ruolo} (${lista.length})</h4><ul style='list-style: none; padding-left: 0;'>`;
                    lista.forEach(a => {
                        html += `<li><strong>${a.nome_calciatore}</strong> – 💰 ${a.costo_calciatore} crediti - ${a.eta} anni - ${a.fvm} fvm</li>`;
                    });
                    html += "</ul>";
                }

                html += "<h3>🏆 Albo d'Oro:</h3><ul style='list-style: none; padding-left: 0;'>";
                if (albo.albo?.length) {
                    albo.albo.forEach(c => {
                        html += `<li>🥇 ${c.nome_competizione} - ${c.anno}</li>`;
                    });
                } else {
                    html += "<li>Nessuna coppa registrata.</li>";
                }
                html += "</ul>";

                html += "<h3>🧒 Settore Giovanile:</h3>";
                if (settoreGiovanile.settore_giovanile?.length) {
                    html += `<ul style='list-style: none; padding-left: 0;'>`;
                    settoreGiovanile.settore_giovanile.forEach(g => {
                        const nome = g.nome_calciatore || 'Giocatore sconosciuto';
                        const stagione = g.stagione || 'N/D';
                        const fuoriListone = g.fuori_listone == 1 ? "❌ Fuori listone" : "✅ Nel listone";
                        const primaSquadra = g.prima_squadra == 1 ? "✅ Aggregato alla prima squadra" : "❌ Non aggregato alla prima squadra";

                        html += `<li><strong>${nome}</strong> – 🗓️ Stagione: ${stagione} – ${fuoriListone} – ${primaSquadra}</li>`;
                    });
                    html += "</ul>";
                } else {
                    html += "<p>Nessun calciatore presente nel settore giovanile.</p>";
                }


                html += "<h3>🔁 Trattative recenti:</h3>";
                if (scambi.trattive?.length) {
                    scambi.trattive.forEach(trattativa => {
                        html += `<div style="margin-bottom: 1em; border: 1px solid #ccc; border-radius: 8px; padding: 10px;">
                            <p><strong>📝 Descrizione:</strong><br>${trattativa.descrizione}</p>
                            <p><strong>📅 Fine:</strong> ${trattativa.data_fine || 'N/D'}</p>`;
                        if (trattativa.scambi?.length) {
                            html += `<table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr><th>Calciatore</th><th>Da</th><th>A</th><th>Credito</th></tr>
                                </thead><tbody>`;
                            trattativa.scambi.forEach(s => {
                                html += `<tr>
                            <td>${s.nome_calciatore || '💸 Solo credito'}</td>
                            <td>${s.nome_squadra_cedente}</td>
                            <td>${s.nome_squadra_ricevente}</td>
                            <td>${s.credito_debito} 💰</td>
                        </tr>`;
                            });
                            html += "</tbody></table>";
                        } else {
                            html += "<p>Nessuno scambio registrato.</p>";
                        }
                        html += "</div>";
                    });
                } else {
                    html += "<p>Nessuna trattativa recente.</p>";
                }

                contenuto.innerHTML = html;
            })
            .catch(error => {
                contenuto.innerHTML = `<p class='error'>Errore: ${error.message}</p>`;
            });
    }
</script>
</body>
</html>
