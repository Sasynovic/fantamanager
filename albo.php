<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Albo d'Oro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
</head>


<style>
    /* [TUTTI GLI STILI PRECEDENTI RIMANGONO INVARIATI...] */

    /* STILE SPECIFICO PER L'ALBO D'ORO */
    .main-body-content {
        padding: 20px;
        overflow-y: auto;
    }

    .albo-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 0 20px;
    }

    .albo-header h1 {
        font-size: 2.2rem;
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    #filtroCompetizione {
        background-color: var(--blu-scuro);
        color: white;
        border: 2px solid var(--accento);
        border-radius: 30px;
        padding: 10px 20px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 15px;
    }

    #filtroCompetizione:hover {
        background-color: var(--blu);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.3);
    }

    #filtroCompetizione:focus {
        outline: none;
        border-color: white;
    }

    .table-container {
        background-color: var(--blu-scuro);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        margin: 0 auto;
        max-width: 95%;
    }

    #alboTable {
        width: 100%;
        border-collapse: collapse;
        background-color: var(--blu-scuro);
        border-radius: 15px;
        overflow: hidden;
    }

    #alboTable thead {
        background: linear-gradient(135deg, var(--accento), var(--blu));
    }

    #alboTable th {
        padding: 15px;
        text-align: left;
        font-weight: bold;
        font-size: 1.1rem;
        color: white;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    #alboTable td {
        padding: 12px 15px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        color: #eee;
    }

    #alboTable tr:nth-child(even) {
        background-color: rgba(41, 69, 130, 0.2);
    }

    #alboTable tr:hover {
        background-color: rgba(60, 116, 245, 0.15);
    }

    #alboTable tr:last-child td {
        border-bottom: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .albo-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        #filtroCompetizione {
            width: 100%;
        }

        #alboTable th, #alboTable td {
            padding: 10px;
            font-size: 0.9rem;
        }
    }
</style>
<body>



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
