<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Albo d'Oro</title>
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
            font-size: 2em; margin-bottom: 20px;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }
        select {
            padding: 10px; margin-bottom: 20px;
            border-radius: 5px; border: none;
            background-color: #444; color: white;
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
        button{
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
    </style>
</head>
<body>

<button onclick="window.location.href='index.php'">Torna alla Home</button>

<h1>🏆 Albo d'Oro</h1>

<select id="filtroCompetizione">
    <option value="Tutte">Tutte le competizioni</option>
</select>

<div class="table-container">
    <table id="alboTable">
        <thead>
        <tr>
            <th>Competizione</th>
            <th>Anno</th>
            <th>Squadra</th>
        </tr>
        </thead>
        <tbody>
        <tr><td colspan="3">Caricamento dati...</td></tr>
        </tbody>
    </table>
</div>

<script>
    let alboData = [];

    function popolaCompetizioni(data) {
        const select = document.getElementById('filtroCompetizione');
        const competizioni = [...new Set(data.map(r => r.nome_competizione))];
        competizioni.sort();
        competizioni.forEach(nome => {
            const option = document.createElement('option');
            option.value = nome;
            option.textContent = nome;
            select.appendChild(option);
        });
    }

    function mostraAlbo(filtro = 'Tutte') {
        const tbody = document.querySelector('#alboTable tbody');
        tbody.innerHTML = '';

        const datiFiltrati = filtro === 'Tutte'
            ? alboData
            : alboData.filter(r => r.nome_competizione === filtro);

        if (datiFiltrati.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">Nessun vincitore registrato.</td></tr>';
            return;
        }

        datiFiltrati.forEach(r => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${r.nome_competizione}</td>
                <td>${r.anno}</td>
                <td>${r.nome_squadra}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('filtroCompetizione').addEventListener('change', function () {
        mostraAlbo(this.value);
    });

    fetch('https://barrettasalvatore.it/endpoint/albo/read.php')
        .then(response => response.json())
        .then(data => {
            if (data.albo && Array.isArray(data.albo)) {
                alboData = data.albo;
                popolaCompetizioni(alboData);
                mostraAlbo();
            } else {
                document.querySelector('#alboTable tbody').innerHTML =
                    '<tr><td colspan="3">Nessun vincitore trovato.</td></tr>';
            }
        })
        .catch(error => {
            console.error('Errore nel caricamento albo:', error);
            document.querySelector('#alboTable tbody').innerHTML =
                '<tr><td colspan="3">Errore nel caricamento dei dati.</td></tr>';
        });
</script>



</body>
</html>
