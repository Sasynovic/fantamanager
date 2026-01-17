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
$nomeSezione = "Listone";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione <?php echo $nomeSezione ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/tabulator-tables@6.2.1/dist/css/tabulator.min.css" rel="stylesheet">

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

        #btn-update {
            display: none;
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

        #btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,122,204,.45);
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
        <strong>Istruzioni:</strong> scarica l'ultimo listone disponibile. Rimuovi per i fogli "Tutti" e "Ceduti" la prima riga informativa. Carica il file ed il sistema fara' tutto in automatico :)
    </div>

    <div style="margin:20px 0;">
        <input type="file" id="excelInput" accept=".xlsx,.xls">
    </div>

    <button  id="btn-update" onclick="eseguiUpdate()">Aggiorna tutto</button>

    <div id="tabella-diff" style="margin-top:30px;"></div>

    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/tabulator-tables@6.2.1/dist/js/tabulator.min.js"></script>

    <script>
        const excelTutti = [];
        const excelCeduti = [];
        let dbCalciatori = [];

        let nuoviCalciatori = [];
        let calciatoriDaAggiornare = [];

        document.getElementById('excelInput').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (evt) {
                const data = new Uint8Array(evt.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                ['Tutti', 'Ceduti'].forEach(nomeFoglio => {
                    if (!workbook.SheetNames.includes(nomeFoglio)) return;

                    const worksheet = workbook.Sheets[nomeFoglio];
                    const json = XLSX.utils.sheet_to_json(worksheet, { defval: '' });

                    const dati = json.map(r => ({
                        Id: Number(r['Id']),
                        R: r['R'],
                        Nome: r['Nome'],
                        Squadra: r['Squadra'],
                        FVM: Number(r['FVM'])
                    }));

                    if (nomeFoglio === 'Tutti') excelTutti.push(...dati);
                    if (nomeFoglio === 'Ceduti') excelCeduti.push(...dati);

                    console.table(dati);
                });

                caricaDbCalciatori();
            };

            reader.readAsArrayBuffer(file);
        });

        function aggiornaCeduti() {
            excelCeduti.forEach(c => {
                fetch(`https://barrettasalvatore.it/endpoint/calciatori/update.php?id=${c.Id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ fuori_listone: 1 })
                });
            });
        }

        function caricaDbCalciatori() {
            fetch('https://barrettasalvatore.it/endpoint/calciatori/read.php')
                .then(r => r.json())
                .then(data => {
                    dbCalciatori = data.calciatori;
                    confrontaDati();
                });
        }

        function confrontaDati() {
            const dbMap = {};
            dbCalciatori.forEach(c => dbMap[c.id] = c);

            excelTutti.forEach(excel => {
                const db = dbMap[excel.Id];

                if (!db) {
                    // NUOVO
                    nuoviCalciatori.push(excel);
                } else {
                    // confronto campi
                    if (
                        db.nome !== excel.Nome ||
                        db.squadra !== excel.Squadra ||
                        parseInt(db.fvm) !== excel.FVM ||
                        db.ruolo !== excel.R
                    ) {
                        calciatoriDaAggiornare.push({
                            id: excel.Id,
                            before: db,
                            after: excel
                        });
                    }
                }
            });

            mostraTabella();
            const btnUpdate = document.getElementById('btn-update');
            if (nuoviCalciatori.length > 0 || calciatoriDaAggiornare.length > 0) {
                btnUpdate.style.display = 'block';
            } else {
                btnUpdate.style.display = 'none';
                alert('Nessun aggiornamento necessario.');
            }
        }

        function mostraTabella() {
            const tableData = [];

            nuoviCalciatori.forEach(c => {
                tableData.push({
                    tipo: 'NUOVO',
                    id: c.Id,
                    ruolo: c.R,
                    nome: c.Nome,
                    squadra: c.Squadra,
                    fvm: c.FVM
                });
            });

            calciatoriDaAggiornare.forEach(c => {
                tableData.push({
                    tipo: 'UPDATE',
                    id: c.id,
                    ruolo: `${c.before.ruolo} → ${c.after.R}`,
                    nome: `${c.before.nome} → ${c.after.Nome}`,
                    squadra: `${c.before.squadra} → ${c.after.Squadra}`,
                    fvm: `${c.before.fvm} → ${c.after.FVM}`
                });
            });

            new Tabulator("#tabella-diff", {
                data: tableData,
                layout: "fitColumns",
                columns: [
                    { title: "Tipo", field: "tipo" },
                    { title: "ID", field: "id" },
                    { title: "Nome", field: "nome" },
                    { title: "Squadra", field: "squadra" },
                    { title: "FVM", field: "fvm" }
                ]
            });
        }

        function eseguiUpdate() {

            // Ceduti
            aggiornaCeduti();

            // CREATE
            nuoviCalciatori.forEach(c => {
                fetch('https://barrettasalvatore.it/endpoint/calciatori/create.php', {
                    method: 'POST',
                    mode: 'cors',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: Number(c.Id),
                        nome: String(c.Nome || '').trim(),
                        ruolo: String(c.R || '').trim(),
                        squadra: String(c.Squadra || '').trim(),
                        fvm: Number(c.FVM) > 0 ? Number(c.FVM) : 1
                    })
                })
                    .then(r => r.text())
                    .then(t => console.log('CREATE', c.Id, t))
                    .catch(err => console.error('CREATE ERROR', c.Id, err));
            });


            // UPDATE
            calciatoriDaAggiornare.forEach(c => {
                fetch(`https://barrettasalvatore.it/endpoint/calciatori/update.php?id=${c.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        nome: c.after.Nome,
                        ruolo: c.after.R,
                        squadra: c.after.Squadra,
                        fvm: c.after.FVM,
                        prelazione: 1
                    })
                });
            });
            alert('Aggiornamento completato con successo!');
            window.location.reload();
        }
    </script>



