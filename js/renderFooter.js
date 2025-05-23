 function renderFooter() {
    document.addEventListener('DOMContentLoaded', function() {
        // Inizializza le divisioni di esempio per la sezione principale
        const mainDivisions = [
            { id: 1, nome: "Divisione 1", isMain: false, bandiera: "path/to/default-flag1.png" },
            { id: 2, nome: "Divisione 2", isMain: true, bandiera: "path/to/default-flag2.png" },
            { id: 3, nome: "Divisione 3", isMain: false, bandiera: "path/to/default-flag3.png" },
            { id: 4, nome: "Divisione 4", isMain: false, bandiera: "path/to/default-flag4.png" },
            { id: 5, nome: "Divisione 5", isMain: false, bandiera: "path/to/default-flag5.png" },
            { id: 6, nome: "Divisione 6", isMain: false, bandiera: "path/to/default-flag6.png" },
            { id: 7, nome: "Divisione 7", isMain: false, bandiera: "path/to/default-flag7.png" }
        ];

        // Riferimento al contenitore Swiper per la sezione principale
        const mainDivisionList = document.getElementById('mainDivisionList');
        // Riferimento al contenitore Swiper per il footer
        const footerList = document.getElementById('footerList');

        // Funzione per inizializzare Swiper nella sezione principale
        function initMainSwiper(totalSlides) {
            const mainSwiper = new Swiper('.main-swiper', {
                slidesPerView: 3,  // Mostra 3 slide alla volta
                spaceBetween: 50,
                centeredSlides: true,
                loop: true,         // Abilita il loop infinito
                loopAdditionalSlides: totalSlides,
                initialSlide: 1,    // Inizia con la slide centrale
                speed: 500,         // Velocità dell'animazione in ms
                navigation: {
                    nextEl: '.main-nav-next',
                    prevEl: '.main-nav-prev',
                },
                breakpoints: {
                    // Responsive settings
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
                        // Aggiorna la classe per lo stile dell'elemento centrale
                        const slides = document.querySelectorAll('.main-swiper .swiper-slide');
                        slides.forEach((slide, index) => {
                            const divElement = slide.querySelector('div');
                            if (divElement) {
                                // Rimuovi tutte le classi
                                if (divElement.classList.contains('division-main-item')) {
                                    divElement.classList.remove('division-main-item');
                                    divElement.classList.add('division-item');
                                }
                            }
                        });

                        // Aggiungi la classe all'elemento attivo
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

        // Funzione per renderizzare le divisioni nel footer
        function renderDivisions(divisions) {
            footerList.innerHTML = '';

            // Duplica gli elementi per l'effetto infinito
            const allDivisions = [...divisions, ...divisions, ...divisions];

            // Aggiungi gli elementi al DOM
            allDivisions.forEach((division, index) => {
                const slide = document.createElement('div');
                slide.className = 'swiper-slide';

                const li = document.createElement('div');
                li.className = 'division-ball';
                li.id = division.id;
                li.title = division.nome || 'Divisione ' + division.id;
                li.dataset.index = index % divisions.length;

                // Aggiungi l'ID come attributo data
                li.dataset.divisionId = division.id;

                const url = document.createElement('a');
                url.className = 'division-link';
                url.href = "divisione.php?id=" + division.id;
                li.appendChild(url);

                // Crea e aggiungi l'immagine della bandiera come sfondo o come elemento figlio
                // Opzione 1: Come sfondo CSS
                if (division.bandiera) {
                    li.style.backgroundImage = `url(public/flag/${division.bandiera})`;
                    li.style.backgroundSize = 'cover';
                    li.style.backgroundPosition = 'center';
                }

                // Aggiungi click event per mostrare la divisione selezionata
                li.addEventListener('click', function() {
                    // Qui puoi aggiungere il codice per mostrare la divisione selezionata
                });

                slide.appendChild(li);
                footerList.appendChild(slide);
            });

            // Inizializza Swiper dopo aver aggiunto gli elementi
            initFooterSwiper(divisions.length);
        }

        // Funzione per inizializzare Swiper nel footer
        function initFooterSwiper(totalSlides) {
            const swiper = new Swiper('.footer-swiper', {
                slidesPerView: 7,  // Mostra 7 slide alla volta
                spaceBetween: 10,
                centeredSlides: false,
                loop: true,         // Abilita il loop infinito
                loopAdditionalSlides: totalSlides,
                speed: 500,         // Velocità dell'animazione in ms
                navigation: {
                    nextEl: '.footer-nav-next',
                    prevEl: '.footer-nav-prev',
                },
                breakpoints: {
                    // Responsive settings
                    320: {
                        slidesPerView: 3,
                    },
                    480: {
                        slidesPerView: 4,
                    },
                    768: {
                        slidesPerView: 5,
                    },
                    1024: {
                        slidesPerView: 7,
                    }
                }
            });
        }

        // Carica le divisioni (reali o di esempio)
        fetch('endpoint/divisione/read.php')
            .then(response => response.json())
            .then(result => {
                const data = Array.isArray(result) ? result : result.divisioni;

                if (Array.isArray(data)) {
                    // Verifica che ci sia il campo bandiera, altrimenti aggiungi un valore predefinito
                    const processedData = data.map((div, idx) => ({
                        ...div,
                        isMain: idx === 1,  // La seconda divisione è principale
                        bandiera: div.bandiera || `path/to/default-flag${div.id}.png` // Aggiungi un valore predefinito se manca
                    }));
                    renderDivisions(processedData);
                } else {
                    console.error('La risposta non è un array valido:', result);
                    renderDivisions(mainDivisions);
                }
            })
            .catch(error => {
                console.error('Errore durante il fetch delle divisioni:', error);
                renderDivisions(mainDivisions);
            });
    });

}
 renderFooter();
