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


        /* Layout principale */
        .main-body-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            color: var(--blu-scurissimo);
        }

        /* Container dei contatti */
        .contact-container {
            background: var(--blu);
            color: #f1f1f1;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
            max-width: 600px;
            width: 100%;
            text-align: center;
            margin: 2rem 0;
        }

        .contact-container h2 {
            color: #f1f1f1;
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .contact-container p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            line-height: 1.5;
        }

        /* Sezione informazioni di contatto */
        .contact-info {
            margin-top: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
            padding: 0.5rem 1rem;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 8px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            color: #333;
            width: 100%;
            padding: 0.5rem 0;
        }

        .contact-item a {
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .contact-item a:hover {
            text-decoration: underline;
            color: var(--blu-scurissimo);
        }

        /* Social media */
        .social-media {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }

        .social-media a {
            text-decoration: none;
            color: var(--blu-scurissimo);
            font-weight: 500;
            padding: 0.5rem 1rem;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .social-media a:hover {
            background-color: var(--blu-scurissimo);
            color: white;
            transform: translateY(-3px);
        }

        .social-icon {
            width: 32px;
            height: 32px;
            transition: transform 0.2s ease;
        }

        .social-icon:hover {
            transform: scale(1.1);
        }

        /* Credit footer */
        .developer-credits {
            font-size: 0.85rem;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            text-align: center;
            color: #555;
        }

        .developer-credits a {
            text-decoration: none;
        }

        .developer-credits a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .contact-container {
                padding: 1.5rem;
                margin: 1rem;
                width: calc(100% - 2rem);
            }

            .contact-info {
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
</head>
<body>
<div class="main-container">
    <aside class="main-menu">
        <div class="menu-header">
            <img src="public/background/logo.png" alt="Logo" class="logo" width="80" height="80">
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
                    <img src="public/chevron/chevronL.svg" alt="Indietro" height="40" width="40">
                </button>
                <h1>Contatti</h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
        </header>


        <div class="main-body">
            <div class="main-body-content" id="main-body-content">
                <div class="contact-container">
                    <h2>Contattaci</h2>
                    <p>Hai domande o bisogno di assistenza? Siamo a tua disposizione.</p>

                    <div class="contact-info">
                        <div class="contact-item">
                            <span><strong>Email:</strong> <a href="mailto:info@fantamanagerpro.eu">info@fantamanagerpro.eu</a></span>
                        </div>
                        <div class="contact-item">
                            <span><strong>Telefono:</strong> <a href="tel:+393371447208">+39 337 144 7208</a></span>
                        </div>
                    </div>

                    <div class="social-media">
                        <a href="https://www.facebook.com/FManagerPro" target="_blank">Facebook</a>
                        <a href="https://www.instagram.com/fantamanagerpro/" target="_blank">Instagram</a>
                    </div>

                    <div class="developer-credits">
                        <p>Sviluppato con ♥ da <a href="https://www.linkedin.com/in/salvatore-barretta-ab318b264/">Salvatore Barretta</a></p>
                        <p>© 2025 FantaManager Pro - Tutti i diritti riservati</p>
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