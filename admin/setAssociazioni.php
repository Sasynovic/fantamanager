<?php
session_start();

// Timeout in secondi
$timeout = 12000;

// Controlla se l'admin è loggato
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Se esiste il timestamp dell'ultima attività
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    if ($elapsed_time > $timeout) {
        // Timeout superato: logout
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit();
    }
}

// Aggiorna il timestamp dell'ultima attività
$_SESSION['last_activity'] = time();
require_once 'heading.php';
$nomeSezione = "Associazioni";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione <?php echo $nomeSezione?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        #istr {
            background-color: #f0f8ff;
            border-left: 5px solid #007acc;
            padding: 16px 20px;
            margin-bottom: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);

        }

        #istr p {
            margin: 0;
            line-height: 1.6;
            font-size: 16px;
        }

        #table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-family: 'Segoe UI', sans-serif;
        }

        #table th,
        #table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        #table th {
            background-color: #007acc;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }

        #table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        #table tr:hover {
            background-color: #eef6ff;
        }

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
            box-shadow: 0 4px 12px rgba(0, 122, 204, 0.35);
            transition: all 0.25s ease;
        }

        /* Hover */
        #salva-modifiche:hover {
            background: linear-gradient(135deg, #005fa3, #004c82);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 122, 204, 0.45);
        }

        /* Click */
        #salva-modifiche:active {
            transform: translateY(0);
            box-shadow: 0 3px 8px rgba(0, 122, 204, 0.35);
        }

        /* Disabilitato (se in futuro lo userai) */
        #salva-modifiche:disabled {
            background: #b5c7d6;
            cursor: not-allowed;
            box-shadow: none;
        }

    </style>
</head>
<body>

<div class="app-container">
    <div class="header">
        <h1>Gestione <?php echo $nomeSezione?></h1>
    </div>

    <div id="istr">
        <p>
            <strong>Istruzioni:</strong> Questa pagina ti permette di modificare i valori delle associazioni dell fvm.
            Scegli l'associazione che desideri modificare dalla tabella sottostante e aggiorna i valori secondo le tue necessità.
        </p>
    </div>

    <div>
        <select id="divisione-select">
            <option value="0" selected>Seleziona Divisione</option>
        </select>

        <select id="campionato" disabled>
            <option value="0" selected>Seleziona Campionato</option>
        </select>

        <select id="squadra" disabled>
            <option value="0" selected>Seleziona Squadra</option>
        </select>
    </div>

    <div id="calciatori-container" style="margin-top:20px; display:none;">
        <table id="calciatori-table" border="1" width="100%">
            <thead>
            <tr>
                <th>Nome</th>
                <th>Ruolo</th>
                <th>Squadra</th>
                <th>Cartellino</th>

                <th>Fuori listone</th>
                <th>Prelazione</th>
                <th>Fine prelazione</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button id="salva-modifiche" style="margin-top:15px;">
            Salva modifiche
        </button>
    </div>




</body>
</html>


<script>
    document.addEventListener('DOMContentLoaded', () => {

        /* =======================
           ELEMENTI DOM
        ======================= */
        const divisioneSelect = document.getElementById('divisione-select');
        const campionatoSelect = document.getElementById('campionato');
        const squadraSelect = document.getElementById('squadra');

        const calciatoriContainer = document.getElementById('calciatori-container');
        const calciatoriTableBody = document.querySelector('#calciatori-table tbody');
        const salvaBtn = document.getElementById('salva-modifiche');

        let modifiche = {}; // { id_associazione: { campo: valore } }

        /* =======================
           CARICA DIVISIONI
        ======================= */
        fetch('https://fantamanagerpro.eu/endpoint/divisione/read.php')
            .then(res => res.json())
            .then(data => {
                data.divisioni.forEach(divisione => {
                    const option = document.createElement('option');
                    option.value = divisione.id;
                    option.textContent = divisione.nome_divisione;
                    divisioneSelect.appendChild(option);
                });
            })
            .catch(err => console.error('Errore divisioni:', err));

        /* =======================
           DIVISIONE → CAMPIONATI
        ======================= */
        divisioneSelect.addEventListener('change', () => {
            const idDivisione = divisioneSelect.value;

            campionatoSelect.innerHTML = '<option value="0">Seleziona Campionato</option>';
            squadraSelect.innerHTML = '<option value="0">Seleziona Squadra</option>';
            campionatoSelect.disabled = true;
            squadraSelect.disabled = true;

            calciatoriContainer.style.display = 'none';
            calciatoriTableBody.innerHTML = '';
            modifiche = {};

            if (idDivisione === '0') return;

            fetch(`https://fantamanagerpro.eu/endpoint/competizione/read.php?id_divisione=${idDivisione}`)
                .then(res => res.json())
                .then(data => {
                    data.competizione.forEach(campionato => {
                        const option = document.createElement('option');
                        option.value = campionato.id;
                        option.textContent = campionato.nome_competizione;
                        campionatoSelect.appendChild(option);
                    });
                    campionatoSelect.disabled = false;
                })
                .catch(err => console.error('Errore campionati:', err));
        });

        /* =======================
           CAMPIONATO → SQUADRE
        ======================= */
        campionatoSelect.addEventListener('change', () => {
            const idCampionato = campionatoSelect.value;

            squadraSelect.innerHTML = '<option value="0">Seleziona Squadra</option>';
            squadraSelect.disabled = true;

            calciatoriContainer.style.display = 'none';
            calciatoriTableBody.innerHTML = '';
            modifiche = {};

            if (idCampionato === '0') return;

            fetch(`https://fantamanagerpro.eu/endpoint/partecipazione/read.php?id_competizione=${idCampionato}`)
                .then(res => res.json())
                .then(data => {
                    data.squadre.forEach(squadra => {
                        const option = document.createElement('option');
                        option.value = squadra.id_squadra;
                        option.textContent = squadra.nome_squadra;
                        squadraSelect.appendChild(option);
                    });
                    squadraSelect.disabled = false;
                })
                .catch(err => console.error('Errore squadre:', err));
        });

        /* =======================
           SQUADRA → CALCIATORI
        ======================= */
        squadraSelect.addEventListener('change', () => {
            const idSquadra = squadraSelect.value;

            calciatoriTableBody.innerHTML = '';
            calciatoriContainer.style.display = 'none';
            modifiche = {};

            if (idSquadra === '0') return;

            fetch(`https://fantamanagerpro.eu/endpoint/associazioni/read.php?id_squadra=${idSquadra}`)
                .then(res => res.json())
                .then(data => {

                    data.associazioni.forEach(row => {
                        const tr = document.createElement('tr');

                        tr.innerHTML = `
                        <td>${row.nome_calciatore}</td>
                        <td>${row.ruolo_calciatore}</td>
                        <td>${row.nome_squadra_calciatore}</td>

                        <td>
                            <input type="number" value="${row.costo_calciatore}"
                                   data-id="${row.id}" data-field="costo_calciatore">
                        </td>



                        <td>
                            <input type="checkbox" ${row.fuori_listone ? 'checked' : ''}
                                   data-id="${row.id}" data-field="fuori_listone">
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
                })
                .catch(err => console.error('Errore calciatori:', err));
        });

        /* =======================
           TRACK MODIFICHE
        ======================= */
        calciatoriTableBody.addEventListener('change', (e) => {
            const input = e.target;
            const id = input.dataset.id;
            const field = input.dataset.field;

            if (!id || !field) return;

            if (!modifiche[id]) modifiche[id] = {};

            modifiche[id][field] =
                input.type === 'checkbox'
                    ? (input.checked ? 1 : 0)
                    : input.value;
        });

        /* =======================
           SALVA MODIFICHE
        ======================= */
        salvaBtn.addEventListener('click', async () => {

            const ids = Object.keys(modifiche);
            if (ids.length === 0) {
                alert('Nessuna modifica da salvare');
                return;
            }

            try {
                for (const id of ids) {

                    await fetch(
                        `https://fantamanagerpro.eu/endpoint/associazioni/update.php?id=${id}`,
                        {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(modifiche[id]) // 👈 SOLO campi
                        }
                    );
                }

                alert('Modifiche salvate con successo');
                modifiche = {};

            } catch (err) {
                console.error(err);
                alert('Errore durante il salvataggio');
            }
        });
    });
</script>

