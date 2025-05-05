<?php
// Verifica che l'ID divisione sia stato fornito
if (!isset($_GET['id_divisione']) || empty($_GET['id_divisione'])) {
    header("Location: index.php");
    exit;
}

$id_divisione = $_GET['id_divisione'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fantacalcio - Dettaglio Divisione</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>

<div class="main-container">
    <aside class="left">
        <div class="heading">
            <h2>Fantacalcio</h2>
            <h3>Fantamanager</h3>
        </div>
        <div class="menu">
            <ul class="menu-list">
                <li class="menu-option" onclick="location.href='index.php'">DASHBOARD</li>
                <li class="menu-option">RICERCA</li>
                <li class="menu-option">ALBO D'ORO</li>
                <li class="menu-option">SQUADRE IN VENDITA</li>
                <li class="menu-option">TOOL SCAMBI</li>
                <li class="menu-option">CONTATTI</li>
            </ul>
        </div>
    </aside>

    <div class="actual-selection">
        <header id="divisione-header">
            <div id="header-competizioni"></div>
        </header>

        <div class="tabs">
            <div class="tab" data-tab="squadre">Squadre</div>
            <div class="tab" data-tab="classifica">Classifica</div>
            <div class="tab" data-tab="news">News</div>
        </div>

        <div class="tab-content" id="classifica">
            <div id="competizione-selector-classifica" class="competizione-selector">
                <label for="select-competizione-classifica">Seleziona competizione:</label>
                <select id="select-competizione-classifica" onchange="caricaClassifica(this.value)">
                    <option value="">Caricamento...</option>
                </select>
            </div>
            <table class="classifica-table">
                <thead>
                <tr>
                    <th>Pos</th>
                    <th>Squadra</th>
                    <th>PT</th>
                    <th>G</th>
                    <th>V</th>
                    <th>N</th>
                    <th>P</th>
                    <th>GF</th>
                    <th>GS</th>
                    <th>DR</th>
                </tr>
                </thead>
                <tbody id="classifica-body">
                <tr>
                    <td colspan="10" class="loading">Seleziona una competizione</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="tab-content" id="squadre">
            <div id="competizione-selector-squadre" class="competizione-selector">
                <label for="select-competizione-squadre">Seleziona competizione:</label>
                <select id="select-competizione-squadre" onchange="caricaSquadreCompetizione(this.value)">
                    <option value="">Caricamento...</option>
                </select>
            </div>
            <div class="grid" id="squadre-container">
                <div class="loading">Seleziona una competizione</div>
            </div>
        </div>

        <div class="tab-content" id="news">
            <div id="competizione-selector-news" class="competizione-selector">
                <label for="select-competizione-news">Seleziona competizione:</label>
                <select id="select-competizione-news" onchange="caricaNews(this.value)">
                    <option value="">Caricamento...</option>
                </select>
            </div>
            <div id="news-container">
                <div class="loading">Seleziona una competizione</div>
            </div>
        </div>

        </main>
    </div>
</div>

<script>
    // Variabile globale per memorizzare le competizioni
    let competizioni = [];
    const idDivisione = <?php echo $id_divisione; ?>;

    // Funzione per caricare i dettagli della divisione
    // Funzione per caricare le competizioni della divisione
    async function caricaCompetizioni(idDivisione) {
        const container = document.getElementById("header-competizioni");
        const selectClassifica = document.getElementById("select-competizione-classifica");
        const selectSquadre = document.getElementById("select-competizione-squadre");
        const selectNews = document.getElementById("select-competizione-news");

        try {
            const res = await fetch(`https://barrettasalvatore.it/endpoint/competizione/read.php?id_divisione=${idDivisione}`);
            const data = await res.json();

            if (!data.competizioni || data.competizioni.length === 0) {
                container.innerHTML = "<p>Nessuna competizione trovata in questa divisione.</p>";
                return;
            }

            // Memorizzare le competizioni
            competizioni = data.competizioni;

            // Popolare il container principale delle competizioni
            container.innerHTML = "";
            competizioni.forEach(comp => {
                container.innerHTML += `
                    <div class="competizione-card" onclick="selezionaCompetizione(${comp.id})">
                        <div class="competizione-header">
                            <div class="competizione-title">${comp.nome_competizione}</div>
                        </div>
                    </div>
                `;
            });

            // Popolare i selettori delle competizioni
            const selectHTML = `
                <option value="">Seleziona competizione</option>
                ${competizioni.map(comp => `
                    <option value="${comp.id}">${comp.nome_divisione} - ${comp.nome_competizione}</option>
                `).join('')}
            `;

            selectClassifica.innerHTML = selectHTML;
            selectSquadre.innerHTML = selectHTML;
            selectNews.innerHTML = selectHTML;

        } catch (err) {
            container.innerHTML = "<p>Errore nel caricamento delle competizioni.</p>";
            console.error(err);
        }
    }

    // Funzione per selezionare una competizione e mostrare le squadre partecipanti
    function selezionaCompetizione(idCompetizione) {
        // Attivare la tab squadre
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelector('.tab[data-tab="squadre"]').classList.add('active');

        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.getElementById('squadre').classList.add('active');

        // Selezionare la competizione nel dropdown
        document.getElementById("select-competizione-squadre").value = idCompetizione;
        document.getElementById("select-competizione-classifica").value = idCompetizione;
        document.getElementById("select-competizione-news").value = idCompetizione;

        // Caricare le squadre della competizione
        caricaSquadreCompetizione(idCompetizione);
        caricaClassifica(idCompetizione);
        caricaNews(idCompetizione);
    }

    async function caricaInfoSquadra(idSquadra) {
        const container = document.getElementById("squadre-container");
        container.innerHTML = "<div class='loading'>Caricamento info squadra...</div>";

        try {
            // Prima carichiamo i dati base della squadra
            const resSquadra = await fetch(`https://barrettasalvatore.it/endpoint/squadra/read.php?id_squadra=${idSquadra}`);
            const dataSquadra = await resSquadra.json();

            console.log("Risposta fetch squadra:", dataSquadra);

            if (!dataSquadra.squadre || dataSquadra.squadre.length === 0) {
                console.warn("Nessuna squadra trovata con ID:", idSquadra);
                container.innerHTML = "<p>Nessuna squadra trovata.</p>";
                return;
            }

            const squadra = dataSquadra.squadre[0];
            console.log("Dati squadra:", squadra);

            // Ora carichiamo i dati aggiuntivi
            const [associazioni, albo, scambi, settoreGiovanile, finanzeData] = await Promise.all([
                fetch(`https://barrettasalvatore.it/endpoint/associazioni/read.php?id_squadra=${idSquadra}`).then(res => res.json()),
                fetch(`https://barrettasalvatore.it/endpoint/albo/read.php?id_squadra=${idSquadra}`).then(res => res.json()),
                fetch(`https://barrettasalvatore.it/endpoint/scambi/read.php?id=${idSquadra}`).then(res => res.json()),
                fetch(`https://barrettasalvatore.it/endpoint/settore_giovanile/read.php?id_squadra=${idSquadra}`).then(res => res.json()),
                fetch(`https://barrettasalvatore.it/endpoint/finanze_squadra/read.php?id_squadra=${idSquadra}`).then(res => res.json())
            ]);

            // Costruiamo l'HTML con tutti i dati
            let html = `
            <div style="grid-column: 1 / -1">
                <h2>${squadra.nome_squadra}</h2>

                <div class="team-card" style="margin-bottom: 2rem;">
                    <div class="team-header">
                        <div>
                            <div class="team-name">${squadra.nome_squadra}</div>
                            <div class="team-manager">Presidente: ${squadra.presidente || 'Non specificato'}</div>
                            ${squadra.vicepresidente && squadra.vicepresidente.trim() ? `<div class="team-manager">Vice: ${squadra.vicepresidente}</div>` : ''}
                            <div class="team-stadium">Stadio: ${squadra.stadio?.trim() || 'N/D'}</div>
                            <div class="team-value">Valore FVM: ${squadra.valore_fvm || 'N/D'}</div>
                        </div>
                    </div>
                </div>

                <h3>💰 Finanze del Club:</h3>`;

            const finanze = finanzeData.finanze_squadra?.[0];
            if (finanze) {
                html += `<div class="team-card" style="margin-bottom: 2rem;">
                <ul style='list-style: none; padding-left: 0;'>
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
                </ul>
            </div>`;
            } else {
                html += "<p>Dati finanziari non disponibili.</p>";
            }

            html += `<h3>🏟️ Informazioni Stadio:</h3>
            <div class="team-card" style="margin-bottom: 2rem;">
                <ul style='list-style: none; padding-left: 0;'>
                    <li><strong>Nome:</strong> ${squadra.stadio?.trim() || "N/D"}</li>
                    <li><strong>Livello:</strong> ${squadra.livello_stadio || "N/D"}</li>
                    <li><strong>Manutenzione:</strong> ${squadra.costo_manutenzione || "N/D"} 💰</li>
                    <li><strong>Bonus casa Nazionale:</strong> ${squadra.bonus_casa_nazionale || "N/D"} 💰</li>
                    <li><strong>Bonus casa Uefa:</strong> ${squadra.bonus_casa_uefa || "N/D"} 💰</li>
                </ul>
            </div>`;

            // Sezione giocatori
            const ruoli = { P: [], D: [], C: [], A: [], Altro: [] };
            if (associazioni.associazioni?.length) {
                associazioni.associazioni.forEach(a => {
                    const ruolo = a.ruolo_calciatore || "Altro";
                    (ruoli[ruolo] || ruoli.Altro).push(a);
                });
            }

            html += "<h3>⚽ Giocatori:</h3>";
            for (const [ruolo, lista] of Object.entries(ruoli)) {
                if (!lista.length) continue;
                html += `<div class="team-card" style="margin-bottom: 1rem;">
                <h4>${ruolo} (${lista.length})</h4>
                <ul style='list-style: none; padding-left: 0;'>`;
                lista.forEach(a => {
                    html += `<li><strong>${a.nome_calciatore}</strong> – 💰 ${a.costo_calciatore} crediti - ${a.eta} anni - ${a.fvm} fvm</li>`;
                });
                html += "</ul></div>";
            }

            // Albo d'oro
            html += `<h3>🏆 Albo d'Oro:</h3>
            <div class="team-card" style="margin-bottom: 2rem;">
                <ul style='list-style: none; padding-left: 0;'>`;
            if (albo.albo?.length) {
                albo.albo.forEach(c => {
                    html += `<li>🥇 ${c.nome_competizione} - ${c.anno}</li>`;
                });
            } else {
                html += "<li>Nessuna coppa registrata.</li>";
            }
            html += "</ul></div>";

            // Settore giovanile
            html += "<h3>🧒 Settore Giovanile:</h3>";
            if (settoreGiovanile.settore_giovanile?.length) {
                html += `<div class="team-card" style="margin-bottom: 2rem;">
                <ul style='list-style: none; padding-left: 0;'>`;
                settoreGiovanile.settore_giovanile.forEach(g => {
                    const nome = g.nome_calciatore || 'Giocatore sconosciuto';
                    const stagione = g.stagione || 'N/D';
                    const fuoriListone = g.fuori_listone == 1 ? "❌ Fuori listone" : "✅ Nel listone";
                    const primaSquadra = g.prima_squadra == 1 ? "✅ Aggregato alla prima squadra" : "❌ Non aggregato alla prima squadra";

                    html += `<li><strong>${nome}</strong> – 🗓️ Stagione: ${stagione} – ${fuoriListone} – ${primaSquadra}</li>`;
                });
                html += "</ul></div>";
            } else {
                html += "<p>Nessun calciatore presente nel settore giovanile.</p>";
            }

            // Trattative
            html += "<h3>🔁 Trattative recenti:</h3>";
            if (scambi.trattive?.length) {
                scambi.trattive.forEach(trattativa => {
                    html += `<div class="team-card" style="margin-bottom: 1rem;">
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

            container.innerHTML = html;

        } catch (err) {
            console.error("Errore nel caricamento delle info squadra:", err);
            container.innerHTML = `<div class="error-message">Errore nel caricamento dei dettagli della squadra: ${err.message}</div>`;
        }
    }


    // Funzione per caricare le squadre di una competizione
    async function caricaSquadreCompetizione(idCompetizione) {
        if (!idCompetizione) {
            document.getElementById("squadre-container").innerHTML = "<p>Seleziona una competizione</p>";
            return;
        }

        const container = document.getElementById("squadre-container");
        container.innerHTML = "<div class='loading'>Caricamento squadre...</div>";

        try {
            const res = await fetch(`https://barrettasalvatore.it/endpoint/partecipazione/read.php?id_competizione=${idCompetizione}`);
            const data = await res.json();

            if (!data.squadre || data.squadre.length === 0) {
                container.innerHTML = "<p>Nessuna squadra trovata in questa competizione.</p>";
                return;
            }

            // Trovare il nome della competizione
            const nomeCompetizione = data.nome_competizione || "Competizione";

            // Creare un titolo per la sezione
            container.innerHTML = `
                <h3 style="grid-column: 1 / -1">${nomeCompetizione}</h3>
            `;

            // Aggiungere le squadre
            data.squadre.forEach(squadra => {
                container.innerHTML += `
        <div class="team-card">
            <div class="team-header">
                <div>
                    <div class="team-name">${squadra.nome_squadra}</div>
                    <div class="team-manager">Presidente: ${squadra.presidente || 'Non specificato'}</div>
                    ${squadra.vicepresidente && squadra.vicepresidente.trim() ? `<div class="team-manager">Vice: ${squadra.vicepresidente}</div>` : ''}
                </div>
                <div class="team-details">
                    <button onclick="caricaInfoSquadra(${squadra.id})" class="btn">Dettagli</button>
                </div>
            </div>
        </div>
    `;
            });


        } catch (err) {
            container.innerHTML = "<p>Errore nel caricamento delle squadre.</p>";
            console.error(err);
        }
    }

    // Funzione per caricare la classifica di una competizione
    async function caricaClassifica(idCompetizione) {
        if (!idCompetizione) {
            document.getElementById("classifica-body").innerHTML = "<tr><td colspan='10'>Seleziona una competizione</td></tr>";
            return;
        }

        const tableBody = document.getElementById("classifica-body");
        tableBody.innerHTML = "<tr><td colspan='10' class='loading'>Caricamento classifica...</td></tr>";

        try {
            const res = await fetch(`https://barrettasalvatore.it/endpoint/partecipazione/read.php?id_competizione=${idCompetizione}`);
            const data = await res.json();

            if (!data.squadre || data.squadre.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='10'>Nessun dato di classifica disponibile.</td></tr>";
                return;
            }

            tableBody.innerHTML = "";
            data.squadre.forEach((squadra, index) => {
                tableBody.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <div class="team-row">
                            <img src="${squadra.logo ? `https://barrettasalvatore.it/public/team/${squadra.logo}` : 'https://via.placeholder.com/24?text=NA'}" alt="${squadra.nome_squadra}">
                            ${squadra.nome_squadra}
                        </div>
                    </td>
                    <td>${squadra.punti || '0'}</td>
                    <td>${squadra.giocate || '0'}</td>
                    <td>${squadra.vittorie || '0'}</td>
                    <td>${squadra.pareggi || '0'}</td>
                    <td>${squadra.sconfitte || '0'}</td>
                    <td>${squadra.gol_fatti || '0'}</td>
                    <td>${squadra.gol_subiti || '0'}</td>
                    <td>${squadra.differenza_reti || '0'}</td>
                </tr>
            `;
            });

        } catch (err) {
            tableBody.innerHTML = "<tr><td colspan='10'>Errore nel caricamento della classifica.</td></tr>";
            console.error(err);
        }
    }

    // Funzione per caricare le news di una competizione
    async function caricaNews(idCompetizione) {
        if (!idCompetizione) {
            document.getElementById("news-container").innerHTML = "<p>Seleziona una competizione</p>";
            return;
        }

        const container = document.getElementById("news-container");
        container.innerHTML = "<div class='loading'>Caricamento news...</div>";

        try {
            const res = await fetch(`https://barrettasalvatore.it/endpoint/news/read.php?id_competizione=${idCompetizione}`);
            const data = await res.json();

            if (!data.news || data.news.length === 0) {
                container.innerHTML = "<p>Nessuna news trovata per questa competizione.</p>";
                return;
            }

            // Ordinare le news per data (più recenti prima)
            const newsOrdinati = data.news.sort((a, b) => {
                return new Date(b.data_pubblicazione) - new Date(a.data_pubblicazione);
            });

            // Creare il contenitore delle news
            container.innerHTML = `<div class="news-list"></div>`;
            const newsList = container.querySelector('.news-list');

            // Formattazione della data
            const formatData = (dataStr) => {
                const data = new Date(dataStr);
                const giorno = data.getDate().toString().padStart(2, '0');
                const mese = (data.getMonth() + 1).toString().padStart(2, '0');
                const anno = data.getFullYear();
                const ore = data.getHours().toString().padStart(2, '0');
                const minuti = data.getMinutes().toString().padStart(2, '0');
                return `${giorno}/${mese}/${anno} ${ore}:${minuti}`;
            };

            // Aggiungere le news
            newsOrdinati.forEach(news => {
                newsList.innerHTML += `
                    <div class="news-item">
                        <div class="news-header">
                            <h3>${news.titolo}</h3>
                            <div class="news-meta">
                                <span class="news-date">${formatData(news.data_pubblicazione)}</span>
                                ${news.autore ? `<span class="news-author">Autore: ${news.autore}</span>` : ''}
                            </div>
                        </div>
                        <div class="news-content">
                            ${news.contenuto}
                        </div>
                    </div>
                `;
            });

        } catch (err) {
            container.innerHTML = "<p>Errore nel caricamento delle news.</p>";
            console.error(err);
        }
    }

    // Gestione delle tabs
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            // Rimuovere la classe active da tutte le tabs
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            // Aggiungere la classe active alla tab cliccata
            tab.classList.add('active');

            // Nascondere tutti i contenuti delle tabs
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            // Mostrare il contenuto della tab corrispondente
            document.getElementById(tab.getAttribute('data-tab')).classList.add('active');
        });
    });

    // Caricare i dati quando la pagina è pronta
    document.addEventListener('DOMContentLoaded', () => {
        caricaCompetizioni(idDivisione);
    });
</script>

</body>
</html>