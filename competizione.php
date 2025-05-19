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
</head>
<style>

    .main-body-content{
        height: 100%;
        overflow-y: scroll;
    }

    .news-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .tablinks {
        padding: 10px 20px;
        background-color: var(--blu);
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        color: white;
        border: none;
        outline: none;
        font-size: 1rem;
    }

    .tablinks.active {
        background-color: var(--oro);
        color: var(--blu-scurissimo);
    }

    .tablinks:hover:not(.active) {
        background-color: var(--accento);
    }

    .tablinks:hover:not(.active) {
        background-color: #2a3a5a;
    }


    .news-content {
        min-height: 300px;
    }

    .news-item {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        border: 2px solid var(--accento);
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .news-item h3 {
        margin-top: 0;
        color: #2e6be6;
    }


    .news-item .news-meta {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }

    .news-pagination {
        display: flex;
        justify-content: center;
        margin-top: 30px;
        gap: 5px;
    }

    .tab{
        align-items: center;
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }
    .container{
        width: 100%;
        height: 100%;

    }
    .news-contenuto *{
        color: black;
    }
</style>

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
                <h1>
                    <?php
                    // Recupera l'ID competizione dall'URL
                    $urlParams = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
                    parse_str($urlParams, $params);
                    $id_competizione = $params['id'] ?? 0;

                    $baseUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/';  // Prende il dominio automaticamente

                    $data = file_get_contents($baseUrl.'endpoint/competizione/read.php?id_competizione='.$id_competizione, false, stream_context_create([
                        'http' => [
                            'method' => 'GET',
                            'header' => 'Content-Type: application/json'
                        ]
                    ]));

                    if ($data) {
                        $json = json_decode($data);}
                    if (isset($json->competizione[0]->nome_competizione)) {
                        echo htmlspecialchars($json->competizione[0]->nome_competizione);
                    } else {
                        echo "Competizione non trovata";
                    }
                    ?>
                </h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
        </header>

        <div class="main-body">
            <h3  style="text-align: center; padding: 10px; background-color: var(--accento)" >   Clicca sul nome della squadra per maggiori dettagli</h3>
            <div class="main-body-content" id="main-body-content">
                <div class="container">

                    <div class="tab">
                        <button class="tablinks" onclick="openTab(event, 'classifica')" id="defaultOpen">Classifica</button>
                        <button class="tablinks" onclick="openTab(event, 'news')">News</button>
                    </div>

                    <div id="classifica" name="tabcontent" >
                        <script>
                            fetch('/endpoint/partecipazione/read.php?id_competizione=' + <?php echo $id_competizione; ?>)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Errore nella risposta del server');
                                    }
                                    return response.json();
                                })
                                .then(data => {

                                    // Verifica che data.squadre esista e sia un array
                                    if (!data.squadre || !Array.isArray(data.squadre)) {
                                        console.error('Dati non validi:', data);
                                        throw new Error('Formato dati non valido');
                                    }

                                    // Ordina le squadre per posizione in classifica
                                    const squadreOrdinate = [...data.squadre].sort((a, b) => {
                                        // Prima per punti totali (decrescente)
                                        if (b.punti !== a.punti) {
                                            return b.punti - a.punti;
                                        }
                                        if (b.punti_totali !== a.punti_totali) {
                                            return b.punti_totali - a.punti_totali;
                                        }

                                        // Poi per differenza reti (decrescente)
                                        if (b.differenza_reti !== a.differenza_reti) {
                                            return b.differenza_reti - a.differenza_reti;
                                        }

                                        // Infine per gol fatti (decrescente)
                                        return b.gol_fatti - a.gol_fatti;
                                    });

                                    // Genera la tabella HTML
                                    const classificaContainer = document.getElementById('classifica');
                                    let classificaHTML = `
                                            <table class="classifica-table">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Squadra</th>
                                                        <th>Pn</th>
                                                        <th>G</th>
                                                        <th>V</th>
                                                        <th>N</th>
                                                        <th>P</th>
                                                        <th>Gf</th>
                                                        <th>Gs</th>

                                                        <th>Pt</th>
                                                        <th>Pt tot</th>
                                                    </tr>
                                                </thead>
                                                <tbody>`;

                                    squadreOrdinate.forEach((squadra, index) => {
                                        classificaHTML += `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>
                                                      <a href="squadra.php?id=${squadra.id}">
                                                        ${squadra.nome_squadra || 'N/D'}
                                                      </a>
                                                    </td>
                                                    <td>${squadra.penalizzazione || 0}</td>
                                                    <td>${squadra.giocate || 0}</td>
                                                    <td>${squadra.vittorie || 0}</td>
                                                    <td>${squadra.pareggi || 0}</td>
                                                    <td>${squadra.sconfitte || 0}</td>
                                                    <td>${squadra.gol_fatti || 0}</td>
                                                    <td>${squadra.gol_subiti || 0}</td>

                                                    <td>${squadra.punti || 0}</td>
                                                    <td>${squadra.punti_totali || 0}</td>
                                                </tr>`;
                                    });

                                    classificaHTML += `</tbody></table>`;
                                    classificaContainer.innerHTML = classificaHTML;
                                })
                                .catch(error => {
                                    console.error('Errore:', error);
                                    document.getElementById('classifica').innerHTML = `
                <div class="error-message">
                    Errore nel caricamento della classifica: ${error.message}
                </div>`;
                                });
                        </script>
                    </div>

                    <div id="news" name="tabcontent" class="news-container">
                        <div class="news-content" id="news-content">
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Elementi DOM
                                    const newsContent = document.getElementById('news-content');
                                    const paginationContainer = document.getElementById('news-pagination');
                                    const tabButtons = document.querySelectorAll('.tablinks');

                                    // Stato dell'applicazione
                                    let currentTab = 'all';
                                    let currentPage = 1;
                                    const itemsPerPage = 5; // Puoi cambiare questo valore

                                    // Inizializza
                                    loadNews(currentTab, currentPage);

                                    // Gestione tab
                                    tabButtons.forEach(button => {
                                        button.addEventListener('click', function() {
                                            // Aggiorna tab attivo
                                            tabButtons.forEach(btn => btn.classList.remove('active'));
                                            this.classList.add('active');

                                            // Carica news per il tab selezionato
                                            // Verifica se il pulsante ha un attributo data-tab
                                            currentTab = this.dataset.tab || 'all';
                                            currentPage = 1; // Resetta alla prima pagina quando cambi tab
                                            loadNews(currentTab, currentPage);
                                        });
                                    });

                                    // Funzione per caricare le news
                                    function loadNews(tab, page) {
                                        // Usa direttamente l'ID competizione dall'URL
                                        let url = '/endpoint/news/read.php?visibile=1&id_competizione=' + <?php echo $id_competizione; ?> + '&page=' + page + '&limit=' + itemsPerPage;

                                        // Rimuoviamo questa parte che causa l'errore
                                        // Non sovrascriviamo l'ID competizione che è già nell'URL

                                        fetch(url)
                                            .then(response => response.json())
                                            .then(data => {
                                                // Verifica che i dati siano validi
                                                if (!data.news || !Array.isArray(data.news)) {
                                                    throw new Error('Formato dati non valido');
                                                }

                                                // Mostra le news
                                                renderNews(data.news);

                                                // Mostra la paginazione
                                                renderPagination(data.pagination);
                                            })
                                            .catch(error => {
                                                console.error('Errore nel caricamento delle news:', error);
                                                newsContent.innerHTML = `<div class="error-message">Nessuna news trovata per la competizione scelta</div>`;
                                                paginationContainer.innerHTML = '';
                                            });
                                    }

                                    // Funzione per visualizzare le news
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

                                    // Funzione per visualizzare la paginazione
                                    function renderPagination(pagination) {
                                        if (pagination.total_pages <= 1) {
                                            paginationContainer.innerHTML = '';
                                            return;
                                        }

                                        let html = '';

                                        // Pulsante "Precedente"
                                        html += `
                                                        <button class="page-btn ${pagination.has_previous_page ? '' : 'disabled'}"
                                                            onclick="changePage(${currentPage - 1})"
                                                            ${!pagination.has_previous_page ? 'disabled' : ''}>
                                                            &laquo;
                                                        </button>
                                                    `;

                                        // Numeri di pagina
                                        for (let i = 1; i <= pagination.total_pages; i++) {
                                            html += `
                                                            <button class="page-btn ${i === currentPage ? 'active' : ''}"
                                                                onclick="changePage(${i})">
                                                                ${i}
                                                            </button>
                                                        `;
                                        }

                                        // Pulsante "Successivo"
                                        html += `
                                                        <button class="page-btn ${pagination.has_next_page ? '' : 'disabled'}"
                                                            onclick="changePage(${currentPage + 1})"
                                                            ${!pagination.has_next_page ? 'disabled' : ''}>
                                                            &raquo;
                                                        </button>
                                                    `;

                                        paginationContainer.innerHTML = html;
                                    }

                                    // Funzione per cambiare pagina (da chiamare globalmente)
                                    window.changePage = function(page) {
                                        if (page !== currentPage) {
                                            currentPage = page;
                                            loadNews(currentTab, currentPage);
                                            // Scrolla in cima alla sezione news
                                            document.querySelector('.news-container').scrollIntoView({
                                                behavior: 'smooth'
                                            });
                                        }
                                    };

                                    // Funzione helper per formattare la data
                                    function formatDate(dateString) {
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
                        </div>
                        <div class="news-pagination" id="news-pagination">
                            <!-- La paginazione verrà generata dinamicamente -->
                        </div>

                    </div>

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
</body>
</html>

<script>
    function openTab(evt, tabName) {
        let i, tabcontent, tablinks;
        tabcontent = document.getElementsByName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Apri la prima scheda per impostazione predefinita
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("defaultOpen").click();
    });
</script>