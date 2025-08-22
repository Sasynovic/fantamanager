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
            margin-bottom: 20px;
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

        .search-container {
            background-color: var(--blu-scuro, #1a2c56);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .search-wrapper {
            position: relative;
            max-width: 400px;
            margin: 0 auto;
        }

        .search-input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: 2px solid var(--accento, #3c74f5);
            border-radius: 25px;
            background-color: white;
            color: var(--blu-scuro, #1a2c56);
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .search-input:focus {
            border-color: var(--blu, #294582);
            box-shadow: 0 0 0 3px rgba(60, 116, 245, 0.1);
            transform: translateY(-1px);
        }

        .search-input::placeholder {
            color: #999;
            font-style: italic;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accento, #3c74f5);
            font-size: 1.2rem;
            pointer-events: none;
        }

        .clear-search {
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            font-size: 1.3rem;
            cursor: pointer;
            padding: 2px;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .clear-search:hover {
            background-color: #f0f0f0;
            color: #666;
        }

        .search-stats {
            text-align: center;
            margin-top: 15px;
            color: white;
            font-size: 0.9rem;
            opacity: 0.8;
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



        .no-results {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 40px 20px;
        }

        .highlight {
            background-color: yellow;
            color: black;
            font-weight: bold;
            border-radius: 3px;
            padding: 1px 2px;
        }

        @media (max-width: 768px) {
            .pagination {
                gap: 4px;
            }


            .search-input {
                font-size: 0.9rem;
                padding: 10px 40px 10px 16px;
            }

            .search-container {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .pagination {
                gap: 2px;
            }


            .search-wrapper {
                max-width: 100%;
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

    <div class="main-content" >
        <header class="main-header">
            <div class="main-text-header">
                <button class="back-button" onclick="window.history.back();">
                    <img src="public/chevron/chevronL.svg" alt="Indietro" height="40px" width="40px">
                </button>
                <h1>🏅 Classifica Ranking</h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
        </header>

        <div class="main-body" style="overflow-y: scroll">
            <div class="main-body-content" id="main-body-content">
                <!-- Barra di ricerca -->
                <div class="search-container">
                    <div class="search-wrapper">
                        <input
                            type="text"
                            id="searchInput"
                            class="search-input"
                            placeholder="Cerca squadra per nome..."
                            autocomplete="off"
                        >
                        <button id="clearSearch" class="clear-search" title="Cancella ricerca">×</button>
                    </div>
                    <div id="searchStats" class="search-stats"></div>
                </div>

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
    let filteredData = [];
    let rankingPage = 1;
    let rankingItemsPerPage = 30;
    let currentSearch = '';

    // Elementi DOM
    const searchInput = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearSearch');
    const searchStats = document.getElementById('searchStats');

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

                    // Applica il filtro corrente
                    applySearch(currentSearch);
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

    function applySearch(searchTerm) {
        currentSearch = searchTerm.toLowerCase().trim();

        if (currentSearch === '') {
            filteredData = [...rankingData];
            clearButton.style.display = 'none';
        } else {
            filteredData = rankingData.filter(team =>
                team.nome_squadra.toLowerCase().includes(currentSearch)
            );
            clearButton.style.display = 'flex';
        }

        // Aggiorna le statistiche
        updateSearchStats();

        // Reset alla prima pagina quando si cerca
        rankingPage = 1;

        // Aggiorna visualizzazione
        mostraRanking();
        renderRankingPagination();
    }

    function updateSearchStats() {
        if (currentSearch === '') {
            searchStats.textContent = `Visualizzando ${rankingData.length} squadre totali`;
        } else {
            searchStats.textContent = `Trovate ${filteredData.length} squadre${filteredData.length !== 1 ? '' : ''} per "${currentSearch}"`;
        }
    }

    function highlightSearchTerm(text, searchTerm) {
        if (!searchTerm) return text;

        const regex = new RegExp(`(${searchTerm})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }

    function mostraRanking() {
        const tbody = document.querySelector('#rankingTable tbody');
        tbody.innerHTML = '';

        const start = (rankingPage - 1) * rankingItemsPerPage;
        const end = start + rankingItemsPerPage;
        const datiPagina = filteredData.slice(start, end);

        if (datiPagina.length === 0) {
            if (currentSearch) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="no-results">
                            Nessuna squadra trovata per "${currentSearch}"<br>
                            <small>Prova con un termine di ricerca diverso</small>
                        </td>
                    </tr>`;
            } else {
                tbody.innerHTML = '<tr><td colspan="3">Nessun dato disponibile.</td></tr>';
            }
            return;
        }

        datiPagina.forEach((r, index) => {
            // Calcola la posizione nella classifica originale
            const originalIndex = rankingData.findIndex(team => team.id === r.id);
            const posizione = originalIndex + 1;

            const tr = document.createElement('tr');
            const highlightedName = highlightSearchTerm(r.nome_squadra, currentSearch);

            tr.innerHTML = `
                <td>${posizione}</td>
                <td><a href="squadra.php?id=${r.id}">${highlightedName}</a></td>
                <td>${r.punteggio}</td>
            `;
            tbody.appendChild(tr);
        });

        // Aggiunge righe vuote per mantenere la consistenza visiva
        for (let i = datiPagina.length; i < Math.min(rankingItemsPerPage, 10); i++) {
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

        const totalPages = Math.ceil(filteredData.length / rankingItemsPerPage);

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
            if (rankingPage > 1) {
                rankingPage--;
                mostraRanking();
                renderRankingPagination();
            }
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
            firstButton.addEventListener('click', () => {
                rankingPage = 1;
                mostraRanking();
                renderRankingPagination();
            });
            paginationContainer.appendChild(firstButton);

            // Aggiungi puntini se c'è un gap
            if (startPage > 2) {
                const dotsButton = document.createElement('button');
                dotsButton.className = 'page-btn dots';
                dotsButton.textContent = '...';
                dotsButton.title = `Vai alla pagina ${Math.max(1, startPage - 5)}`;
                dotsButton.addEventListener('click', () => {
                    rankingPage = Math.max(1, startPage - 5);
                    mostraRanking();
                    renderRankingPagination();
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
            pageButton.addEventListener('click', () => {
                rankingPage = i;
                mostraRanking();
                renderRankingPagination();
            });
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
                    rankingPage = Math.min(totalPages, endPage + 5);
                    mostraRanking();
                    renderRankingPagination();
                });
                paginationContainer.appendChild(dotsButton);
            }

            const lastButton = document.createElement('button');
            lastButton.className = 'page-btn';
            lastButton.textContent = totalPages;
            lastButton.addEventListener('click', () => {
                rankingPage = totalPages;
                mostraRanking();
                renderRankingPagination();
            });
            paginationContainer.appendChild(lastButton);
        }

        // Bottone Successivo
        const nextButton = document.createElement('button');
        nextButton.className = 'page-btn next';
        nextButton.innerHTML = '›';
        nextButton.disabled = rankingPage === totalPages;
        nextButton.title = 'Pagina successiva';
        nextButton.addEventListener('click', () => {
            if (rankingPage < totalPages) {
                rankingPage++;
                mostraRanking();
                renderRankingPagination();
            }
        });
        paginationContainer.appendChild(nextButton);
    }

    // Event listeners per la ricerca
    searchInput.addEventListener('input', (e) => {
        applySearch(e.target.value);
    });

    clearButton.addEventListener('click', () => {
        searchInput.value = '';
        applySearch('');
        searchInput.focus();
    });

    // Aggiorna la paginazione quando si ridimensiona la finestra
    window.addEventListener('resize', () => {
        if (filteredData.length > 0) {
            renderRankingPagination();
        }
    });

    // Inizializza
    loadRanking();
</script>
</body>
</html>
<script src="js/renderFooter.js"></script>