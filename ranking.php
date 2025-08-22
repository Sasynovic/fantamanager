<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FMPro - Classifica Ranking</title>
    <link rel="icon" href="public/background/logo.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="js/renderFooter.js" defer></script>
    <script src="js/showmenu.js" defer></script>
    <style>
        .main-body-content {
            margin-top: 20px;
        }

        .ranking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 0 20px;
        }

        .ranking-header h1 {
            font-size: 2.2rem;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .table-container {
            background-color: var(--blu-scuro, #1a2c56);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            margin: 0 auto;
            max-width: 95%;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            flex-wrap: wrap;
        }


        @media (max-width: 768px) {
            .pagination {
                gap: 4px;
            }

        }

        @media (max-width: 480px) {
            .pagination {
                gap: 2px;
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
                <a href="news.php">News</a>
            </li>
            <li class="menu-item active">
                <a href="ranking.php">Classifica Ranking</a>
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
                <h1>🏅 Classifica Ranking</h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
        </header>

        <div class="main-body">
            <div class="main-body-content" id="main-body-content">
                <table id="rankingTable">
                    <thead>
                    <tr>
                        <th>Posizione</th>
                        <th>Squadra</th>
                        <th>Punteggio</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td colspan="3">Caricamento dati...</td></tr>
                    </tbody>
                </table>
                <div id="rankingPagination" class="pagination"></div>
            </div>
        </div>

        <footer class="main-footer">
            <div class="swiper-container footer-swiper">
                <div class="swiper-wrapper" id="footerList"></div>
                <div class="swiper-button-prev footer-nav-prev">
                    <img src="public/chevron/chevronL.svg" alt="Indietro">
                </div>
                <div class="swiper-button-next footer-nav-next">
                    <img src="public/chevron/chevronR.svg" alt="Avanti">
                </div>
            </div>
        </footer>
    </div>
</div>

<script>
    let rankingData = [];
    let rankingPage = 1;
    let rankingItemsPerPage = 10;

    function loadRanking(page = 1) {
        rankingPage = page;
        const url = `${window.location.protocol}//${window.location.host}/endpoint/finanze_squadra/read.php`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.finanze_squadra && Array.isArray(data.finanze_squadra)) {
                    rankingData = data.finanze_squadra
                        .map(r => ({
                            id: r.id,
                            nome_squadra: r.nome_squadra,
                            punteggio: parseFloat(r.punteggio_ranking) || 0
                        }))
                        .sort((a, b) => b.punteggio - a.punteggio);

                    mostraRanking();
                    renderRankingPagination();
                } else {
                    document.querySelector('#rankingTable tbody').innerHTML =
                        '<tr><td colspan="3">Nessun dato disponibile.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Errore nel caricamento ranking:', error);
                document.querySelector('#rankingTable tbody').innerHTML =
                    '<tr><td colspan="3">Errore nel caricamento dei dati.</td></tr>';
            });
    }

    function mostraRanking() {
        const tbody = document.querySelector('#rankingTable tbody');
        tbody.innerHTML = '';

        const start = (rankingPage - 1) * rankingItemsPerPage;
        const end = start + rankingItemsPerPage;
        const datiPagina = rankingData.slice(start, end);

        if (datiPagina.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">Nessun dato disponibile.</td></tr>';
            return;
        }

        datiPagina.forEach((r, index) => {
            const posizione = start + index + 1;
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>${posizione}</td>
                <td><a href="squadra.php?id=${r.id}">${r.nome_squadra}</a></td><td>${r.punteggio}</td>
        `;
            tbody.appendChild(tr);
        });

        // Aggiunge righe vuote per arrivare a 10
        for (let i = datiPagina.length; i < rankingItemsPerPage; i++) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        `;
            tbody.appendChild(tr);
        }
    }

    function renderRankingPagination() {
        const paginationContainer = document.getElementById('rankingPagination');
        paginationContainer.innerHTML = '';

        const totalPages = Math.ceil(rankingData.length / rankingItemsPerPage);

        if (totalPages <= 1) return;

        // Configurazione per la paginazione
        const maxVisiblePages = window.innerWidth <= 480 ? 3 : (window.innerWidth <= 768 ? 5 : 7);
        const sidePages = Math.floor(maxVisiblePages / 2);

        // Bottone Precedente
        const prevButton = document.createElement('button');
        prevButton.className = 'page-btn prev';
        prevButton.innerHTML = '‹';
        prevButton.disabled = rankingPage === 1;
        prevButton.title = 'Pagina precedente';
        prevButton.addEventListener('click', () => {
            if (rankingPage > 1) loadRanking(rankingPage - 1);
        });
        paginationContainer.appendChild(prevButton);

        // Calcola l'intervallo di pagine da mostrare
        let startPage = Math.max(1, rankingPage - sidePages);
        let endPage = Math.min(totalPages, rankingPage + sidePages);

        // Aggiusta l'intervallo se siamo vicini agli estremi
        if (endPage - startPage + 1 < maxVisiblePages) {
            if (startPage === 1) {
                endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            } else if (endPage === totalPages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
        }

        // Mostra sempre la prima pagina se non è nell'intervallo
        if (startPage > 1) {
            const firstButton = document.createElement('button');
            firstButton.className = 'page-btn';
            firstButton.textContent = '1';
            firstButton.addEventListener('click', () => loadRanking(1));
            paginationContainer.appendChild(firstButton);

            // Aggiungi puntini se c'è un gap
            if (startPage > 2) {
                const dotsButton = document.createElement('button');
                dotsButton.className = 'page-btn dots';
                dotsButton.textContent = '...';
                dotsButton.title = `Vai alla pagina ${Math.max(1, startPage - 5)}`;
                dotsButton.addEventListener('click', () => {
                    const targetPage = Math.max(1, startPage - 5);
                    loadRanking(targetPage);
                });
                paginationContainer.appendChild(dotsButton);
            }
        }

        // Pagine nell'intervallo visibile
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.className = 'page-btn' + (i === rankingPage ? ' active' : '');
            pageButton.textContent = i;
            pageButton.title = `Pagina ${i}`;
            pageButton.addEventListener('click', () => loadRanking(i));
            paginationContainer.appendChild(pageButton);
        }

        // Mostra sempre l'ultima pagina se non è nell'intervallo
        if (endPage < totalPages) {
            // Aggiungi puntini se c'è un gap
            if (endPage < totalPages - 1) {
                const dotsButton = document.createElement('button');
                dotsButton.className = 'page-btn dots';
                dotsButton.textContent = '...';
                dotsButton.title = `Vai alla pagina ${Math.min(totalPages, endPage + 5)}`;
                dotsButton.addEventListener('click', () => {
                    const targetPage = Math.min(totalPages, endPage + 5);
                    loadRanking(targetPage);
                });
                paginationContainer.appendChild(dotsButton);
            }

            const lastButton = document.createElement('button');
            lastButton.className = 'page-btn';
            lastButton.textContent = totalPages;
            lastButton.addEventListener('click', () => loadRanking(totalPages));
            paginationContainer.appendChild(lastButton);
        }

        // Bottone Successivo
        const nextButton = document.createElement('button');
        nextButton.className = 'page-btn next';
        nextButton.innerHTML = '›';
        nextButton.disabled = rankingPage === totalPages;
        nextButton.title = 'Pagina successiva';
        nextButton.addEventListener('click', () => {
            if (rankingPage < totalPages) loadRanking(rankingPage + 1);
        });
        paginationContainer.appendChild(nextButton);
    }

    // Aggiorna la paginazione quando si ridimensiona la finestra
    window.addEventListener('resize', () => {
        if (rankingData.length > 0) {
            renderRankingPagination();
        }
    });

    loadRanking();
</script>
</body>
</html>
<script src="js/renderFooter.js"></script>