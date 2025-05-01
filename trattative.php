<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Trattative di Mercato</title>
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

        select {
            padding: 10px; margin-bottom: 20px;
            border-radius: 10px; border: none;
            color: white; font-weight: bold;
            background: black;
        }
    </style>
</head>
<body>
<button onclick="window.location.href='index.php'">Torna alla Home</button>

<h1>📋 Trattative di Mercato</h1>

<label for="filtroSquadra">Filtra per squadra:</label>
<select id="filtroSquadra">
    <option value="tutte">Tutte le squadre</option>
</select>

<div class="table-container" id="contenitoreTrattative">
    <!-- Le trattative verranno caricate qui -->
</div>

<script>
    let datiTrattative = [];

    fetch("https://barrettasalvatore.it/endpoint/scambi/read.php")
        .then(res => res.json())
        .then(json => {
            datiTrattative = json.trattive || [];
            popolaSelect();
            mostraTrattative("tutte");
        })
        .catch(err => {
            document.getElementById("contenitoreTrattative").innerHTML = "<p>Errore nel caricamento dei dati.</p>";
        });

    function popolaSelect() {
        const select = document.getElementById("filtroSquadra");
        const squadre = new Set();

        datiTrattative.forEach(trattativa => {
            trattativa.scambi.forEach(s => {
                if (s.nome_squadra_cedente) {
                    squadre.add(s.nome_squadra_cedente);
                }
            });
        });

        Array.from(squadre).sort().forEach(nome => {
            const opt = document.createElement("option");
            opt.value = nome;
            opt.textContent = nome;
            select.appendChild(opt);
        });

        select.addEventListener("change", () => {
            mostraTrattative(select.value);
        });
    }

    function mostraTrattative(filtro) {
        const contenitore = document.getElementById("contenitoreTrattative");
        contenitore.innerHTML = "";

        datiTrattative.forEach(trattativa => {
            const contieneSquadra = filtro === "tutte" || trattativa.scambi.some(s => s.nome_squadra_cedente === filtro);
            if (!contieneSquadra) return;

            const div = document.createElement("div");
            div.style.marginBottom = "30px";

            const dataInizio = trattativa.data_inizio ?? "N/D";
            const dataFine = trattativa.data_fine ?? "N/D";

            div.innerHTML = `
                <h3>🆔 id_trattativa: ${trattativa.id_trattativa}</h3>
                <p><strong>Descrizione:</strong> ${trattativa.descrizione}</p>
                <p><strong>Periodo:</strong> ${dataInizio} ➡️ ${dataFine}</p>
                <table>
                    <thead>
                        <tr>
                            <th>Calciatore</th>
                            <th>Cedente</th>
                            <th>Ricevente</th>
                            <th>Importo</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${trattativa.scambi.map(s => `
                            <tr>
                                <td>${s.nome_calciatore ?? "-"}</td>
                                <td>${s.nome_squadra_cedente}</td>
                                <td>${s.nome_squadra_ricevente}</td>
                                <td>💰 ${s.credito_debito} crediti</td>
                            </tr>
                        `).join("")}
                    </tbody>
                </table>
            `;

            contenitore.appendChild(div);
        });
    }
</script>

</body>
</html>
