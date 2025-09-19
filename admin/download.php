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
$nomeSezione = "download";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione <?php echo $nomeSezione?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .app-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 30px;
    </style>
</head>
<body>
<div class="app-container">
        <button id="download-rose-associazioni" class="btn btn-primary"> Download associazione rosa + numero calciatori</button>
        <button id="download-finanze" class="btn btn-primary"> Download finanze</button>
        <button id="download-stadio" class="btn btn-primary"> Download stadio</button>
        <button id="download-presidenti" class="btn btn-primary"> Download presidenti + squadra</button>
        <button id="download-prelazioni" class="btn btn-primary"> Download prelazioni</button>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // download associazioni rose
        document.getElementById('download-rose-associazioni').addEventListener('click', function() {
            fetch('https://fantamanagerpro.eu/endpoint/associazioni/read.php?prelazione=0')
                .then(r => r.json())
                .then(associazioni => {
                    // Primo sheet: elenco dettagliato
                    const datiAssociazioni = associazioni.associazioni.map(assoc => ({
                        "ID Associazione": assoc.id,
                        "ID Squadra": assoc.id_squadra,
                        "Nome Squadra": assoc.nome_squadra,
                        "ID Calciatore": assoc.id_calciatore,
                        "Nome Calciatore": assoc.nome_calciatore,
                        "Ruolo": assoc.ruolo_calciatore,
                        "Costo": assoc.costo_calciatore,
                        "Squadra di Appartenenza": assoc.nome_squadra_calciatore,
                        "FVM": assoc.fvm,
                        "Età": assoc.eta ?? "N/D",
                        "Numero Movimenti": assoc.n_movimenti ?? "N/D",
                        "Scambiato": assoc.scambiato === null ? "No" : "Sì"
                    }));
                    // Secondo sheet: riepilogo calciatori per reparto per squadra
                    const riepilogo = {};
                    associazioni.associazioni.forEach(assoc => {
                        const squadra = assoc.nome_squadra;
                        const ruolo = assoc.ruolo_calciatore;

                        if (!riepilogo[squadra]) {
                            riepilogo[squadra] = { "Nome Squadra": squadra, "P": 0, "D": 0, "C": 0, "A": 0 };
                        }

                        if (["P", "D", "C", "A"].includes(ruolo)) {
                            riepilogo[squadra][ruolo] += 1;
                        }
                    });

                    const datiRiepilogo = Object.values(riepilogo);

                    // Creazione workbook
                    const wb = XLSX.utils.book_new();

                    // Sheet 1: elenco completo
                    XLSX.utils.book_append_sheet(
                        wb,
                        XLSX.utils.json_to_sheet(datiAssociazioni),
                        "Associazioni"
                    );

                    // Sheet 2: riepilogo per reparto
                    XLSX.utils.book_append_sheet(
                        wb,
                        XLSX.utils.json_to_sheet(datiRiepilogo),
                        "Riepilogo per Reparto"
                    );

                    XLSX.writeFile(wb, `Associazioni_${new Date().toISOString().slice(0, 10)}.xlsx`);
                })
                .catch(error => {
                    console.error("Errore durante l'esportazione:", error);
                    alert("Errore durante l'esportazione. Controlla la console.");
                });
        });

        //download finanze - NO CREDITO RESIDUO PROSSIMA STAGIONE
        document.getElementById('download-finanze').addEventListener('click', function() {
            fetch('https://fantamanagerpro.eu/endpoint/finanze_squadra/read.php')
                .then(r => r.json())
                .then(finanze_squadra => {
                    const datiFinanzePS = finanze_squadra.finanze_squadra.map(fin => ({
                        "id": fin.id,
                        "guadagno_crediti_stadio_league": fin.guadagno_crediti_stadio_league,
                        "guadagno_crediti_stadio_cup": fin.guadagno_crediti_stadio_cup,
                        "premi_league": fin.premi_league,
                        "premi_cup": fin.premi_cup,
                        "prequalifiche_uefa_stadio": fin.prequalifiche_uefa_stadio,
                        "prequalifiche_uefa_premio": fin.prequalifiche_uefa_premio,
                        "competizioni_uefa_stadio": fin.competizioni_uefa_stadio,
                        "competizioni_uefa_premio": fin.competizioni_uefa_premio,
                    }))
                    const datiFinanze = finanze_squadra.finanze_squadra.map(fin => ({
                            "id": fin.id,
                            "totale_crediti_bilancio": fin.totale_crediti_bilancio,
                        }));

                    const wb = XLSX.utils.book_new();

                    XLSX.utils.book_append_sheet(wb,
                        XLSX.utils.json_to_sheet(datiFinanze),
                        "Finanze");
                    XLSX.utils.book_append_sheet(wb,
                        XLSX.utils.json_to_sheet(datiFinanzePS),
                        "Finanze prossima stagione");

                    XLSX.writeFile(wb, `Finanze_${new Date().toISOString().slice(0,10)}.xlsx`);
                })
                .catch(error => {
                    console.error("Errore durante l'esportazione:", error);
                    alert("Errore durante l'esportazione. Controlla la console.");
                });


        });

        document.getElementById('download-stadio').addEventListener('click', function() {
            fetch('https://fantamanagerpro.eu/endpoint/stadio/read.php')
                .then(r => r.json())
                .then(stadio => {
                    const datiStadio = stadio.stadio.map(stad => ({
                        "ID squadra": stad.id,
                        "Nome stadio": stad.nome_stadio,
                        "Livello stadio": stad.livello_stadio,
                        "Costo manutenzione": stad.costo_manutenzione,
                        "Costo costruzione": stad.costo_costruzione,
                        "Bonus casa Nazionale" : stad.bonus_casa_n,
                        "Bonus casa Uefa": stad.bonus_casa_u,
                        "Sold out": stad.sold_out,
                        "Abbonati": stad.abbonati,
                        "Ultimo aggiornamento" : stad.timestamp,
                    }));

                    const wb = XLSX.utils.book_new();

                    XLSX.utils.book_append_sheet(wb,
                        XLSX.utils.json_to_sheet(datiStadio),
                        "Stadio");

                    XLSX.writeFile(wb, `Stadio_${new Date().toISOString().slice(0,10)}.xlsx`);
                })
                .catch(error => {
                    console.error("Errore durante l'esportazione:", error);
                    alert("Errore durante l'esportazione. Controlla la console.");
                });
        });

        document.getElementById('download-presidenti').addEventListener('click', function (){
            fetch('https://fantamanagerpro.eu/endpoint/squadra/read.php?limit=1000')
                .then(r => r.json())
                .then(squadra => {
                    const datiSquadra = squadra.squadra.map(squad => ({
                        "ID": squad.id,
                        "Nome squadra": squad.nome_squadra,
                        "ID Presidente": squad.dirigenza.id_pres,
                        "Nome Presidente": squad.dirigenza.presidente,
                        "ID Vice Presidente": squad.dirigenza.id_vice,
                        "Nome Vice Presidente": squad.dirigenza.vicepresidente
                    }));

                    const wb = XLSX.utils.book_new();

                    XLSX.utils.book_append_sheet(wb,
                        XLSX.utils.json_to_sheet(datiSquadra),
                        "Squadra");

                    XLSX.writeFile(wb, `Squadre_e_Pres_${new Date().toISOString().slice(0,10)}.xlsx`);
                })
                .catch(error => {
                    console.error("Errore durante l'esportazione:", error);
                    alert("Errore durante l'esportazione. Controlla la console.");
                });
        })

        document.getElementById('download-prelazioni').addEventListener('click', function() {
            fetch('https://fantamanagerpro.eu/endpoint/associazioni/read.php')
                .then(r => r.json())
                .then(associazioni => {
                    // Prelazione Sì
                    const prelazioneSi = associazioni.associazioni
                        .filter(prel => prel.prelazione == 1)
                        .map(prel => ({
                            "ID Squadra": prel.id_squadra,
                            "Nome Squadra": prel.nome_squadra,
                            "ID Calciatore": prel.id_calciatore,
                            "Nome Calciatore": prel.nome_calciatore,
                            "Ruolo": prel.ruolo_calciatore,
                            "Costo": prel.costo_calciatore,
                            "FVM": prel.fvm
                        }));

                    //Filtra solo quelle con timestamp valorizzato

                    const valide = associazioni.associazioni.filter(prel => prel.Timestamp !== null);

                    // Prelazione No
                    const prelazioneNo = valide
                        .filter(prel => prel.prelazione == 0)
                        .map(prel => ({
                            "ID Squadra": prel.id_squadra,
                            "Nome Squadra": prel.nome_squadra,
                            "ID Calciatore": prel.id_calciatore,
                            "Nome Calciatore": prel.nome_calciatore,
                            "Ruolo": prel.ruolo_calciatore,
                            "Costo": prel.costo_calciatore,
                            "FVM": prel.fvm,
                            "Timestamp": prel.Timestamp
                        }));

                    const wb = XLSX.utils.book_new();

                    // Aggiunge foglio "Prelazione Sì"
                    XLSX.utils.book_append_sheet(
                        wb,
                        XLSX.utils.json_to_sheet(prelazioneSi),
                        "Prelazioni in corso"
                    );

                    // Aggiunge foglio "Prelazione No"
                    XLSX.utils.book_append_sheet(
                        wb,
                        XLSX.utils.json_to_sheet(prelazioneNo),
                        "Prelazioni riscattate"
                    );

                    XLSX.writeFile(wb, `Prelazioni_${new Date().toISOString().slice(0, 10)}.xlsx`);
                })
                .catch(error => {
                    console.error("Errore durante l'esportazione:", error);
                    alert("Errore durante l'esportazione. Controlla la console.");
                });
        });
    </script>
