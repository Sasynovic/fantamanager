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
        padding: 20px;
    }

    .container{
        width: 100%;
        height: 100%;
    }

   .view-tabs{
       margin-bottom: 0;
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
            <div class="main-body-content" id="main-body-content">
                <div class="container">

                    <div class="view-tabs">
                        <button class="view-tab" onclick="openTab(event, 'classifica')" id="defaultOpen">Classifica</button>
                        <button class="view-tab" onclick="openTab(event, 'news')">News</button>
                    </div>

                    <div id="classifica" name="tabcontent" >
                        <script>
                            fetch('/endpoint/partecipazione/read.php?id_competizione=' + <?php echo $id_competizione; ?>)
                                .then(response => {
                                    if (!response.ok) throw new Error('Errore nella risposta del server');
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data.squadre || !Array.isArray(data.squadre)) {
                                        console.error('Dati non validi:', data);
                                        throw new Error('Formato dati non valido');
                                    }

                                    const squadre = data.squadre;
                                    const gironiPresenti = squadre.some(s => s.girone !== null && s.girone !== '');

                                    let gruppi = {};

                                    if (gironiPresenti) {
                                        // Raggruppa le squadre per girone
                                        squadre.forEach(s => {
                                            const girone = s.girone || 'Senza Girone';
                                            if (!gruppi[girone]) gruppi[girone] = [];
                                            gruppi[girone].push(s);
                                        });
                                    } else {
                                        // Se non ci sono gironi, metti tutte le squadre in un unico gruppo
                                        gruppi[''] = squadre;
                                    }

                                    const container = document.getElementById('classifica');
                                    container.innerHTML = "";

                                    for (const girone in gruppi) {
                                        const squadreOrdinate = gruppi[girone].sort((a, b) => {
                                            if (b.punti !== a.punti) return b.punti - a.punti;
                                            if (b.punti_totali !== a.punti_totali) return b.punti_totali - a.punti_totali;
                                            if (b.differenza_reti !== a.differenza_reti) return b.differenza_reti - a.differenza_reti;
                                            return b.gol_fatti - a.gol_fatti;
                                        });

                                        let classificaHTML = `
                <h2 style="text-align:center; margin-top: 30px;">${girone}</h2>
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
                            <th>Tot</th>
                        </tr>
                    </thead>
                    <tbody>`;

                                        squadreOrdinate.forEach((s, index) => {
                                            classificaHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><b><a href="squadra.php?id=${s.id_squadra}">${s.nome_squadra || 'N/D'}</a></b></td>
                        <td>${s.penalizzazione || 0}</td>
                        <td>${s.giocate || 0}</td>
                        <td>${s.vittorie || 0}</td>
                        <td>${s.pareggi || 0}</td>
                        <td>${s.sconfitte || 0}</td>
                        <td>${s.gol_fatti || 0}</td>
                        <td>${s.gol_subiti || 0}</td>
                        <td><b>${s.punti || 0}</b></td>
                        <td>${s.punti_totali || 0}</td>
                    </tr>`;
                                        });

                                        classificaHTML += `</tbody></table>`;
                                        container.innerHTML += classificaHTML;
                                    }
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
                                    const tabButtons = document.querySelectorAll('.view-tab');

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
        let i, tabcontent, viewtab;
        tabcontent = document.getElementsByName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        viewtab = document.getElementsByClassName("view-tab");
        for (i = 0; i < viewtab.length; i++) {
            viewtab[i].className = viewtab[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Apri la prima scheda per impostazione predefinita
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("defaultOpen").click();
    });
</script>