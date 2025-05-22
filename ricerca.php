<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>FMPro</title>
    <link rel="icon" href="public/background/logo.png" type="image/png">

    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="js/renderFooter.js" defer></script>
    <script src="js/showmenu.js" defer></script>

    <style>
        .filter{
            background: linear-gradient(135deg, var(--accento), var(--blu-scurissimo));
            margin: 20px auto;
            width: 90%;
            border: 1px solid white;
            border-radius: 30px;
            text-align: center;
            padding: 20px;
        }
        .filter-item{
            margin: 10px 0;
        }
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
            <li class="menu-item"><a href="index.php">Dashboard</a></li>
            <li class="menu-item"><a href="albo.php">Albo d'oro</a></li>
            <li class="menu-item"><a href="vendita.php">Squadre in vendita</a></li>
            <li class="menu-item"><a href="tool.php">Tool scambi</a></li>
            <li class="menu-item"><a href="regolamento.php">Regolamento</a></li>
            <li class="menu-item"><a href="ricerca.php">Ricerca</a></li>
            <li class="menu-item"><a href="contatti.php">Contatti</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <header class="main-header">
            <div class="main-text-header">
                <button class="back-button" onclick="window.history.back();">
                    <img src="public/chevron/chevronL.svg" alt="Indietro" height="40" width="40"/>
                </button>
                <h1>Ricerca</h1>
                <h1 id="hamburger-menu"></h1>
            </div>
        </header>

        <div class="main-body">
            <div class="main-body-content" id="main-body-content">
                <div class="filter">
                    <h2>Filtri di ricerca</h2>
                    <div class="filter-item">
                        <label for="team"></label>
                        <input type="text" id="team" placeholder="Squadra" />
                    </div>
                    <div class="filter-item">
                        <label for="player"></label>
                        <input type="text" id="player" placeholder="Presidente" />
                    </div>
                    <button id="search-button">Cerca</button>
                </div>

                <table id="squadraTable">
                    <thead>
                    <tr>
                        <th>Squadra</th>
                        <th>Presidente</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td colspan="3">Caricamento dati...</td></tr>
                    </tbody>
                </table>
                <div id="pagination" class="pagination"></div>
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
    let currentPage = 1;
    const itemsPerPage = 10;
    let totalPages = 1;

    function fetchSquadraData(team = '', player = '', page = 1) {
        const query = new URLSearchParams({
            nome_squadra: team,
            nome_presidente: player,
            page: page,          // Invia la pagina corrente all'API
            items_per_page: itemsPerPage  // Invia il numero di elementi per pagina
        });
        const url = `${window.location.origin}/endpoint/squadra/read.php?${query}`;

        const tbody = document.querySelector('#squadraTable tbody');
        tbody.innerHTML = '<tr><td colspan="3">Caricamento dati...</td></tr>';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                // Usa i dati della paginazione forniti dall'API
                currentPage = data.pagination.current_page;
                totalPages = data.pagination.total_pages;

                const squadre = data.squadra || [];
                renderTable(squadre);
                renderPagination(data.pagination);
            })
            .catch(err => {
                console.error('Errore:', err);
                tbody.innerHTML = '<tr><td colspan="3">Errore nel caricamento dei dati.</td></tr>';
            });
    }

    function renderTable(squadre) {
        const tbody = document.querySelector('#squadraTable tbody');
        tbody.innerHTML = '';

        if (squadre.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="3">Nessun risultato trovato.</td>';
            tbody.appendChild(tr);
            return;
        }

        squadre.forEach(squadra => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>${squadra.nome_squadra}</td>
            <td>${squadra.dirigenza.presidente}</td>
            <td><a href="squadra.php?id=${squadra.id}"><button>Dettagli</button></a></td>
        `;
            tbody.appendChild(tr);
        });
    }

    function renderPagination(paginationData) {
        const container = document.getElementById('pagination');
        container.innerHTML = '';

        if (paginationData.total_pages <= 1) return;

        const createBtn = (label, page, disabled = false) => {
            const btn = document.createElement('button');
            btn.textContent = label;
            btn.disabled = disabled;
            btn.className = 'page-btn' + (page === paginationData.current_page ? ' active' : '');
            btn.onclick = () => {
                // Mantiene i filtri quando cambia pagina
                const team = document.getElementById('team').value.trim();
                const player = document.getElementById('player').value.trim();
                fetchSquadraData(team, player, page);
            };
            return btn;
        };

        // Pulsante pagina precedente
        container.appendChild(createBtn('«', paginationData.current_page - 1, !paginationData.has_previous_page));

        // Calcola le pagine da mostrare (mostra 5 numeri di pagina)
        let startPage = Math.max(1, paginationData.current_page - 2);
        let endPage = Math.min(paginationData.total_pages, startPage + 4);

        // Aggiusta startPage se necessario
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }

        // Prima pagina (se non è inclusa nel range)
        if (startPage > 1) {
            container.appendChild(createBtn(1, 1));
            if (startPage > 2) {
                // Aggiunge puntini di sospensione
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'pagination-ellipsis';
                container.appendChild(ellipsis);
            }
        }

        // Pagine numeriche
        for (let i = startPage; i <= endPage; i++) {
            container.appendChild(createBtn(i, i));
        }

        // Ultima pagina (se non è inclusa nel range)
        if (endPage < paginationData.total_pages) {
            if (endPage < paginationData.total_pages - 1) {
                // Aggiunge puntini di sospensione
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'pagination-ellipsis';
                container.appendChild(ellipsis);
            }
            container.appendChild(createBtn(paginationData.total_pages, paginationData.total_pages));
        }

        // Pulsante pagina successiva
        container.appendChild(createBtn('»', paginationData.current_page + 1, !paginationData.has_next_page));
    }

    document.getElementById('search-button').addEventListener('click', () => {
        const team = document.getElementById('team').value.trim();
        const player = document.getElementById('player').value.trim();
        fetchSquadraData(team, player, 1); // Resetta alla prima pagina quando si cerca
    });

    document.addEventListener('DOMContentLoaded', () => {
        fetchSquadraData();
    });
</script>
</body>
</html>
