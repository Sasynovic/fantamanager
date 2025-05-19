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
    <script src="js/renderFooter.js" defer></script>
    <script src="js/showmenu.js" defer></script>
    <style>
        /* STILE SPECIFICO PER L'ALBO D'ORO */
        .filter-section{
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-body-content{
            margin-top: 20px;
        }

        .albo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 0 20px;
        }

        .albo-header h1 {
            font-size: 2.2rem;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        select {
            background-color: var(--blu-scuro, #1a2c56);
            color: white;
            border: 2px solid var(--accento, #3c74f5);
            border-radius: 30px;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
        }

        select:hover {
            background-color: var(--blu, #294582);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        select:focus {
            outline: none;
            border-color: white;
        }

        .table-container {
            background-color: var(--blu-scuro, #1a2c56);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            margin: 0 auto;
            max-width: 95%;
        }





        /* Responsive */
        @media (max-width: 1024px) {
            .albo-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            select {
                width: 100%;
            }

            .pagination {
                flex-wrap: wrap;
            }

            .page-btn {
                padding: 6px 10px;
                font-size: 0.9rem;
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
                <h1>🏆 Albo d'Oro</h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
        </header>


        <div class="main-body">
            <div class="main-body-content" id="main-body-content">
                <div class="filter-section">
                    <select id="filter-competizione">
                        <option value="Tutte">Tutte le competizioni</option>
                    </select>
                </div>
                <table id="alboTable">
                    <thead>
                    <tr>
                        <th>Competizione</th>
                        <th>Anno</th>
                        <th>Squadra</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td colspan="3">Caricamento dati...</td></tr>
                    </tbody>
                </table>
                <div id="pagination" class="pagination">
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
</body>



<script>
    let alboData = [];
    let formFilterCompetizione = document.getElementById('filter-competizione');
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalPages = 1;
    let currentFilter = 'Tutte';

    function loadCompetizioni(select) {
        let urlDiv = `${window.location.protocol}//${window.location.host}/endpoint/competizione/read.php?limit=1000`;

        fetch(urlDiv)
            .then(response => response.json())
            .then(data => {
                const competizione = data.competizione;

                if (Array.isArray(competizione)) {
                    competizione.sort((a, b) => a.nome_competizione.localeCompare(b.nome_competizione));

                    competizione.forEach(competizione => {
                        const option = document.createElement('option');
                        option.value = competizione.id;
                        option.textContent = competizione.nome_competizione;
                        select.appendChild(option);
                    });
                } else {
                    console.error("Il campo 'competizione' non è un array:", competizione);
                }
            })
            .catch(error => console.error('Errore nel caricamento delle competizioni:', error));
    }
    function mostraAlbo(filtro = 'Tutte') {
        const tbody = document.querySelector('#alboTable tbody');
        tbody.innerHTML = '';

        const filtroID = parseInt(filtro);
        const datiFiltrati = filtro === 'Tutte'
            ? alboData
            : alboData.filter(r => r.id_competizione === filtroID || r.nome_competizione === filtro);

        if (datiFiltrati.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="3">Nessun vincitore registrato.</td>';
            tbody.appendChild(tr);

            // Aggiungi 9 righe vuote per un totale di 10
            for (let i = 0; i < itemsPerPage - 1; i++) {
                const trVuoto = document.createElement('tr');
                trVuoto.innerHTML = `
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        `;
                tbody.appendChild(trVuoto);
            }

            return;
        }


        // Aggiunge le righe dei dati reali
        datiFiltrati.forEach(r => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>${r.nome_competizione}</td>
            <td>${r.anno}</td>
            <td>${r.nome_squadra}</td>
        `;
            tbody.appendChild(tr);
        });

        // Aggiunge righe vuote fino a raggiungere 10
        const righeVuoteDaAggiungere = itemsPerPage - datiFiltrati.length;
        for (let i = 0; i < righeVuoteDaAggiungere; i++) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        `;
            tbody.appendChild(tr);
        }
    }


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
                loadAlbo(currentFilter, currentPage - 1);
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
                loadAlbo(currentFilter, currentPage + 1);
            }
        });
        paginationContainer.appendChild(nextButton);

        function addPageButton(pageNum, currentPage) {
            const pageButton = document.createElement('button');
            pageButton.className = 'page-btn' + (pageNum === currentPage ? ' active' : '');
            pageButton.textContent = pageNum;
            pageButton.addEventListener('click', () => {
                loadAlbo(currentFilter, pageNum);
            });
            paginationContainer.appendChild(pageButton);
        }
    }

    function loadAlbo(competizioneId = 'Tutte', page = 1) {
        currentFilter = competizioneId;
        currentPage = page;

        let urlAlbo = `${window.location.protocol}//${window.location.host}/endpoint/albo/read.php?limit=${itemsPerPage}&page=${page}`;

        if (competizioneId !== 'Tutte') {
            urlAlbo += `&id_competizione=${competizioneId}`;
        }
        console.log(urlAlbo);

        // Mostra indicatore di caricamento
        document.querySelector('#alboTable tbody').innerHTML =
            '<tr><td colspan="3">Caricamento dati...</td></tr>';

        fetch(urlAlbo)
            .then(response => response.json())
            .then(data => {
                if (data.albo && Array.isArray(data.albo)) {
                    alboData = data.albo; // Assegna i dati qui
                    mostraAlbo(competizioneId); // Poi chiama mostraAlbo

                    // Aggiorna le informazioni di paginazione
                    if (data.pagination) {
                        totalPages = data.pagination.total_pages;
                        itemsPerPage = data.pagination.items_per_page;
                        currentPage = data.pagination.current_page;
                        renderPagination(data.pagination);
                    }
                } else {
                    document.querySelector('#alboTable tbody').innerHTML =
                        '<tr><td colspan="3">Nessun vincitore trovato.</td></tr>';
                    document.getElementById('pagination').innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Errore nel caricamento albo:', error);
                document.querySelector('#alboTable tbody').innerHTML =
                    '<tr><td colspan="3">Errore nel caricamento dei dati.</td></tr>';
                document.getElementById('pagination').innerHTML = '';
            });
    }

    document.getElementById('filter-competizione').addEventListener('change', function () {
        loadAlbo(this.value);
    });

    // Inizializzazione
    loadCompetizioni(formFilterCompetizione);
    loadAlbo();
</script>
</body>
</html>