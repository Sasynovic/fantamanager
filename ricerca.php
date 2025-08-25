<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>FMPro - Ricerca</title>
    <link rel="icon" href="public/background/logo.png" type="image/png">

    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="js/renderFooter.js" defer></script>
    <script src="js/showmenu.js" defer></script>

    <style>
        .main-body-content{
            height: 100%;
            overflow-y: scroll;
        }
        .main-body-content{
            justify-content: flex-start;
        }
    </style>
</head>

<body>
<div class="main-container">
    <!-- Menu laterale -->
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
            <li class="menu-item active">
                <a href="ricerca.php">Ricerca</a>
            </li>
            <li class="menu-item">
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
                    <img src="public/chevron/chevronL.svg" alt="Indietro" height="40" width="40"/>
                </button>
                <h1>Ricerca Squadre</h1>
                <h1 id="hamburger-menu"></h1>
            </div>
        </header>

        <div class="main-body">
            <div class="main-body-content" id="main-body-content">
                <!-- Barra di ricerca avanzata -->
                <div class="search-container">
                    <div class="search-wrapper">
                        <input
                                type="text"
                                id="searchInput"
                                class="search-input"
                                placeholder="Cerca squadre per nome o presidente..."
                                autocomplete="off"
                        >
                        <button id="clearSearch" class="clear-search" title="Cancella ricerca">×</button>
                    </div>
                    <div id="searchStats" class="search-stats"></div>
                </div>

                <table class="squadra-table" id="squadraTable">
                    <thead>
                    <tr>
                        <th>Squadra</th>
                        <th>Presidente</th>
                        <th>Dettagli</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td colspan="3">Caricamento dati...</td></tr>
                    </tbody>
                </table>
                <div id="pagination" class="news-pagination"></div>
            </div>
        </div>

        <footer class="main-footer">
            <div class="swiper-container footer-swiper">
                <div class="swiper-wrapper" id="footerList"></div>
                <div class="swiper-button-prev footer-nav-prev">
                    <img src="public/chevron/chevronL.svg" alt="Indietro"/>
                </div>
                <div class="swiper-button-next footer-nav-next">
                    <img src="public/chevron/chevronR.svg" alt="Avanti"/>
                </div>
            </div>
        </footer>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const clearButton = document.getElementById('clearSearch');
        const searchStats = document.getElementById('searchStats');

        let allSquadre = [];
        let filteredSquadre = [];
        let currentPage = 1;
        let currentSearch = '';
        const itemsPerPage = 10;

        // Inizializza
        fetchAllSquadre();

        // Event listeners per la ricerca
        searchInput.addEventListener('input', (e) => {
            applySearch(e.target.value);
        });

        clearButton.addEventListener('click', () => {
            searchInput.value = '';
            applySearch('');
            searchInput.focus();
        });

        function fetchAllSquadre() {
            const url = `${window.location.origin}/endpoint/squadra/read.php?limit=1000`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (!data.squadra || !Array.isArray(data.squadra)) {
                        throw new Error('Formato dati non valido');
                    }

                    allSquadre = data.squadra;
                    applySearch(''); // Inizializza con tutti i risultati
                })
                .catch(error => {
                    console.error('Errore nel caricamento delle squadre:', error);
                    const tbody = document.querySelector('#squadraTable tbody');
                    tbody.innerHTML = '<tr><td colspan="3">Errore nel caricamento dei dati.</td></tr>';
                });
        }

        function applySearch(searchTerm) {
            currentSearch = searchTerm.toLowerCase().trim();

            if (currentSearch === '') {
                filteredSquadre = [...allSquadre];
                clearButton.style.display = 'none';
            } else {
                filteredSquadre = allSquadre.filter(squadra => {
                    // Cerca nel nome della squadra
                    const matchesSquadra = squadra.nome_squadra.toLowerCase().includes(currentSearch);

                    // Cerca nel nome del presidente
                    const matchesPresidente = squadra.dirigenza && squadra.dirigenza.presidente &&
                        squadra.dirigenza.presidente.toLowerCase().includes(currentSearch);

                    return matchesSquadra || matchesPresidente;
                });
                clearButton.style.display = 'flex';
            }

            // Aggiorna le statistiche
            updateSearchStats();

            // Reset alla prima pagina quando si cerca
            currentPage = 1;

            // Aggiorna visualizzazione
            renderSquadre();
            renderPagination();
        }

        function updateSearchStats() {
            if (currentSearch === '') {
                searchStats.textContent = `Visualizzando ${allSquadre.length} squadre totali`;
            } else {
                searchStats.textContent = `Trovate ${filteredSquadre.length} squadre per "${currentSearch}"`;
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

        function renderSquadre() {
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageSquadre = filteredSquadre.slice(startIndex, endIndex);
            const tbody = document.querySelector('#squadraTable tbody');

            if (pageSquadre.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3">
                            <div class="no-results">
                                Nessuna squadra trovata<br>
                            </div>
                        </td>
                    </tr>`;
                return;
            }

            let html = '';
            pageSquadre.forEach(squadra => {
                const highlightedSquadra = highlightSearchTerm(squadra.nome_squadra, currentSearch);
                const highlightedPresidente = squadra.dirigenza && squadra.dirigenza.presidente ?
                    highlightSearchTerm(squadra.dirigenza.presidente, currentSearch) : 'N/D';

                html += `
                    <tr>
                        <td>${highlightedSquadra}</td>
                        <td>${highlightedPresidente}</td>
                        <td><a href="squadra.php?id=${squadra.id}"><button class="tablinks" style="background-color: var(--accento)">Dettagli</button></a></td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredSquadre.length / itemsPerPage);
            const paginationContainer = document.getElementById('pagination');

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
                renderSquadre();
                renderPagination();
                document.querySelector('.search-container').scrollIntoView({ behavior: 'smooth' });
            }
        };

        // Aggiorna la paginazione quando si ridimensiona la finestra
        window.addEventListener('resize', () => {
            if (filteredSquadre.length > 0) {
                renderPagination();
            }
        });
    });
</script>
</body>
</html>