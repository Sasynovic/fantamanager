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

        .news-pagination {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            gap: 8px;
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

        let currentPage = 1;
        const itemsPerPage = 5;

        loadNews(currentPage);

        function loadNews(page) {
            const url = `/endpoint/news/read.php?visibile=1&page=${page}&limit=${itemsPerPage}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (!data.news || !Array.isArray(data.news)) {
                        throw new Error('Formato dati non valido');
                    }

                    renderNews(data.news);
                    renderPagination(data.pagination);
                })
                .catch(error => {
                    console.error('Errore nel caricamento delle news:', error);
                    newsContent.innerHTML = `<div class="error-message">Nessuna news trovata</div>`;
                    paginationContainer.innerHTML = '';
                });
        }

        function renderNews(newsItems) {
            if (newsItems.length === 0) {
                newsContent.innerHTML = `<div class="no-news">Nessuna news disponibile</div>`;
                return;
            }

            let html = '';
            newsItems.forEach(news => {
                html += `
                <div class="news-item">
                    <h3>${news.titolo}</h3>
                    <div class="news-meta">
                        ${formatDate(news.data_pubblicazione)} |
                        ${news.nome_competizione || 'Generale'}
                    </div>
                    <div class="news-contenuto">${news.contenuto}</div>
                </div>
            `;
            });

            newsContent.innerHTML = html;
        }

        function renderPagination(pagination) {
            if (!pagination || pagination.total_pages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let html = '';

            html += `
            <button class="page-btn ${pagination.has_previous_page ? '' : 'disabled'}"
                onclick="changePage(${currentPage - 1})"
                ${!pagination.has_previous_page ? 'disabled' : ''}>
                &laquo;
            </button>
        `;

            for (let i = 1; i <= pagination.total_pages; i++) {
                html += `
                <button class="page-btn ${i === currentPage ? 'active' : ''}"
                    onclick="changePage(${i})">
                    ${i}
                </button>
            `;
            }

            html += `
            <button class="page-btn ${pagination.has_next_page ? '' : 'disabled'}"
                onclick="changePage(${currentPage + 1})"
                ${!pagination.has_next_page ? 'disabled' : ''}>
                &raquo;
            </button>
        `;

            paginationContainer.innerHTML = html;
        }

        window.changePage = function(page) {
            if (page !== currentPage) {
                currentPage = page;
                loadNews(currentPage);
                document.querySelector('.news-content').scrollIntoView({ behavior: 'smooth' });
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
    });
</script>
</html>
