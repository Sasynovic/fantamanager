<?php
$nome_presidente = isset($_GET['nome']) ? $_GET['nome'] : null;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ricerca Presidente - Fantamanager</title>
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

        .box {
            width: 90%; max-width: 1000px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px; padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
        }

        table {
            width: 100%; border-collapse: collapse;
            color: #fff;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        th {
            background-color: rgba(255, 255, 255, 0.15);
            font-weight: bold;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .noResults {
            font-size: 1.2em;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            padding: 20px;
        }

        button {
            background: rgba(255, 255, 255, 0.15);
            border: none; border-radius: 10px;
            padding: 10px 20px;
            color: white; font-weight: bold;
            cursor: pointer; transition: background 0.3s;
            margin-top: 20px;
        }

        button:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
<h1>Risultati ricerca per presidente</h1>

<div class="box" id="presidentiContainer">
    <!-- Risultati in tabella -->
</div>

<button onclick="window.location.href='index.php'">Torna alla Home</button>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const nomePresidente = "<?php echo htmlspecialchars($nome_presidente); ?>";
        if (nomePresidente) {
            fetch(`endpoint/squadra/read.php?nome_presidente=${encodeURIComponent(nomePresidente)}`)
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('presidentiContainer');
                    if (data.squadre && data.squadre.length > 0) {
                        let table = `<table>
                                <thead>
                                    <tr>
                                        <th>Squadra</th>
                                        <th>Presidente</th>
                                        <th>Stadio</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                        data.squadre.forEach(squadra => {
                            table += `<tr>
                                    <td>${squadra.nome_squadra}</td>
                                    <td>${squadra.presidente}</td>
                                    <td>${squadra.stadio}</td>
                                </tr>`;
                        });
                        table += `</tbody></table>`;
                        container.innerHTML = table;
                    } else {
                        container.innerHTML = `<div class="noResults">Nessun presidente trovato con il nome <strong>${nomePresidente}</strong>.</div>`;
                    }
                })
                .catch(err => {
                    console.error("Errore nella ricerca del presidente:", err);
                    document.getElementById('presidentiContainer').innerHTML = `<div class="noResults">Si è verificato un errore durante la ricerca.</div>`;
                });
        }
    });
</script>
</body>
</html>
