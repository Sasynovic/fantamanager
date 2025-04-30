<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dettaglio Competizione</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        table {
            width: 100%; border-collapse: collapse; color: #fff;
        }
        th, td {
            padding: 12px 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        th {
            background-color: rgba(255, 255, 255, 0.15); font-weight: 600;
        }
        tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        #modaleDettagli {
            display: none;
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
        }
        #contenutoModale {
            background: #fff; color: #000;
            max-width: 600px; margin: 100px auto; padding: 20px;
            border-radius: 10px; position: relative;
        }
        #contenutoModale button {
            position: absolute; top: 10px; right: 10px;
            background: crimson; color: #fff; border: none;
            padding: 5px 10px; border-radius: 5px;
            cursor: pointer;
        }


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
            border: none; border-radius: 10px;
            padding: 8px 12px; margin: 0 5px;
            color: white; font-weight: bold;
            cursor: pointer; transition: background 0.3s;
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
    </style>
</head>
<body>
<button onclick="window.location.href='index.php'">Torna alla Home</button>

<h1 id="nomeCompetizione">Caricamento...</h1>

<div class="table-container">
    <table id="squadreTable">
        <thead>
        <tr>
            <th>Nome Squadra</th>
            <th>Presidente</th>
            <th>Vicepresidente</th>
            <th>Dettagli</th>
        </tr>
        </thead>
        <tbody>
        <!-- Dati caricati dinamicamente -->
        </tbody>
    </table>
</div>

<div id="modaleDettagli" class="modal" >
    <div id="contenutoModale" class="modal-content">
        <span class="close" onclick="document.getElementById('modaleDettagli').style.display='none'">&times;</span>
        <div id="contenutoModaleTesto">Caricamento...</div>
    </div>
</div>

<script>
    function creaBottone(label, classe = '', onclick = '') {
        return `<button class="${classe}" onclick="${onclick}">${label}</button>`;
    }

    function visualizzaDettagli(id) {
        const modale = document.getElementById('modaleDettagli');
        const contenuto = document.getElementById('contenutoModaleTesto');
        contenuto.innerHTML = "<p>Caricamento dettagli...</p>";
        modale.style.display = 'block';

        Promise.all([
            fetch(`https://barrettasalvatore.it/endpoint/associazioni/read.php?id_squadra=${id}`).then(res => res.json()),
            fetch(`https://barrettasalvatore.it/endpoint/albo/read.php?id_squadra=${id}`).then(res => res.json())
        ])
            .then(([calciatori, albo]) => {
                let html = `<h2>Dettagli Squadra</h2>`;

                const ruoli = { P: [], D: [], C: [], A: [], Altro: [] };

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
                    html += `<h4>${ruolo} (${lista.length})</h4><ul style='list-style: none; padding-left: 0;'>`;
                    lista.forEach(a => {
                        html += `<li style="margin-bottom: 6px;"><strong>${a.nome_calciatore}</strong> – 💰 ${a.costo_calciatore} crediti</li>`;
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
</script>

<script src="script/competizione.js" defer></script>

</body>
</html>
