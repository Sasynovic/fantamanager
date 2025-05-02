<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Squadre in Vendita</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 40px;
            background: linear-gradient(135deg, #1e1e2f, #323251);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff; display: flex; flex-direction: column; align-items: center;
        }

        h1 {
            font-size: 2em; margin-bottom: 30px;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }

        .table-container {
            width: 90%; max-width: 1000px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px; padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: collapse; color: #fff; }
        th, td { padding: 12px 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.2); }
        th { background-color: rgba(255, 255, 255, 0.15); font-weight: 600; }
        tr:hover { background-color: rgba(255, 255, 255, 0.05); }

        .loading, .error {
            font-size: 1.2em;
            text-align: center;
        }

        button {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 10px;
            padding: 8px 12px;
            margin: 0 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover { background: rgba(255, 255, 255, 0.3); }

        .modal {
            display: none; position: fixed; z-index: 999;
            left: 0; top: 0; width: 100%; height: 100%;
            overflow: auto; background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            background-color: #29293d; margin: 10% auto;
            padding: 20px; border: 1px solid #888;
            width: 80%; max-width: 700px;
            border-radius: 15px; color: #fff;
        }

        .modal-content h2 { margin-top: 0; }
        .close {
            color: #aaa; float: right;
            font-size: 28px; font-weight: bold;
        }

        .close:hover, .close:focus { color: #fff; text-decoration: none; cursor: pointer; }

        .filters {
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .filters label {
            display: flex;
            flex-direction: column;
            font-weight: bold;
        }

        .filters select, .filters input {
            padding: 6px 8px;
            border-radius: 6px;
            border: none;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<button onclick="window.location.href='index.php'">Torna alla Home</button>
<h1>🏆 Squadre attualmente in vendita</h1>

<div class="filters">
    <label>
        Prezzo max (€)
        <input type="number" id="filtroPrezzo" min="0" />
    </label>
    <label>
        Rate
        <select id="filtroRate">
            <option value="">Tutte</option>
            <option value="5">★★★★★</option>
            <option value="4">★★★★</option>
            <option value="3">★★★</option>
            <option value="2">★★</option>
            <option value="1">★</option>
        </select>
    </label>
    <label>
        Lega
        <select id="filtroLega">
            <option value="">Tutte</option>
        </select>
    </label>
</div>

<div class="table-container">
    <div id="output" class="loading">Caricamento in corso...</div>
</div>

<div id="modaleDettagli" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modaleDettagli').style.display='none'">&times;</span>
        <div id="contenutoModale"></div>
    </div>
</div>

<script src="script/vendita.js" defer></script>

<script>
    let tutteLeSquadre = [];

    function generaStelle(rate) {
        let stelle = '';
        for (let i = 1; i <= 5; i++) {
            stelle += i <= rate ? '★' : '☆';
        }
        return `<span style="color: gold; font-size: 1.2em;">${stelle}</span>`;
    }

    function visualizzaDettagli(id) {
        const modale = document.getElementById('modaleDettagli');
        const contenuto = document.getElementById('contenutoModale');
        contenuto.innerHTML = "<p>Caricamento dettagli...</p>";
        modale.style.display = 'block';

        Promise.all([
            fetch(`https://barrettasalvatore.it/endpoint/associazioni/read.php?id_squadra=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/albo/read.php?id_squadra=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/scambi/read.php?id=${id}`).then(res => res.json())
        ])
            .then(([calciatori, albo, scambi]) => {
                let html = `<h2>Dettagli Squadra</h2>`;
                const ruoli = { P: [], D: [], C: [], A: [], Altro: [] };
                calciatori.associazioni?.forEach(a => {
                    const ruolo = a.ruolo_calciatore || "Altro";
                    (ruoli[ruolo] || ruoli.Altro).push(a);
                });

                html += "<h3>⚽ Giocatori:</h3>";
                for (const [ruolo, lista] of Object.entries(ruoli)) {
                    if (!lista.length) continue;
                    html += `<h4>${ruolo} (${lista.length})</h4><ul>`;
                    lista.forEach(a => {
                        html += `<li><strong>${a.nome_calciatore}</strong> – 💰 ${a.costo_calciatore} crediti - ${a.eta} anni - ${a.fvm} fvm</li>`;
                    });
                    html += "</ul>";
                }

                html += "<h3>🏆 Albo d'Oro:</h3><ul>";
                albo.albo?.length
                    ? albo.albo.forEach(c => html += `<li>🥇 ${c.nome_competizione} - ${c.anno}</li>`)
                    : html += "<li>Nessuna coppa registrata.</li>";
                html += "</ul>";

                html += "<h3>🔁 Trattative recenti:</h3>";
                scambi.trattive?.length
                    ? scambi.trattive.forEach(trattativa => {
                        html += `<div style="margin-bottom: 1em;"><p><strong>📝</strong> ${trattativa.descrizione}</p>`;
                        html += `<p><strong>📅 Fine:</strong> ${trattativa.data_fine || 'N/D'}</p>`;
                        if (trattativa.scambi?.length) {
                            html += `<table><thead><tr><th>Calciatore</th><th>Da</th><th>A</th><th>Credito</th></tr></thead><tbody>`;
                            trattativa.scambi.forEach(s => {
                                html += `<tr><td>${s.nome_calciatore ?? '💸 Solo credito'}</td><td>${s.nome_squadra_cedente}</td><td>${s.nome_squadra_ricevente}</td><td>${s.credito_debito} 💰</td></tr>`;
                            });
                            html += `</tbody></table>`;
                        } else {
                            html += `<p>Nessuno scambio registrato.</p>`;
                        }
                        html += `</div>`;
                    })
                    : html += "<p>Nessuna trattativa recente.</p>";

                contenuto.innerHTML = html;
            })
            .catch(error => {
                contenuto.innerHTML = `<p class='error'>Errore: ${error.message}</p>`;
            });
    }

    function aggiornaTabella() {
        const filtroPrezzo = parseFloat(document.getElementById("filtroPrezzo").value) || Infinity;
        const filtroRate = document.getElementById("filtroRate").value;
        const filtroLega = document.getElementById("filtroLega").value;

        const container = document.getElementById("output");
        const filtrate = tutteLeSquadre.filter(s => {
            const prezzoValido = parseFloat(s.prezzo) <= filtroPrezzo;
            const rateValido = filtroRate === "" || s.rate == filtroRate;
            const legaValida = filtroLega === "" || s.lega === filtroLega;
            return prezzoValido && rateValido && legaValida;
        });

        if (!filtrate.length) {
            container.innerHTML = "<p class='error'>Nessuna squadra corrisponde ai filtri selezionati.</p>";
            return;
        }

        let tabella = `<table>
            <thead>
                <tr>
                    <th>Nome Squadra</th>
                    <th>Stadio</th>
                    <th>Rate</th>
                    <th>Prezzo</th>
                    <th>Azioni</th>
                </tr>
            </thead><tbody>`;

        filtrate.forEach(sq => {
            const linkWhatsapp = `https://wa.me/+393371447208?text=Salve, vorrei info per acquistare la squadra ${encodeURIComponent(sq.nome_squadra)}.`;
            tabella += `<tr>
                <td>${sq.nome_squadra}</td>
                <td>${sq.stadio}</td>
                <td>${generaStelle(sq.rate)}</td>
                <td>${sq.prezzo}€</td>
                <td>
                    <button onclick="visualizzaDettagli(${sq.id})">Dettagli</button>
                    <a href="${linkWhatsapp}" target="_blank"><button>Contatta</button></a>
                </td>
            </tr>`;
        });

        tabella += "</tbody></table>";
        container.innerHTML = tabella;
    }

    fetch("https://barrettasalvatore.it/endpoint/squadra/read.php?vendita=1")
        .then(res => res.json())
        .then(data => {
            tutteLeSquadre = data.squadre || [];
            const legheUniche = [...new Set(tutteLeSquadre.map(s => s.lega).filter(Boolean))];
            const selectLega = document.getElementById("filtroLega");
            legheUniche.forEach(lega => {
                const opt = document.createElement("option");
                opt.value = lega;
                opt.textContent = lega;
                selectLega.appendChild(opt);
            });
            aggiornaTabella();
        })
        .catch(() => {
            document.getElementById("output").innerHTML = "<p class='error'>Errore nel caricamento dei dati.</p>";
        });

    document.getElementById("filtroPrezzo").addEventListener("input", aggiornaTabella);
    document.getElementById("filtroRate").addEventListener("change", aggiornaTabella);
    document.getElementById("filtroLega").addEventListener("change", aggiornaTabella);
</script>
</body>
</html>
