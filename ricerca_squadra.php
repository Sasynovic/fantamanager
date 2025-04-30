<?php
$nome_squadra = isset($_GET['nome']) ? $_GET['nome'] : null;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ricerca Squadra - Fantamanager</title>
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
            text-align: center; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }

        button {
            background: rgba(255, 255, 255, 0.15);
            border: none; border-radius: 10px;
            padding: 8px 12px;
            color: white; font-weight: bold;
            cursor: pointer; transition: background 0.3s;
            margin-top: 20px;
        }

        button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        #squadreContainer {
            width: 90%; max-width: 1000px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px; padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: #fff;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: rgba(255, 255, 255, 0.2);
            font-size: 1.1em;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .noResults {
            font-size: 1.2em;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
<h1>Risultati ricerca per squadra</h1>

<div id="squadreContainer">
    <!-- I risultati verranno inseriti qui come tabella -->
</div>

<button onclick="window.location.href='index.php'">Torna alla Home</button>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const nomeSquadra = "<?php echo htmlspecialchars($nome_squadra); ?>";
        if (nomeSquadra) {
            fetch(`endpoint/squadra/read.php?nome_squadra=${encodeURIComponent(nomeSquadra)}`)
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('squadreContainer');
                    if (data.squadre && data.squadre.length > 0) {
                        let table = `
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nome Squadra</th>
                                        <th>Presidente</th>
                                        <th>Stadio</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        data.squadre.forEach(squadra => {
                            table += `
                                <tr>
                                    <td>${squadra.nome_squadra}</td>
                                    <td>${squadra.presidente}</td>
                                    <td>${squadra.stadio}</td>
                                </tr>
                            `;
                        });

                        table += `</tbody></table>`;
                        container.innerHTML = table;
                    } else {
                        container.innerHTML = `<p class="noResults">Nessuna squadra trovata con il nome "${nomeSquadra}".</p>`;
                    }
                })
                .catch(err => {
                    console.error("Errore nella ricerca della squadra:", err);
                });
        }
    });
</script>
</body>
</html>
