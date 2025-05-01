<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dettagli Squadra</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f7f9fc;
            --card-bg: #ffffff;
            --primary: #0066cc;
            --secondary: #333;
            --accent: #ffaa00;
            --border: #ddd;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--secondary);
            padding: 1rem;
        }

        h2 {
            color: var(--primary);
            margin-top: 0.5rem;
        }

        h3 {
            margin-top: 1.5rem;
            border-left: 4px solid var(--primary);
            padding-left: 0.5rem;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .column {
            background-color: var(--card-bg);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .badge {
            background-color: var(--primary);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-size: 0.85em;
        }

        ul {
            padding-left: 0;
            list-style: none;
        }

        li {
            margin: 0.5rem 0;
        }

        .player {
            padding: 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .player:last-child {
            border-bottom: none;
        }

        .role-block {
            margin-bottom: 1rem;
        }

        .stars {
            color: gold;
            font-size: 1.2rem;
        }

        .error {
            color: red;
        }

        /* Layout orizzontale su desktop */
        @media (min-width: 768px) {
            .wrapper {
                flex-direction: row;
                align-items: flex-start;
            }

            .column {
                flex: 1;
                min-width: 0;
            }
        }
    </style>

<body>
<div class="wrapper" id="contenutoSquadra">
    <p>Caricamento dati della squadra...</p>
</div>

<script>
    function generaStelle(rate) {
        let stelle = '';
        for (let i = 1; i <= 5; i++) {
            stelle += i <= rate ? '★' : '☆';
        }
        return `<span class="stars">${stelle}</span>`;
    }

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    if (!id) {
        document.getElementById('contenutoSquadra').innerHTML = "<p class='error'>ID squadra non fornito.</p>";
    } else {
        Promise.all([
            fetch(`https://barrettasalvatore.it/endpoint/squadra/read.php?id_squadra=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/associazioni/read.php?id_squadra=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/albo/read.php?id_squadra=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/scambi/read.php?id=${id}`).then(res => res.json())
        ])
            .then(([squadraData, calciatori, albo, scambi]) => {
                let info = `<div class="column"><h2>🏟️ ${squadraData.squadra?.nome_squadra || "Sconosciuta"}</h2>
                    <p><strong>Stadio:</strong> ${squadraData.squadra?.stadio || "N/D"}</p>
                    <p><strong>Rate:</strong> ${generaStelle(parseInt(squadraData.squadra?.rate || 0))}</p>
                </div>`;

                const ruoli = { P: [], D: [], C: [], A: [], Altro: [] };
                if (calciatori.associazioni?.length) {
                    calciatori.associazioni.forEach(a => {
                        const ruolo = a.ruolo_calciatore || "Altro";
                        (ruoli[ruolo] || ruoli.Altro).push(a);
                    });
                }

                let giocatori = `<div class="column"><h3>⚽ Giocatori</h3>`;
                for (const [ruolo, lista] of Object.entries(ruoli)) {
                    if (!lista.length) continue;
                    giocatori += `<div class="role-block"><strong>${ruolo}</strong><ul>`;
                    lista.forEach(a => {
                        giocatori += `<li class="player"><strong>${a.nome_calciatore}</strong> – 💰 ${a.costo_calciatore} crediti – ${a.eta} anni – FVM: ${a.fvm}</li>`;
                    });
                    giocatori += "</ul></div>";
                }
                giocatori += "</div>";

                let extra = `<div class="column"><h3>🏆 Albo d'Oro</h3><ul>`;
                if (albo.albo?.length) {
                    albo.albo.forEach(c => {
                        extra += `<li>🥇 ${c.nome_competizione} - ${c.anno}</li>`;
                    });
                } else {
                    extra += "<li>Nessuna coppa registrata.</li>";
                }
                extra += "</ul><h3>🔁 Ultimi Scambi</h3><ul>";
                if (scambi.scambi?.length) {
                    scambi.scambi.forEach(s => {
                        extra += `<li><strong>${s.nome_calciatore}</strong> ${s.tipo === "acquisto" ? "acquistato da" : "ceduto a"} <em>${s.nome_squadra_ricevente}</em> – 📅 ${s.data}</li>`;
                    });
                } else {
                    extra += "<li>Nessuno scambio recente.</li>";
                }
                extra += "</ul></div>";

                document.getElementById('contenutoSquadra').innerHTML = info + giocatori + extra;
            })
            .catch(error => {
                document.getElementById('contenutoSquadra').innerHTML = `<p class='error'>Errore: ${error.message}</p>`;
            });
    }
</script>
</body>
