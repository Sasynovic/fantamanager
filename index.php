<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Squadre di Calcio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        .competizioni { display: none; }
        .flag { width: 20px; height: auto; margin-left: 8px; }
        .squadra { border: 1px solid #ccc; margin: 10px 0; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <main id="squadre-container">
        <h1>Squadre</h1>
        <p>Caricamento in corso...</p>
    </main>

    <aside class="elenco">
        <ul id="divisioni-container"></ul>
    </aside>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        caricaSquadre();
        caricaDivisioni();
    });

    async function caricaSquadre() {
        try {
            const res = await fetch('endpoint/squadra/read.php'); // Endpoint da adattare
            const data = await res.json();
            const container = document.getElementById('squadre-container');
            container.innerHTML = '<h1>Squadre</h1>';

            if (!data.squadre) {
                container.innerHTML += '<p>Errore nel recupero delle squadre.</p>';
                return;
            }

            data.squadre.forEach(squadra => {
                const div = document.createElement('div');
                div.className = 'squadra';
                div.innerHTML = `
                    <h3>${squadra.nome_squadra}</h3>
                    <p><strong>Presidente:</strong> ${squadra.presidente}</p>
                    <p><strong>Vice Presidente:</strong> ${squadra.vicepresidente}</p>
                    <p><strong>Stadio:</strong> ${squadra.stadio}</p>
                `;
                container.appendChild(div);
            });
        } catch (error) {
            document.getElementById('squadre-container').innerHTML += '<p>Errore di connessione.</p>';
            console.error(error);
        }
    }

    async function caricaDivisioni() {
        try {
            const res = await fetch('endpoint/divisione/read.php'); // Endpoint da adattare
            const data = await res.json();
            const lista = document.getElementById('divisioni-container');

            if (!data.divisioni) return;

            for (const divisione of data.divisioni) {
                const li = document.createElement('li');
                const div = document.createElement('div');
                div.className = 'divisione';
                div.innerHTML = `
                    <span>${divisione.nome_divisione}</span>
                    <img src="public/flag/${divisione.bandiera}" alt="Bandiera" class="flag">
                `;
                li.appendChild(div);

                const ulCompetizioni = document.createElement('ul');
                ulCompetizioni.className = 'competizioni';
                li.appendChild(ulCompetizioni);

                div.addEventListener('click', () => {
                    ulCompetizioni.style.display = ulCompetizioni.style.display === 'block' ? 'none' : 'block';
                });

                // Recupero delle competizioni per la divisione
                const competizioniRes = await fetch(`api/competizioni?divisione_id=${divisione.id}`); // Endpoint da adattare
                const competizioniData = await competizioniRes.json();

                if (competizioniData.competizioni) {
                    competizioniData.competizioni.forEach(comp => {
                        const compLi = document.createElement('li');
                        compLi.className = 'competizione';
                        compLi.innerHTML = `<a href="competizione.php?id=${comp.id}">${comp.nome_competizione}</a>`;
                        ulCompetizioni.appendChild(compLi);
                    });
                }

                lista.appendChild(li);
            }
        } catch (error) {
            console.error('Errore nel caricamento delle divisioni:', error);
        }
    }
</script>

</body>
</html>
