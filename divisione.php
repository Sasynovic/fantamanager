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
                <h1>
                    <?php
                    // Recupera l'ID divisione dall'URL
                    $urlParams = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
                    parse_str($urlParams, $params);
                    $idDivisione = isset($params['id']) ? $params['id'] : 0;

                    $baseUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/';  // Prende il dominio automaticamente

                    $data = file_get_contents($baseUrl.'endpoint/divisione/read.php?id='.$idDivisione, false, stream_context_create([
                        'http' => [
                            'method' => 'GET',
                            'header' => 'Content-Type: application/json'
                        ]
                    ]));

                    if ($data) {
                        $json = json_decode($data);
                        // Accedi al primo elemento dell'array divisioni
                        echo $json->divisioni[0]->nome_divisione ?? 'Divisione non trovata';
                    } else {
                        echo 'Divisione non trovata';
                    }
                    ?>
                </h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
        </header>

        <div class="main-body">
            <div class="main-body-content" id="main-body-content">
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Recupera l'ID divisione dall'URL
                        const urlParams = new URLSearchParams(window.location.search);
                        const idDivisione = urlParams.get('id') || 0;

                        // Elemento dove visualizzare i contenuti
                        const contentContainer = document.getElementById('main-body-content');

                        // Funzione per caricare i dati
                        async function loadCompetizioni() {
                            try {
                                // Esegui la chiamata all'endpoint
                                const response = await fetch(`endpoint/competizione/read.php?id_divisione=${idDivisione}`);

                                if (!response.ok) {
                                    throw new Error(`Errore nella richiesta: ${response.status}`);
                                }

                                const data = await response.json();

                                // Verifica se ci sono dati e se contiene l'array di competizioni
                                if (data && Array.isArray(data.competizione) && data.competizione.length > 0) {
                                    displayCompetizioni(data.competizione);
                                } else {
                                    contentContainer.innerHTML = `
                            <div class="error-message">
                                Nessuna competizione trovata per questa divisione.
                            </div>
                        `;
                                }
                            } catch (error) {
                                console.error("Errore durante il caricamento dei dati:", error);
                                contentContainer.innerHTML = `
                        <div class="error-message">
                            Si è verificato un errore durante il caricamento delle competizioni: ${error.message}
                        </div>
                    `;
                            }
                        }

                        // Funzione per visualizzare i dati nella tabella
                        function displayCompetizioni(competizioni) {

                            // Crea la tabella HTML
                            const tableHTML = `
                                        <table class="competizioni-table">
                        <thead class="competizioni-table__header">
                            <tr class="competizioni-table__row">
                                <th class="competizioni-table__header-cell">Nome</th>
                                <th class="competizioni-table__header-cell">Stagione</th>
                                <th class="competizioni-table__header-cell">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="competizioni-table__body">
                            ${competizioni.map(comp => `
                                <tr class="competizioni-table__row">
                                    <td class="competizioni-table__cell competizioni-table__cell">${comp.nome_competizione}</td>
                                    <td class="competizioni-table__cell competizioni-table__cell">${comp.anno}</td>
                                    <td class="competizioni-table__cell competizioni-table__cell"><a class="comp-button" href='competizione.php?id=${comp.id}'>Dettagli</a></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;

                            contentContainer.innerHTML = tableHTML;
                        }

                        // Carica i dati
                        loadCompetizioni();
                    });
                </script>

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