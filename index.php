<link rel="stylesheet" href="style.css">
<div class="main-container">

    <aside class="left">
        <div class="heading">
            <h2>Fantacalcio</h2>
            <h3>Fantamanager</h3>
        </div>
        <div class="menu">
            <ul class="menu-list">
                <li class="menu-option" onclick="location.href='index.php'">DASHBOARD</li>
                <li class="menu-option">RICERCA</li>
                <li class="menu-option">ALBO D'ORO</li>
                <li class="menu-option" onclick="location.href='vendita.php'">SQUADRE IN VENDITA</li>
                <li class="menu-option">TOOL SCAMBI</li>
                <li class="menu-option">CONTATTI</li>
            </ul>
        </div>
    </aside>

    <div class="actual-selection">
    <header >
        <h1>DASHBOARD</h1>
    </header>


    <main>
        <!-- SEZIONE DIVISIONI -->
        <section>
            <h2>Elenco Divisioni</h2>
            <div class="grid" id="divisioni-container"></div>
        </section>
    </main>
</div>
</div>

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
    caricaDivisioni();

</script>