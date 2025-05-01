<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Trattative di Mercato</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        select {
            padding: 5px;
            font-size: 1em;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<h1>📋 Trattative di Mercato</h1>
<label for="filtroSquadra">Filtra per squadra cedente:</label>
<select id="filtroSquadra">
    <option value="tutte">Tutte le squadre</option>
</select>

<table id="tabellaTrattative">
    <thead>
    <tr>
        <th>Calciatore</th>
        <th>Cedente</th>
        <th>Ricevente</th>
        <th>Importo</th>
    </tr>
    </thead>
    <tbody id="corpoTabella">
    <!-- Trattative qui -->
    </tbody>
</table>

<script>
    let datiTrattative = [];

    fetch("https://barrettasalvatore.it/endpoint/scambi/read.php") // ← assicurati che questo endpoint esista
        .then(res => res.json())
        .then(json => {
            datiTrattative = json.scambi || [];
            popolaSelect();
            mostraTabella("tutte");
        });

    function popolaSelect() {
        const select = document.getElementById("filtroSquadra");
        const squadre = [...new Set(datiTrattative.map(t => t.nome_squadra_cedente))];
        squadre.sort().forEach(nome => {
            const opt = document.createElement("option");
            opt.value = nome;
            opt.textContent = nome;
            select.appendChild(opt);
        });

        select.addEventListener("change", () => {
            mostraTabella(select.value);
        });
    }

    function mostraTabella(filtro) {
        const corpo = document.getElementById("corpoTabella");
        corpo.innerHTML = "";

        datiTrattative
            .filter(t => filtro === "tutte" || t.nome_squadra_cedente === filtro)
            .forEach(t => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                        <td>${t.nome_calciatore}</td>
                        <td>${t.nome_squadra_cedente}</td>
                        <td>${t.nome_squadra_ricevente}</td>
                        <td>💰 ${t.debito_credito} crediti</td>
                    `;
                corpo.appendChild(tr);
            });
    }
</script>

</body>
</html>
