<?php
//session_start();
//
//// Timeout in secondi
//$timeout = 12000;
//
//// Controlla se l'admin è loggato
//if (!isset($_SESSION['admin_logged_in'])) {
//    header("Location: login.php");
//    exit();
//}
//
//// Se esiste il timestamp dell'ultima attività
//if (isset($_SESSION['last_activity'])) {
//    $elapsed_time = time() - $_SESSION['last_activity'];
//    if ($elapsed_time > $timeout) {
//        // Timeout superato: logout
//        session_unset();
//        session_destroy();
//        header("Location: login.php?timeout=1");
//        exit();
//    }
//}
//
//// Aggiorna il timestamp dell'ultima attività
//$_SESSION['last_activity'] = time();
require_once 'heading.php';
$nomeSezione = "Associazioni";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione <?php echo $nomeSezione ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }

        #istr {
            background-color: #f0f8ff;
            border-left: 5px solid #007acc;
            padding: 16px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #007acc;
            color: #fff;
            font-size: 14px;
        }

        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #eef6ff; }

        #salva-modifiche {
            margin-top: 20px;
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #007acc, #005fa3);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,122,204,.35);
            transition: all .25s;
        }

        #salva-modifiche:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,122,204,.45);
        }

        #search-calciatore-btn {
            padding: 9px 18px;
            background: #444;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        select, input[type="number"] {
            padding: 8px;
        }
    </style>
</head>

<body>

<div class="app-container">
    <h1>Gestione <?php echo $nomeSezione ?></h1>

    <div id="istr">
        <strong>Istruzioni:</strong> puoi cercare per squadra oppure direttamente per ID calciatore.
    </div>

    <!-- RICERCA ID CALCIATORE -->
    <div style="margin-bottom:15px;">
        <input type="number" id="search-id-calciatore" placeholder="ID calciatore">
        <button id="search-calciatore-btn">Cerca</button>
    </div>

    <!-- SELECT -->
    <div>
        <select id="divisione-select">
            <option value="0">Seleziona Divisione</option>
        </select>

        <select id="campionato" disabled>
            <option value="0">Seleziona Campionato</option>
        </select>

        <select id="squadra" disabled>
            <option value="0">Seleziona Squadra</option>
        </select>
    </div>

    <!-- TABELLA -->
    <div id="calciatori-container" style="display:none;">
        <table id="calciatori-table">
            <thead>
            <tr>
                <th>Nome squadra</th>
                <th>Nome</th>
                <th>Ruolo</th>
                <th>Squadra</th>
                <th>Cartellino</th>
                <th>Prelazione</th>
                <th>Fine prelazione</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button id="salva-modifiche">Salva modifiche</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        const divisioneSelect = document.getElementById('divisione-select');
        const campionatoSelect = document.getElementById('campionato');
        const squadraSelect = document.getElementById('squadra');

        const calciatoriContainer = document.getElementById('calciatori-container');
        const calciatoriTableBody = document.querySelector('#calciatori-table tbody');
        const salvaBtn = document.getElementById('salva-modifiche');

        const searchBtn = document.getElementById('search-calciatore-btn');
        const searchInput = document.getElementById('search-id-calciatore');

        let modifiche = {};

        /* =========================
           RENDER TABELLA
        ========================= */
        function renderCalciatori(data) {
            calciatoriTableBody.innerHTML = '';
            modifiche = {};

            if (!data.associazioni || data.associazioni.length === 0) {
                alert('Nessun risultato');
                calciatoriContainer.style.display = 'none';
                return;
            }

            data.associazioni.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${row.nome_squadra}</td>
                <td>${row.nome_calciatore}</td>
                <td>${row.ruolo_calciatore}</td>
                <td>${row.nome_squadra_calciatore}</td>

                <td>
                    <input type="number" value="${row.costo_calciatore}"
                        data-id="${row.id}" data-field="costo_calciatore">
                </td>

                <td>
                    <input type="checkbox" ${row.prelazione ? 'checked' : ''}
                        data-id="${row.id}" data-field="prelazione">
                </td>

                <td>
                    <input type="date"
                        value="${row.fine_prelazione ? row.fine_prelazione.split(' ')[0] : ''}"
                        data-id="${row.id}" data-field="fine_prelazione">
                </td>
            `;
                calciatoriTableBody.appendChild(tr);
            });

            calciatoriContainer.style.display = 'block';
        }

        /* =========================
           TRACK MODIFICHE
        ========================= */
        calciatoriTableBody.addEventListener('change', e => {
            const el = e.target;
            const id = el.dataset.id;
            const field = el.dataset.field;
            if (!id || !field) return;

            if (!modifiche[id]) modifiche[id] = {};
            modifiche[id][field] = el.type === 'checkbox' ? (el.checked ? 1 : 0) : el.value;
        });

        /* =========================
           SALVA
        ========================= */
        salvaBtn.addEventListener('click', async () => {
            console.log('CLICK su Salva');

            const ids = Object.keys(modifiche);
            console.log('IDs da salvare:', ids);

            if (!ids.length) {
                console.warn('Nessuna modifica presente');
                return alert('Nessuna modifica');
            }

            for (const id of ids) {
                console.log(`Invio update per ID: ${id}`);
                console.log('Payload:', modifiche[id]);

                try {
                    const response = await fetch(
                        `https://www.fantamanagerpro.eu/endpoint/associazioni/update.php?id=${id}`,
                        {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(modifiche[id])
                        }
                    );

                    console.log(`Response status per ID ${id}:`, response.status);

                    const text = await response.text();
                    console.log(`Response body per ID ${id}:`, text);

                    if (!response.ok) {
                        console.error(`Errore HTTP su ID ${id}`, response.status);
                    }

                } catch (error) {
                    console.error(`Errore fetch per ID ${id}`, error);
                }
            }

            console.log('Tutte le richieste completate');
            alert('Modifiche salvate');
            modifiche = {};
            window.location.reload();
        });


        /* =========================
           RICERCA ID CALCIATORE
        ========================= */
        searchBtn.addEventListener('click', () => {
            const id = searchInput.value;
            if (!id) return alert('Inserisci ID');

            fetch(`https://www.fantamanagerpro.eu/endpoint/associazioni/read.php?id_calciatore=${id}`)
                .then(r => r.json())
                .then(renderCalciatori);
        });

        /* =========================
           CASCATA SELECT
        ========================= */
        fetch('https://www.fantamanagerpro.eu/endpoint/divisione/read.php')
            .then(r => r.json())
            .then(d => d.divisioni.forEach(x => {
                divisioneSelect.innerHTML += `<option value="${x.id}">${x.nome_divisione}</option>`;
            }));

        divisioneSelect.addEventListener('change', () => {
            campionatoSelect.disabled = true;
            squadraSelect.disabled = true;
            campionatoSelect.innerHTML = '<option value="0">Seleziona Campionato</option>';
            squadraSelect.innerHTML = '<option value="0">Seleziona Squadra</option>';

            if (divisioneSelect.value === '0') return;

            fetch(`https://www.fantamanagerpro.eu/endpoint/competizione/read.php?id_divisione=${divisioneSelect.value}`)
                .then(r => r.json())
                .then(d => {
                    d.competizione.forEach(c =>
                        campionatoSelect.innerHTML += `<option value="${c.id}">${c.nome_competizione}</option>`
                    );
                    campionatoSelect.disabled = false;
                });
        });

        campionatoSelect.addEventListener('change', () => {
            squadraSelect.disabled = true;
            squadraSelect.innerHTML = '<option value="0">Seleziona Squadra</option>';

            fetch(`https://www.fantamanagerpro.eu/endpoint/partecipazione/read.php?id_competizione=${campionatoSelect.value}`)
                .then(r => r.json())
                .then(d => {
                    d.squadre.forEach(s =>
                        squadraSelect.innerHTML += `<option value="${s.id_squadra}">${s.nome_squadra}</option>`
                    );
                    squadraSelect.disabled = false;
                });
        });

        squadraSelect.addEventListener('change', () => {
            if (squadraSelect.value === '0') return;

            fetch(`https://www.fantamanagerpro.eu/endpoint/associazioni/read.php?id_squadra=${squadraSelect.value}`)
                .then(r => r.json())
                .then(renderCalciatori);
        });
    });
</script>

</body>
</html>