<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Divisioni e Competizioni</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 40px;
            background: linear-gradient(135deg, #1e1e2f, #323251);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff; display: flex; flex-direction: column; align-items: center;
        }

        h1 { font-size: 2em; margin-bottom: 30px; text-align: center; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4); }

        .box {
            width: 90%; max-width: 1000px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px; padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
        }

        .divisione {
            cursor: pointer; padding: 10px;
            background: rgba(255, 255, 255, 0.15);
            margin: 10px 0; border-radius: 10px;
        }

        .competizioni { display: none; padding-left: 20px; }
        .competizione { margin: 5px 0; }

        input[type="text"] {
            padding: 8px; border-radius: 8px;
            border: none; width: 70%; margin-right: 10px;
        }

        .controls {
            display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-bottom: 20px;
        }

        button {
            background: rgba(255, 255, 255, 0.15);
            border: none; border-radius: 10px;
            padding: 8px 12px;
            color: white; font-weight: bold;
            cursor: pointer; transition: background 0.3s;
        }

        button:hover { background: rgba(255, 255, 255, 0.3); }
    </style>
</head>
<body>
<h1>Benvenuto in Fantamanager</h1>

<div class="controls">
    <input type="text" id="searchTeam" placeholder="Cerca squadra...">
    <button onclick="searchTeam()">Cerca Squadra</button>

    <input type="text" id="searchCoach" placeholder="Cerca allenatore...">
    <button onclick="searchCoach()">Cerca Allenatore</button>

    <div>
        <button onclick="window.location.href='albo.php'">Vai all'Albo d'Oro</button>
        <button onclick="window.location.href='trattative.php'">Trattative di Mercato</button>
        <button onclick="window.location.href='vendita.php'">Squadre in Vendita</button>
    </div>
</div>

<div class="box" id="divisioniContainer">
    <!-- Divisioni caricate dinamicamente -->
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        fetch('endpoint/divisione/read.php')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('divisioniContainer');
                data.divisioni.forEach(div => {
                    const divEl = document.createElement('div');
                    divEl.className = 'divisione';
                    divEl.textContent = div.nome_divisione;
                    divEl.dataset.id = div.id;

                    const compList = document.createElement('div');
                    compList.className = 'competizioni';
                    divEl.addEventListener('click', () => {
                        if (compList.childElementCount === 0) {
                            fetch(`endpoint/competizione/read.php?id_divisione=${div.id}`)
                                .then(res => res.json())
                                .then(compData => {
                                    compData.competizioni.forEach(c => {
                                        const comp = document.createElement('div');
                                        comp.className = 'competizione';
                                        comp.innerHTML = `<a href="competizione.php?id=${c.id}" style="color:white;">${c.nome_competizione}</a>`;
                                        compList.appendChild(comp);
                                    });
                                    compList.style.display = 'block';
                                });
                        } else {
                            compList.style.display = compList.style.display === 'block' ? 'none' : 'block';
                        }
                    });

                    container.appendChild(divEl);
                    container.appendChild(compList);
                });
            });
    });

    function searchTeam() {
        const nome = document.getElementById('searchTeam').value;
        window.location.href = `ricerca_squadra.php?nome=${encodeURIComponent(nome)}`;
    }

    function searchCoach() {
        const nome = document.getElementById('searchCoach').value;
        window.location.href = `ricerca_presidente.php?nome=${encodeURIComponent(nome)}`;
    }
</script>
</body>
</html>