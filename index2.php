<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Homepage - Divisioni e Notizie</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #dc3545;
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        main {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        h2 {
            margin-top: 3rem;
            margin-bottom: 1rem;
            color: #333;
            text-align: center;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }

        .circle-card {
            background-color: white;
            border-radius: 50%;
            width: 160px;
            height: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            margin: 0 auto;
        }

        .circle-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
            background-color: #f9f9f9;
        }

        .circle-card img {
            height: 50%;
            margin-bottom: 0.5rem;
        }

        .circle-card span {
            font-weight: bold;
            text-align: center;
        }

        .card {
            background-color: white;
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        footer {
            margin-top: 4rem;
            padding: 2rem 1rem;
            text-align: center;
            color: #777;
        }

        .buttons {
            text-align: center;
            margin: 2rem 0;
        }

        .buttons button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            margin: 0.3rem;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .buttons button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<header>
    <h1>Divisioni</h1>
</header>

<main>
    <!-- SEZIONE DIVISIONI -->
    <section>
        <h2>Elenco Divisioni</h2>
        <div class="grid" id="divisioni-container"></div>
    </section>

    <!-- SEZIONE NEWS -->
    <section>
        <h2>Ultime Notizie</h2>
            <button>Vedi Tutte</button>
        </div>
        <div class="grid" id="news-container"></div>
    </section>
</main>

<footer>
    &copy; 2025 Barrettasalvatore.it - Tutti i diritti riservati
</footer>

<script>
    async function caricaDivisioni() {
        const container = document.getElementById("divisioni-container");
        container.innerHTML = "<p>Caricamento...</p>";

        try {
            const res = await fetch("https://barrettasalvatore.it/endpoint/divisione/read.php");
            const data = await res.json();

            container.innerHTML = "";
            if (!data.divisioni || data.divisioni.length === 0) {
                container.innerHTML = "<p>Nessuna divisione trovata.</p>";
                return;
            }

            data.divisioni.forEach(div => {
                const link = document.createElement("a");
                link.href = `divisione.php?id_divisione=${div.id}`;
                link.className = "circle-card";

                const img = document.createElement("img");
                img.src = div.bandiera
                    ? `https://barrettasalvatore.it/public/flag/${div.bandiera}`
                    : "https://via.placeholder.com/48?text=NA";
                img.alt = div.nome_divisione;

                const label = document.createElement("span");
                label.textContent = div.nome_divisione;

                link.appendChild(img);
                link.appendChild(label);
                container.appendChild(link);
            });

        } catch (err) {
            container.innerHTML = "<p>Errore nel caricamento delle divisioni.</p>";
            console.error(err);
        }
    }

    async function caricaUltimeNews() {
        const container = document.getElementById("news-container");
        container.innerHTML = "<p>Caricamento...</p>";

        try {
            const res = await fetch("https://barrettasalvatore.it/endpoint/news/read.php?limit=5");
            const data = await res.json();

            container.innerHTML = "";
            if (!data.news || data.news.length === 0) {
                container.innerHTML = "<p>Nessuna notizia disponibile.</p>";
                return;
            }

            data.news.forEach(news => {
                const card = document.createElement("div");
                card.className = "card";

                const titolo = document.createElement("h3");
                titolo.textContent = news.titolo;

                const contenuto = document.createElement("p");
                contenuto.textContent = news.contenuto.substring(0, 100) + "...";

                const data_pubblicazione = document.createElement("p");
                data_pubblicazione.style.fontSize = "0.8rem";
                data_pubblicazione.style.color = "#999";
                data_pubblicazione.textContent = `Pubblicata il ${news.data_pubblicazione}`;

                card.appendChild(titolo);
                card.appendChild(contenuto);
                card.appendChild(data_pubblicazione);
                container.appendChild(card);
            });

        } catch (err) {
            container.innerHTML = "<p>Errore nel caricamento delle news.</p>";
            console.error(err);
        }
    }

    caricaDivisioni();
    caricaUltimeNews();
</script>

</body>
</html>
