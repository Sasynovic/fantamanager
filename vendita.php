<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FMPro</title>
    <link rel="icon" href="public/background/logo.png" type="image/png">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="js/showmenu.js" defer></script>
    <style>
        /* STILE SPECIFICO PER LE SQUADRE IN VENDITA */
        .filter-section{
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .main-body {
            height: 92%;
            overflow-y: scroll;
        }
        .table-container{
            width: 100%;
        }
        .filter-control {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Stile per le stelline */
        .rating {
            color: var(--oro);
        }

        @media (max-width: 992px) {
            .main-body-content{
                width: auto;
                margin: 15px;
            }

        }

    </style>
</head>

<body>
<div class="main-container">
    <aside class="main-menu">
        <div class="menu-header">
            <img src="public/background/logo.png" alt="Logo" class="logo" width="80px" height="80px">
            <h3>FMPro</h3>
        </div>

        <ul class="menu-list">
            <li class="menu-item">
                <a href="index.php">Dashboard</a>
            </li>
            <li class="menu-item">
                <a href="albo.php">Albo d'oro</a>
            </li>
            <li class="menu-item">
                <a href="vendita.php">Squadre in vendita</a>
            </li>
            <li class="menu-item">
                <a href="tool.php">Tool scambi</a>
            </li>
            <li class="menu-item">
                <a href="regolamento.php">Regolamento</a>
            </li>
            <li class="menu-item">
                <a href="ricerca.php">Ricerca</a>
            </li>
            <li class="menu-item">
                <a href="contatti.php">Contatti</a>
            </li>
        </ul>
    </aside>

    <div class="main-content">
        <header class="main-header">
            <div class="main-text-header">
                <button class="back-button" onclick="window.history.back();">
                    <img src="public/chevron/chevronL.svg" alt="Indietro" height="40px" width="40px">
                </button>
                <h1>Squadre in vendita</h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
        </header>

        <div class="main-body">
            <h3  style="text-align: center; padding: 10px; background-color: var(--accento)" >   Clicca sul nome della squadra per maggiori dettagli</h3>
            <div class="main-body-content" id="main-body-content">

                <div class="filter-section">
                    <div class="filter-control">
                        <label for="filter-prezzo">Prezzo massimo</label>
                        <select id="filter-prezzo">
                            <option value="">Tutti</option>
                            <option value="25">25 €</option>
                            <option value="30">30 €</option>
                            <option value="35">35 €</option>
                        </select>
                    </div>

                    <div class="filter-control">
                        <label for="filter-rate">Valutazione minima</label>
                        <select id="filter-rate">
                            <option value="">Tutte</option>
                            <option value="1">★☆☆☆☆</option>
                            <option value="2">★★☆☆☆</option>
                            <option value="3">★★★☆☆</option>
                            <option value="4">★★★★☆</option>
                            <option value="5">★★★★★</option>
                        </select>
                    </div>
                </div>
                <div class="table-container">
                    <table id="venditaTable">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Comp.</th>
                            <th>Prezzo</th>
                            <th>Val.</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><td colspan="4">Caricamento dati...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="pagination" class="pagination">
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    let squadreData = [];
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalPages = 1;
    let currentPrezzo = '';
    let currentRate = '';

    // Funzione per convertire il valore numerico delle rate in stelline
    function getRatingStars(rate) {
        if (!rate) return '☆☆☆☆☆';

        // Assumiamo che il rate sia un numero da 1 a 5
        const rateNum = parseInt(rate);
        const maxRate = 5;

        // Convertiamo il numero in stelline
        let stars = '';
        for (let i = 1; i <= maxRate; i++) {
            stars += i <= rateNum ? '★' : '☆';
        }

        return stars;
    }

    function mostraSquadre() {
        const tbody = document.querySelector('#venditaTable tbody');
        tbody.innerHTML = '';

        if (squadreData.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="4">Nessuna squadra in vendita al momento.</td>';
            tbody.appendChild(tr);
            return;
        }

        // Filtra i dati in base ai filtri selezionati
        let datiFiltrati = [...squadreData];

        if (currentPrezzo) {
            datiFiltrati = datiFiltrati.filter(squadra => squadra.prezzo <= parseInt(currentPrezzo));
        }

        if (currentRate) {
            datiFiltrati = datiFiltrati.filter(squadra => squadra.rate >= parseInt(currentRate));
        }

        if (datiFiltrati.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="4">Nessuna squadra trovata con i filtri selezionati.</td>';
            tbody.appendChild(tr);
            return;
        }

        // Aggiunge le righe dei dati reali
        datiFiltrati.forEach(squadra => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                    <td>
                        <a href="squadra.php?id=${squadra.id}" class="view-btn">${squadra.nome_squadra}</a>
                    </td>
                    <td id="competizioni-${squadra.id}">Caricamento...</td>
                    <td>${squadra.prezzo} €</td>
                    <td><span class="rating">${getRatingStars(squadra.rate)}</span></td>
                    <td class="action-buttons">
                        <button class="buy-btn">Acquista</button>
                    </td>
                `;
            const button = tr.querySelector('.buy-btn');
            button.addEventListener('click', () => acquistaSquadra(squadra.nome_squadra));
            tbody.appendChild(tr);

            // Carica le competizioni per questa squadra
            loadCompetizioniSquadra(squadra.id);
        });
    }

    function loadSquadre(page = 1) {
        currentPage = page;

        let urlSquadre = `${window.location.protocol}//${window.location.host}/endpoint/squadra/read.php?vendita=1`;

        // Aggiungi i parametri dei filtri se presenti
        if (currentPrezzo) {
            urlSquadre += `&prezzo=${currentPrezzo}`;
        }
        if (currentRate) {
            urlSquadre += `&rate=${currentRate}`;
        }

        urlSquadre += `&limit=${itemsPerPage}&page=${page}`;


        // Mostra indicatore di caricamento
        document.querySelector('#venditaTable tbody').innerHTML =
            '<tr><td colspan="4">Caricamento dati...</td></tr>';

        fetch(urlSquadre)
            .then(response => response.json())
            .then(data => {
                if (data.squadra && Array.isArray(data.squadra)) {
                    squadreData = data.squadra;
                    mostraSquadre();

                    // Aggiorna le informazioni di paginazione
                    if (data.pagination) {
                        totalPages = data.pagination.total_pages;
                        itemsPerPage = data.pagination.items_per_page;
                        currentPage = data.pagination.current_page;
                        renderPagination(data.pagination);
                    }
                } else {
                    document.querySelector('#venditaTable tbody').innerHTML =
                        '<tr><td colspan="4">Nessuna squadra in vendita trovata.</td></tr>';
                    document.getElementById('pagination').innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Errore nel caricamento squadre:', error);
                document.querySelector('#venditaTable tbody').innerHTML =
                    '<tr><td colspan="4">Errore nel caricamento dei dati.</td></tr>';
                document.getElementById('pagination').innerHTML = '';
            });
    }

    // Funzione per caricare le competizioni di una squadra
    function loadCompetizioniSquadra(id) {
        const cellCompetizioni = document.getElementById(`competizioni-${id}`);

        fetch(`${window.location.protocol}//${window.location.host}/endpoint/partecipazione/read.php?id_squadra=${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Errore nella risposta del server');
                }
                return response.json();
            })
            .then(data => {
                if (data) {
                    const nomiCompetizioni = data.nome_competizione;
                    cellCompetizioni.textContent = nomiCompetizioni || 'Nessuna competizione';
                } else {
                    cellCompetizioni.textContent = 'Nessuna competizione';
                }
            })
            .catch(error => {
                console.error('Errore nel caricamento delle competizioni:', error);
                cellCompetizioni.textContent = 'Errore caricamento';
            });
    }

    // Funzione per gestire l'acquisto di una squadra
    function acquistaSquadra(nomeSquadra) {
        const conferma1 = confirm(`Sei sicuro di voler acquistare ${nomeSquadra}?`);
        if (!conferma1) return;

        const conferma2 = confirm("Questa operazione è irreversibile! Sei sicuro?");
        if (!conferma2) return;

        const nome = prompt("Inserisci il tuo nome:");
        const cognome = prompt("Inserisci il tuo cognome:");

        if (!nome || !cognome) {
            alert("Tutti i campi sono obbligatori. Operazione annullata.");
            return;
        }

        const numeroWhatsApp = "3371447208";
        const messaggio = `Richiesta acquisto squadra: ${nomeSquadra}%0ANome: ${nome}%0ACognome: ${cognome}`;
        const url = `https://wa.me/${numeroWhatsApp}?text=${messaggio}`;

        window.open(url);
    }

    // Gestori eventi per i filtri
    document.getElementById('filter-prezzo').addEventListener('change', function() {
        currentPrezzo = this.value;
        loadSquadre(1); // Ricarica dalla prima pagina con i nuovi filtri
    });

    document.getElementById('filter-rate').addEventListener('change', function() {
        currentRate = this.value;
        loadSquadre(1); // Ricarica dalla prima pagina con i nuovi filtri
    });

    function renderPagination(paginationData) {
        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = '';

        if (paginationData.total_pages <= 1) {
            return; // Non mostrare paginazione se c'è una sola pagina
        }

        // Pulsante pagina precedente
        const prevButton = document.createElement('button');
        prevButton.className = 'page-btn prev';
        prevButton.innerHTML = '&laquo;';
        prevButton.disabled = !paginationData.has_previous_page;
        prevButton.addEventListener('click', () => {
            if (paginationData.has_previous_page) {
                loadSquadre(currentPage - 1);
            }
        });
        paginationContainer.appendChild(prevButton);

        // Numeri delle pagine
        const startPage = Math.max(1, paginationData.current_page - 2);
        const endPage = Math.min(paginationData.total_pages, paginationData.current_page + 2);

        // Se siamo vicini all'inizio, mostra più pagine successive
        if (startPage === 1 && endPage < 5 && paginationData.total_pages >= 5) {
            for (let i = 1; i <= 5; i++) {
                addPageButton(i, paginationData.current_page);
            }
        }
        // Se siamo vicini alla fine, mostra più pagine precedenti
        else if (endPage === paginationData.total_pages && endPage - startPage < 4 && paginationData.total_pages >= 5) {
            for (let i = paginationData.total_pages - 4; i <= paginationData.total_pages; i++) {
                addPageButton(i, paginationData.current_page);
            }
        }
        // Altrimenti mostra le pagine intorno a quella corrente
        else {
            for (let i = startPage; i <= endPage; i++) {
                addPageButton(i, paginationData.current_page);
            }
        }

        // Pulsante pagina successiva
        const nextButton = document.createElement('button');
        nextButton.className = 'page-btn next';
        nextButton.innerHTML = '&raquo;';
        nextButton.disabled = !paginationData.has_next_page;
        nextButton.addEventListener('click', () => {
            if (paginationData.has_next_page) {
                loadSquadre(currentPage + 1);
            }
        });
        paginationContainer.appendChild(nextButton);

        function addPageButton(pageNum, currentPage) {
            const pageButton = document.createElement('button');
            pageButton.className = 'page-btn' + (pageNum === currentPage ? ' active' : '');
            pageButton.textContent = pageNum;
            pageButton.addEventListener('click', () => {
                loadSquadre(pageNum);
            });
            paginationContainer.appendChild(pageButton);
        }
    }

    // Inizializzazione
    loadSquadre();
</script>
</body>
</html>