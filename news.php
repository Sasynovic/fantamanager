<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FMPro - News</title>
    <link rel="icon" href="public/background/logo.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="js/renderFooter.js" defer></script>
    <script src="js/showmenu.js" defer></script>
    <style>
        .news-content {
            min-height: 300px;
            padding: 20px 0;
        }

        .search-container {
            background-color: var(--blu-scuro, #1a2c56);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .search-wrapper {
            position: relative;
            max-width: 500px;
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

        .news-item {
            color: black;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            border: 2px solid var(--accento);
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .news-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }

        .news-item h3 {
            margin-top: 0;
            color: var(--accento);
            font-size: 1.5rem;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .news-item .news-meta {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .news-item .news-meta::before {
            content: "📅";
            margin-right: 5px;
        }

        .news-item .news-contenuto {
            line-height: 1.6;
            color: #333;
        }

        .news-contenuto > *{
            color: #333;
        }

        .news-item .news-contenuto p {
            margin-bottom: 15px;
        }

        .highlight {
            background-color: yellow;
            color: black;
            font-weight: bold;
            border-radius: 3px;
            padding: 1px 2px;
        }

        .news-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 40px;
            flex-wrap: wrap;
        }


        .no-news, .error-message {
            text-align: center;
            padding: 40px;
            font-size: 1.2rem;
            color: #777;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px dashed #ccc;
        }

        .no-results {
            text-align: center;
            color: #777;
            font-style: italic;
            padding: 40px 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px dashed #ccc;
        }

        @media (max-width: 768px) {
            .news-item {
                padding: 15px;
                margin-bottom: 20px;
            }

            .news-item h3 {
                font-size: 1.3rem;
            }

            .news-pagination {
                flex-wrap: wrap;
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

            .search-wrapper {
                max-width: 100%;
            }

            .news-pagination {
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
            <li class="menu-item active">
                <a href="news.php">News</a>
            </li>
            <li class="menu-item">
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
                <h1>📰 News</h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
        </header>

        <div class="main-body" style="overflow-y: scroll;">
            <div class="main-body-content" id="main-body-content" style="padding: 30px;">

                <!-- Barra di ricerca -->
                <div class="search-container">
                    <div class="search-wrapper">
                        <input
                                type="text"
                                id="searchInput"
                                class="search-input"
                                placeholder="Cerca nelle news per titolo o contenuto..."
                                autocomplete="off"
                        >
                        <button id="clearSearch" class="clear-search" title="Cancella ricerca">×</button>

                    </div>
                    <div id="searchStats" class="search-stats"></div>
                </div>

                <div class="news-content" id="news-content">
                    <!-- Le news verranno caricate qui -->
                </div>
                <div class="news-pagination" id="news-pagination">
                    <!-- La paginazione verrà generata dinamicamente -->
                </div>
            </div>
        </div>

        <footer class="main-footer">
            <div class="swiper-container footer-swiper">
                <div class="swiper-wrapper" id="footerList">
                    <!-- Gli elementi division-ball verranno inseriti qui tramite JavaScript -->
                </div>
                <!-- Aggiunti i pulsanti di navigazione -->
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
    document.addEventListener('DOMContentLoaded', function() {
        const newsContent = document.getElementById('news-content');
        const paginationContainer = document.getElementById('news-pagination');
        const searchInput = document.getElementById('searchInput');
        const clearButton = document.getElementById('clearSearch');
        const searchStats = document.getElementById('searchStats');

        let allNews = [];
        let filteredNews = [];
        let currentPage = 1;
        let currentSearch = '';
        const itemsPerPage = 5;

        // Inizializza
        loadAllNews();

        // Event listeners per la ricerca
        searchInput.addEventListener('input', (e) => {
            applySearch(e.target.value);
        });

        clearButton.addEventListener('click', () => {
            searchInput.value = '';
            applySearch('');
            searchInput.focus();
        });

        function loadAllNews() {
            // Carica tutte le news senza paginazione per poter fare ricerca locale
            const url = `/endpoint/news/read.php?visibile=1&limit=1000`; // Limite alto per prendere tutte

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (!data.news || !Array.isArray(data.news)) {
                        throw new Error('Formato dati non valido');
                    }

                    allNews = data.news;
                    applySearch(''); // Inizializza con tutti i risultati
                })
                .catch(error => {
                    console.error('Errore nel caricamento delle news:', error);
                    newsContent.innerHTML = `<div class="error-message">Errore nel caricamento delle news</div>`;
                    paginationContainer.innerHTML = '';
                });
        }

        function applySearch(searchTerm) {
            currentSearch = searchTerm.toLowerCase().trim();

            if (currentSearch === '') {
                filteredNews = [...allNews];
                clearButton.style.display = 'none';
            } else {
                filteredNews = allNews.filter(news =>
                    news.titolo.toLowerCase().includes(currentSearch) ||
                    news.contenuto.toLowerCase().includes(currentSearch) ||
                    (news.nome_competizione && news.nome_competizione.toLowerCase().includes(currentSearch))
                );
                clearButton.style.display = 'flex';
            }

            // Aggiorna le statistiche
            updateSearchStats();

            // Reset alla prima pagina quando si cerca
            currentPage = 1;

            // Aggiorna visualizzazione
            renderNews();
            renderPagination();
        }

        function updateSearchStats() {
            if (currentSearch === '') {
                searchStats.textContent = `Visualizzando ${allNews.length} news totali`;
            } else {
                searchStats.textContent = `Trovate ${filteredNews.length} news per "${currentSearch}"`;
            }
        }

        function highlightSearchTerm(text, searchTerm) {
            if (!searchTerm || !text) return text;

            const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
            return text.replace(regex, '<span class="highlight">$1</span>');
        }

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function renderNews() {
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageNews = filteredNews.slice(startIndex, endIndex);

            if (pageNews.length === 0) {
                if (currentSearch) {
                    newsContent.innerHTML = `
                        <div class="no-results">
                            Nessuna news trovata per "${currentSearch}"<br>
                            <small>Prova con un termine di ricerca diverso</small>
                        </div>`;
                } else {
                    newsContent.innerHTML = `<div class="no-news">Nessuna news disponibile</div>`;
                }
                return;
            }

            let html = '';
            pageNews.forEach(news => {
                const highlightedTitle = highlightSearchTerm(news.titolo, currentSearch);
                const highlightedContent = highlightSearchTerm(news.contenuto, currentSearch);

                html += `
                    <div class="news-item">
                        <h3>${highlightedTitle}</h3>
                        <div class="news-meta">
                            ${formatDate(news.data_pubblicazione)} |
                            ${news.nome_competizione || 'Generale'}
                        </div>
                        <div class="news-contenuto">${highlightedContent}</div>
                    </div>
                `;
            });

            newsContent.innerHTML = html;
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredNews.length / itemsPerPage);

            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            // Configurazione per la paginazione
            const maxVisiblePages = window.innerWidth <= 480 ? 3 : (window.innerWidth <= 768 ? 5 : 7);
            const sidePages = Math.floor(maxVisiblePages / 2);

            let html = '';

            // Bottone Precedente
            html += `
                <button class="page-btn prev" ${currentPage === 1 ? 'disabled' : ''}
                    onclick="changePage(${currentPage - 1})" title="Pagina precedente">
                    ‹
                </button>
            `;

            // Calcola l'intervallo di pagine da mostrare
            let startPage = Math.max(1, currentPage - sidePages);
            let endPage = Math.min(totalPages, currentPage + sidePages);

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
                html += `<button class="page-btn" onclick="changePage(1)">1</button>`;

                // Aggiungi puntini se c'è un gap
                if (startPage > 2) {
                    html += `
                        <button class="page-btn dots" onclick="changePage(${Math.max(1, startPage - 5)})"
                            title="Vai alla pagina ${Math.max(1, startPage - 5)}">
                            ...
                        </button>`;
                }
            }

            // Pagine nell'intervallo visibile
            for (let i = startPage; i <= endPage; i++) {
                html += `
                    <button class="page-btn ${i === currentPage ? 'active' : ''}"
                        onclick="changePage(${i})" title="Pagina ${i}">
                        ${i}
                    </button>
                `;
            }

            // Mostra sempre l'ultima pagina se non è nell'intervallo
            if (endPage < totalPages) {
                // Aggiungi puntini se c'è un gap
                if (endPage < totalPages - 1) {
                    html += `
                        <button class="page-btn dots" onclick="changePage(${Math.min(totalPages, endPage + 5)})"
                            title="Vai alla pagina ${Math.min(totalPages, endPage + 5)}">
                            ...
                        </button>`;
                }

                html += `<button class="page-btn" onclick="changePage(${totalPages})">${totalPages}</button>`;
            }

            // Bottone Successivo
            html += `
                <button class="page-btn next" ${currentPage === totalPages ? 'disabled' : ''}
                    onclick="changePage(${currentPage + 1})" title="Pagina successiva">
                    ›
                </button>
            `;

            paginationContainer.innerHTML = html;
        }

        window.changePage = function(page) {
            if (page !== currentPage && page >= 1) {
                currentPage = page;
                renderNews();
                renderPagination();
                document.querySelector('.search-container').scrollIntoView({ behavior: 'smooth' });
            }
        };

        function formatDate(dateString) {
            if (!dateString) return '';
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('it-IT', options);
        }

        // Aggiorna la paginazione quando si ridimensiona la finestra
        window.addEventListener('resize', () => {
            if (filteredNews.length > 0) {
                renderPagination();
            }
        });
    });
</script>
</html>