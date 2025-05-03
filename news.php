<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>News Sportive</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
        }

        #filter {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            justify-content: center;
        }

        select, button {
            padding: 0.4rem;
            font-size: 1rem;
        }

        .news-card {
            background-color: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 1rem auto;
            max-width: 600px;
        }

        .news-card h2 {
            margin: 0 0 0.5rem;
        }

        .news-date {
            font-size: 0.85rem;
            color: gray;
        }
    </style>
</head>
<body>

<h1>Ultime News</h1>

<div id="filter">
    <label for="competizione">Campionato:</label>
    <select id="competizione"></select>
    <button onclick="caricaNews()">Cerca</button>
</div>

<div id="news-container"></div>

<script>
    async function caricaCampionati() {
        // Sostituisci questo URL con il tuo endpoint reale
        const url = 'https://barrettasalvatore.it/endpoint/competizione/read.php';

        try {
            const res = await fetch(url);
            const data = await res.json();

            const select = document.getElementById("competizione");
            select.innerHTML = '';

            data.competizioni.forEach(comp => {
                const option = document.createElement("option");
                option.value = comp.id;
                option.textContent = comp.nome;
                select.appendChild(option);
            });

            // Carica notizie del primo campionato all'avvio
            if (data.competizioni.length > 0) {
                caricaNews();
            }
        } catch (err) {
            console.error("Errore nel caricamento dei campionati:", err);
        }
    }

    async function caricaNews() {
        const idCompetizione = document.getElementById("competizione").value;
        const container = document.getElementById("news-container");
        container.innerHTML = "<p>Caricamento in corso...</p>";

        try {
            const url = `https://barrettasalvatore.it/endpoint/news/read.php?id_competizione=${idCompetizione}`;
            const res = await fetch(url);
            const data = await res.json();

            container.innerHTML = "";

            if (!data.news || data.news.length === 0) {
                container.innerHTML = "<p>Nessuna notizia trovata.</p>";
                return;
            }

            data.news.forEach(n => {
                const card = document.createElement("div");
                card.className = "news-card";
                card.innerHTML = `
                    <h2>${n.titolo}</h2>
                    <p class="news-date">${n.data_pubblicazione}</p>
                    <p>${n.contenuto}</p>
                `;
                container.appendChild(card);
            });

        } catch (err) {
            container.innerHTML = "<p>Errore nel caricamento delle notizie.</p>";
            console.error("Errore:", err);
        }
    }

    // Avvio iniziale
    caricaCampionati();
</script>

</body>
</html>
