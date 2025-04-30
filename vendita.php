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

        .ruolo-badge {
            padding: 3px 8px; border-radius: 12px;
            font-size: 0.85em; font-weight: bold; color: #fff;
        }

        .portiere { background-color: #3e8ed0; }
        .difensore { background-color: #2ecc71; }
        .centrocampista { background-color: #f39c12; }
        .attaccante { background-color: #e74c3c; }
        .altro { background-color: #888; }

        button {
            background: rgba(255, 255, 255, 0.15);
            border: none; border-radius: 10px;
            padding: 8px 12px; margin: 0 5px;
            color: white; font-weight: bold;
            cursor: pointer; transition: background 0.3s;
        }

        button:hover { background: rgba(255, 255, 255, 0.3); }

        /* Modale */
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
    </style>
</head>
<body>

<h1>🏆 Squadre attualmente in vendita</h1>

<div class="table-container">
    <div id="output" class="loading">Caricamento in corso...</div>
</div>

<div id="modaleDettagli" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modaleDettagli').style.display='none'">&times;</span>
        <div id="contenutoModale"></div>
    </div>
</div>

<script>
    function generaStelle(rate) {
        let stelle = '';
        for (let i = 1; i <= 5; i++) {
            stelle += i <= rate ? '★' : '☆';
        }
        return `<span style="color: gold; font-size: 1.2em;">${stelle}</span>`;
    }

    function creaBottone(label, classe = '', onclick = '') {
        return `<button class="${classe}" onclick="${onclick}">${label}</button>`;
    }

    function visualizzaDettagli(id) {
        const modale = document.getElementById('modaleDettagli');
        const contenuto = document.getElementById('contenutoModale');
        contenuto.innerHTML = "<p>Caricamento dettagli...</p>";
        modale.style.display = 'block';

        Promise.all([
            fetch(`https://barrettasalvatore.it/endpoint/associazioni/read.php?id_squadra=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/albo/read.php?id_squadra=${id}`).then(res => res.json())
        ])
            .then(([calciatori, albo]) => {
                let html = `<h2>Dettagli Squadra</h2>`;

                const ruoli = {
                    P: [],
                    D: [],
                    C: [],
                    A: [],
                    Altro: []
                };

                // Raggruppa calciatori per ruolo
                if (calciatori.associazioni?.length) {
                    calciatori.associazioni.forEach(a => {
                        const ruolo = a.ruolo_calciatore || "Altro";
                        if (ruoli[ruolo]) {
                            ruoli[ruolo].push(a);
                        } else {
                            ruoli.Altro.push(a);
                        }
                    });
                }

                html += "<h3>⚽ Giocatori:</h3>";
                for (const [ruolo, lista] of Object.entries(ruoli)) {
                    if (lista.length === 0) continue;

                    let classe = ruolo.toLowerCase();
                    html += `<h4 class="${classe}">${ruolo} (${lista.length})</h4><ul style='list-style: none; padding-left: 0;'>`;

                    lista.forEach(a => {
                        html += `<li style="margin-bottom: 6px;">
                <span style="font-weight: bold;">${a.nome_calciatore}</span> –💰 ${a.costo_calciatore} crediti
            </li>`;
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

                contenuto.innerHTML = html;
            })
            .catch(err => {
                contenuto.innerHTML = `<p>Errore nel caricamento dei dettagli: ${err}</p>`;
            });
    }

    fetch("https://barrettasalvatore.it/endpoint/squadra/read.php?vendita=1")
        .then(response => {
            if (!response.ok) throw new Error("Errore nel recupero dei dati.");
            return response.json();
        })
        .then(data => {
            const container = document.getElementById("output");
            if (!data.squadre?.length) {
                container.innerHTML = "<p class='error'>Nessuna squadra in vendita.</p>";
                return;
            }

            let tabella = `<table>
                    <thead>
                        <tr>
                            <th>Nome Squadra</th>
                            <th>Stadio</th>
                            <th>Rate</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>`;

            data.squadre.forEach(sq => {
                const rate = parseInt(sq.rate) || 0;
                const id = sq.id;
                tabella += `<tr>
                        <td>${sq.nome_squadra}</td>
                        <td>${sq.stadio}</td>
                        <td>${generaStelle(rate)}</td>
                        <td>
                            ${creaBottone("Acquista")}
                            ${creaBottone("Dettagli", '', `visualizzaDettagli(${id})`)}
                        </td>
                    </tr>`;
            });

            tabella += "</tbody></table>";
            container.innerHTML = tabella;
        })
        .catch(error => {
            document.getElementById("output").innerHTML = `<p class='error'>Errore: ${error.message}</p>`;
        });
</script>

</body>
</html>
