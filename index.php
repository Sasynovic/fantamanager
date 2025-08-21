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
    <script src="js/renderFooter.js" defer></script>
</head>

<style>
    .division-name{
        color: var(--blu-scuro);
        padding: 10px 30px;
        background: white;
        border-radius: 30px;
        border: 4px solid var(--accento);
        position: absolute;
        bottom: -80px; /* Spostato leggermente più in basso per separarlo dalla sfera */
        left: 50%;
        transform: translateX(-50%); /* Centra orizzontalmente */
        white-space: nowrap; /* Evita che il testo vada a capo */
    }

    .division-item > .division-link > .division-name{
        font-size: 1.2rem;
        bottom: -70px;
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
            <li class="menu-item">
                <a href="contatti.php">Contatti</a>
            </li>
        </ul>
    </aside>

    <div class="main-content">
        <header class="main-header">
            <div class="main-text-header">
                <h1></h1>
                <h1>Dashboard</h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
        </header>

        <div class="main-body">
            <div class="main-body-content" style="height: 100%;">
                <div class="swiper-container main-swiper">
                    <div class="swiper-wrapper" id="mainDivisionList">
                        <!-- Gli elementi divisione verranno inseriti qui tramite JavaScript -->
                    </div>
                    <!-- Aggiunti i pulsanti di navigazione -->
                    <div class="swiper-button-prev main-nav-prev">
                        <img src="public/chevron/chevronL.svg" alt="Indietro">
                    </div>
                    <div class="swiper-button-next main-nav-next">
                        <img src="public/chevron/chevronR.svg" alt="Avanti">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Riferimento al contenitore Swiper per la sezione principale
        const mainDivisionList = document.getElementById('mainDivisionList');
        // Riferimento al contenitore Swiper per il footer
        const footerList = document.getElementById('footerList');

        // Funzione per renderizzare le divisioni nella sezione principale
        function renderMainDivisions(divisions) {
            mainDivisionList.innerHTML = '';

            // Duplica gli elementi per l'effetto infinito
            const allDivisions = [...divisions, ...divisions, ...divisions];

            // Aggiungi gli elementi al DOM
            allDivisions.forEach((division, index) => {
                const slide = document.createElement('div');
                slide.className = 'swiper-slide';

                const divItem = document.createElement('div');
                if (division.isMain) {
                    divItem.className = 'division-main-item';
                } else {
                    divItem.className = 'division-item';
                }

                divItem.dataset.divisionId = division.id;

                const flagImg = document.createElement('img');
                flagImg.src = "public/flag/" + division.bandiera || 'path/to/default-flag.png';
                flagImg.alt = `Bandiera ${division.nome}`;
                flagImg.className = 'division-flag';
                divItem.appendChild(flagImg);

                const url = document.createElement('a');
                url.className = 'division-link';
                url.href = "divisione.php?id=" + division.id;
                divItem.appendChild(url);

                const name = document.createElement('h2');{
                    name.className = 'division-name';
                    name.textContent = division.nome_divisione;
                }

                slide.appendChild(divItem);
                mainDivisionList.appendChild(slide);
                url.appendChild(name);
            });

            initMainSwiper(divisions.length);
        }

        // Funzione per inizializzare Swiper nella sezione principale
        function initMainSwiper(totalSlides) {
            const mainSwiper = new Swiper('.main-swiper', {
                slidesPerView: 3,
                spaceBetween: 50,
                centeredSlides: true,
                loop: true,
                loopAdditionalSlides: totalSlides,
                initialSlide: 1,
                speed: 500,
                navigation: {
                    nextEl: '.main-nav-next',
                    prevEl: '.main-nav-prev',
                },
                breakpoints: {
                    320: {
                        slidesPerView: 1,
                    },
                    768: {
                        slidesPerView: 2,
                        centeredSlides: false,
                    },
                    1024: {
                        slidesPerView: 3,
                        centeredSlides: true,
                    }
                },
                on: {
                    slideChange: function() {
                        const slides = document.querySelectorAll('.main-swiper .swiper-slide');
                        slides.forEach((slide, index) => {
                            const divElement = slide.querySelector('div');
                            if (divElement) {
                                if (divElement.classList.contains('division-main-item')) {
                                    divElement.classList.remove('division-main-item');
                                    divElement.classList.add('division-item');
                                }
                            }
                        });

                        const activeIndex = this.activeIndex;
                        const activeSlide = slides[activeIndex];
                        if (activeSlide) {
                            const divElement = activeSlide.querySelector('div');
                            if (divElement) {
                                divElement.classList.remove('division-item');
                                divElement.classList.add('division-main-item');
                            }
                        }
                    }
                }
            });
        }
        // Carica le divisioni dal server
        fetch('endpoint/divisione/read.php')
            .then(response => response.json())
            .then(result => {
                const data = Array.isArray(result) ? result : result.divisioni;

                if (Array.isArray(data)) {
                    const processedData = data.map((div, idx) => ({
                        ...div,
                        isMain: idx === 1,
                        bandiera: div.bandiera || `path/to/default-flag${div.id}.png`
                    }));

                    renderMainDivisions(processedData);
                    renderDivisions(processedData);
                } else {
                    console.error('La risposta non è un array valido:', result);
                }
            })
            .catch(error => {
                console.error('Errore durante il fetch delle divisioni:', error);
            });
    });
</script>