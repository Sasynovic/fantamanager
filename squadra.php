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

        .trattativa-container {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;

        }

        .operazioni-container {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #ccc;
        }

        .operazione-item {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 3px;
            border-left: 3px solid #4CAF50;
        }

        .no-movements {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .main-body {
            padding: 20px;
            overflow-y: auto;
            background: url('public/background/stadium.png');
            background-position: center;
            background-size: cover;

        }

        /* Stile per i tab di visualizzazione */
        .view-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
            position: relative;
            z-index: 100;
            flex-wrap: wrap;
        }

        .view-tab {
            padding: 10px 20px;
            background-color: var(--blu);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            color: white;
            border: none;
            outline: none;
        }

        .view-tab.active {
            background-color: var(--oro);
            color: var(--blu-scurissimo);
        }

        .view-tab:hover:not(.active) {
            background-color: #2a3a5a;
        }

        /* Stile per la vista a griglia */
        .grid-view {
            display: none;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            width: 100%;
        }

        .grid-view.active {
            display: grid;
        }

        /* Stile per tutte le view */
        .overview, .stadium-view, .albo-view, .sgs-view, .prl-view {
            display: none;
            width: 100%;
            background-color: var(--blu);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .overview.active,
        .stadium-view.active,
        .albo-view.active,
        .sgs-view.active,
        .prl-view.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Stile per le card dei giocatori */
        .grid-player-card-P, .grid-player-card-D, .grid-player-card-C, .grid-player-card-A {
            background-color: var(--blu);
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            padding: 15px;
            color: white;
        }

        .grid-player-card-P {
            border-left: 4px solid var(--oro);
        }
        .grid-player-card-D {
            border-left: 4px solid var(--verde);
        }
        .grid-player-card-C {
            border-left: 4px solid var(--accento);
        }
        .grid-player-card-A {
            border-left: 4px solid var(--rosso);
        }

        .grid-player-card-P:hover,
        .grid-player-card-D:hover,
        .grid-player-card-C:hover,
        .grid-player-card-A:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            background-color: #223155;
        }

        .player-role-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 12px;
            color: white;
        }

        .role-P { background-color: var(--oro); }
        .role-D { background-color: var(--verde); }
        .role-C { background-color: var(--accento); }
        .role-A { background-color: var(--rosso); }

        .player-main-info {
            margin: 10px 0;
        }

        .player-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
            color: white;
        }

        .player-team {
            font-size: 12px;

            margin-bottom: 10px;
        }

        .player-stats {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .stat-value {
            font-weight: bold;
            color: white;
        }

        /* Stile per l'overview */
        .overview-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .overview-card {
            background-color: var(--blu-scurissimo);
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .overview-card h3 {
            margin-top: 0;
            color: var(--oro);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 8px;
        }

        .overview-item {
            display: flex;
            justify-content: space-between;
            flex-direction: column;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .overview-value {
            font-weight: bold;
            color: white;
        }
        .modulo-content{
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
        }

        /* Responsive design */
        @media (max-width: 1024px) {
            .modulo-content{
                display: grid;
                grid-auto-columns: min-content;
            }
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
        <!-- Header -->
        <header class="main-header">
            <div class="main-text-header">
                <button class="back-button" onclick="window.history.back();">
                    <img src="public/chevron/chevronL.svg" alt="Indietro" height="40px" width="40px">
                </button>
                <h1>
                    <?php
                    $squadraNome = 'Squadra';


                    $urlParams = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
                    parse_str($urlParams, $params);
                    $id_squadra = $params['id'] ?? 0;

                    $baseUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/';
                    $data = file_get_contents($baseUrl.'endpoint/squadra/read.php?id_squadra='.$id_squadra, false, stream_context_create([
                        'http' => ['method' => 'GET', 'header' => 'Content-Type: application/json']
                    ]));

                    if ($data) {
                        $json = json_decode($data);
                        echo $json->squadra[0]->nome_squadra ?? 'Squadra non trovata';
                        $squadraNome = $json->squadra[0]->nome_squadra ?? 'Squadra non trovata';
                    } else {
                        echo 'Squadra non trovata';
                    }

                    $settore_giovanile = file_get_contents($baseUrl.'endpoint/settore_giovanile/read.php?id_squadra='.$id_squadra, false, stream_context_create([
                        'http' => ['method' => 'GET', 'header' => 'Content-Type: application/json']
                    ]));

                    if ($settore_giovanile) {
                        $json_settore_giovanile = json_decode($settore_giovanile);
                    }

                    $albo = file_get_contents($baseUrl.'endpoint/albo/read.php?id_squadra='.$id_squadra, false, stream_context_create([
                        'http' => ['method' => 'GET', 'header' => 'Content-Type: application/json']
                    ]));

                    if($albo) {
                        $json_albo = json_decode($albo);
                    }

                    $associazioni = file_get_contents($baseUrl.'endpoint/associazioni/read.php?id_squadra='.$id_squadra . '&prelazione=0', false, stream_context_create([
                        'http' => ['method' => 'GET', 'header' => 'Content-Type: application/json']
                    ]));
                    if($associazioni) {
                        $json_associazioni = json_decode($associazioni);
                    }

                    $prelazioni = file_get_contents($baseUrl.'endpoint/associazioni/read.php?id_squadra=' . $id_squadra . '&prelazione=1', false, stream_context_create([
                        'http' => ['method' => 'GET', 'header' => 'Content-Type: application/json']
                    ]));

                    $calciatori_prelazione = [];

                    if ($prelazioni) {
                        $json_associazioni = json_decode($prelazioni, true);
                        if (!empty($json_associazioni['associazioni'])) {
                            $calciatori_prelazione = $json_associazioni['associazioni'];
                        }
                    }

                    $trattative = file_get_contents($baseUrl.'endpoint/trattative/read.php?ufficializzata=1&id_squadra='.$id_squadra, false, stream_context_create([
                        'http' => ['method' => 'GET', 'header' => 'Content-Type: application/json']
                    ]));

                    if ($trattative) {
                        $json_trattative = json_decode($trattative);
                    }

                    $finanze = file_get_contents($baseUrl.'endpoint/finanze_squadra/read.php?id_squadra='.$id_squadra, false, stream_context_create([
                        'http' => ['method' => 'GET', 'header' => 'Content-Type: application/json']
                    ]));

                    if ($finanze) {
                        $json_finanze = json_decode($finanze);
                    }

                    $partecipazione = file_get_contents($baseUrl.'endpoint/partecipazione/read.php?id_squadra='.$id_squadra, false, stream_context_create([
                        'http' => ['method' => 'GET', 'header' => 'Content-Type: application/json']
                    ]));
                    if ($partecipazione) {
                        $json_partecipazione = json_decode($partecipazione);
                    }
                    ?>
                </h1>
                <h1 id="hamburger-menu">≡</h1>
            </div>
            <div class="header-content">
            </div>
        </header>

        <!-- Corpo principale -->
        <div class="main-body">
            <!-- Tab di navigazione -->
            <div class="view-tabs">
                <div class="view-tab active" data-view="overview">Overview</div>
                <div class="view-tab" data-view="grid">Rosa Giocatori</div>
                <div class="view-tab" data-view="stadium">Stadio</div>
                <div class="view-tab" data-view="albo">Palmarès</div>
                <div class="view-tab" data-view="sgs">Giovanili</div>
                <div class="view-tab" data-view="prl">Prelazioni</div>
            </div>

            <!-- Vista Overview -->
            <div class="overview active" id="overview-view">
                <div class="overview-cards">
                    <div class="overview-card">
                        <h3>Informazioni Generali</h3>
                        <div class="overview-item">
                            <span class="overview-label">Presidente:</span>
                            <span class="overview-value"><?php
                                if ($data) {
                                    echo $json->squadra[0]->dirigenza->presidente ?? 'Presidente non trovato';
                                    if($json->squadra[0]->dirigenza->vicepresidente == ' '){
                                        echo '';
                                    }else{
                                        echo ' </span> ';
                                        echo '<div class="overview-item">';
                                        echo '<span class="overview-label">Vicepresidente:</span>';
                                        echo '<span class="overview-value">' . $json->squadra[0]->dirigenza->vicepresidente;
                                        echo '</span>';
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">In vendita:</span>
                            <span class="overview-value <?php echo $json->squadra[0]->vendita ? 'for-sale' : ''; ?>">
                                <?php echo $json->squadra[0]->vendita ? 'Si' : 'No'; ?>
                            </span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Valutazione squadra:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->rate ?? '0'; ?></span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Costo rinnovo:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->prezzo ?? '0'; ?> €</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Campionato</span>
                            <span class="overview-value"><?php echo $json_partecipazione->nome_competizione; ?></span>
                        </div>
                    </div>

                    <div class="overview-card">
                        <h3>Finanze</h3>
                        <div class="overview-item">
                            <span class="overview-label">Credito restante prossima stagione:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->finanze->credito ?? '0'; ?> FVM</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Punteggio ranking:</span>
                            <span class="overview-value"><?php echo $json_finanze->finanze_squadra->punteggio_ranking ?? '0'; ?></span>
                        </div>
                        <?php
                        if(!empty($json_finanze->finanze_squadra)) {
                            foreach ($json_finanze->finanze_squadra as $finanze) {
                                echo '<div class="overview-item">';
                                echo '<span class="overview-label">Guadagno crediti stadio campionato:</span>';
                                echo '<span class="overview-value">' . $finanze->guadagno_crediti_stadio_league .'</span>';
                                echo '</div>';
                                echo '<div class="overview-item">';
                                echo '<span class="overview-label">Guadagno crediti stadio coppa:</span>';
                                echo '<span class="overview-value">' . $finanze->guadagno_crediti_stadio_cup .'</span>';
                                echo '</div>';
                                echo '<div class="overview-item">';
                                echo '<span class="overview-label">Premi campionato:</span>';
                                echo '<span class="overview-value">' . $finanze->premi_league .'</span>';
                                echo '</div>';
                                echo '<div class="overview-item">';
                                echo '<span class="overview-label">Premi coppa:</span>';
                                echo '<span class="overview-value">' . $finanze->premi_cup .'</span>';
                                echo '</div>';
                                echo '<div class="overview-item">';
                                echo '<span class="overview-label">Guadagno crediti stadio campionato:</span>';
                                echo '<span class="overview-value">' . $finanze->prequalifiche_uefa_stadio .'</span>';
                                echo '</div>';
                                echo '<div class="overview-item">';
                                echo '<span class="overview-label">Prequalifiche uefa premio:</span>';
                                echo '<span class="overview-value">' . $finanze->prequalifiche_uefa_premio .'</span>';
                                echo '</div>';
                                echo '<div class="overview-item">';
                                echo '<span class="overview-label">Competizione uefa stadio:</span>';
                                echo '<span class="overview-value">' . $finanze->competizioni_uefa_stadio .'</span>';
                                echo '</div>';
                                echo '<div class="overview-item">';
                                echo '<span class="overview-label">Competizione uefa premio:</span>';
                                echo '<span class="overview-value">' . $finanze->competizioni_uefa_premio .'</span>';
                                echo '</div>';
                                echo '<div class="overview-item">';
                                echo '<span class="overview-label">Crediti residui cassa:</span>';
                                echo '<span class="overview-value">' . $finanze->crediti_residui_cassa .'</span>';
                                echo '</div>';
                                echo '<div class="overview-item">';
                                echo '<h3 class="overview-label">Totale crediti bilancio:</h3>';
                                echo '<span class="overview-value">' . $finanze->totale_crediti_bilancio .'</span>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>Nessun finanza registrata</p>';
                        }
                        ?>

                    </div>

                    <div class="overview-card">
                        <h3>Rosa</h3>
                        <div class="overview-item">
                            <span class="overview-label">Numero giocatori:</span>
                            <span class="overview-value"><?php echo count($json_associazioni->associazioni ?? []); ?></span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Portieri:</span>
                            <span class="overview-value"><?php echo array_reduce($json_associazioni->associazioni ?? [], function($carry, $item) {
                                    return $carry + ($item->ruolo_calciatore === 'P' ? 1 : 0);
                                }, 0); ?></span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Difensori:</span>
                            <span class="overview-value"><?php echo array_reduce($json_associazioni->associazioni ?? [], function($carry, $item) {
                                    return $carry + ($item->ruolo_calciatore === 'D' ? 1 : 0);
                                }, 0); ?></span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Centrocampisti:</span>
                            <span class="overview-value"><?php echo array_reduce($json_associazioni->associazioni ?? [], function($carry, $item) {
                                    return $carry + ($item->ruolo_calciatore === 'C' ? 1 : 0);
                                }, 0); ?></span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Attaccanti:</span>
                            <span class="overview-value"><?php echo array_reduce($json_associazioni->associazioni ?? [], function($carry, $item) {
                                    return $carry + ($item->ruolo_calciatore === 'A' ? 1 : 0);
                                }, 0); ?></span>
                        </div>
                    </div>
                </div>

                <div class="overview-card">
                    <h3>Ultimi Movimenti</h3>
                    <?php
                    if (!empty($json_trattative->trattative)) {
                        foreach ($json_trattative->trattative as $trattativa) {
                            echo '<div class="trattativa-container">';
                            echo '<div class="trattativa-header">';
                            echo '<h4>Trattativa #' . $trattativa->id . '</h4>';
                            echo '<div class="overview-item">';
                            echo '<span class="overview-label">Descrizione:</span>';
                            echo '<span class="overview-value">' . (!empty($trattativa->descrizione) ? strip_tags($trattativa->descrizione) : 'Nessuna descrizione') . '</span>';
                            echo '</div>';
                            echo '<div class="overview-item">';
                            echo '<span class="overview-label">Data creazione:</span>';
                            echo '<span class="overview-value">' . date('d/m/Y H:i', strtotime($trattativa->data_creazione)) . '</span>';
                            echo '</div>';
                            echo '<div class="overview-item">';
                            echo '<span class="overview-label">Squadre coinvolte:</span>';
                            echo '<span class="overview-value">' . $trattativa->nome_squadra1 . ' ↔ ' . $trattativa->nome_squadra2 . '</span>';
                            echo '</div>';
                            echo '<div class="overview-item">';
                            echo '<span class="overview-label">Stato:</span>';
                            echo '<span class="overview-value">' . ($trattativa->ufficializzata ? 'Ufficializzata' : 'In corso') . '</span>';
                            echo '</div>';
                            echo '</div>';

                            // Recupero le operazioni per questa trattativa
                            $operazioni = file_get_contents($baseUrl.'endpoint/operazioni/read.php?id_trattativa='.$trattativa->id, false, stream_context_create([
                                'http' => ['method' => 'GET', 'header' => 'Content-Type: application/json']
                            ]));

                            if ($operazioni) {
                                $json_operazioni = json_decode($operazioni);

                                if (!empty($json_operazioni->operazioni)) {
                                    echo '<div class="operazioni-container">';
                                    echo '<h5>Dettaglio operazioni:</h5>';

                                    foreach ($json_operazioni->operazioni as $operazione) {
                                        echo '<div class="operazione-item">';
                                        echo '<div class="overview-item">';
                                        echo '<span class="overview-label">Calciatore:</span>';
                                        echo '<span class="overview-value">' . $operazione->calciatore->nome . '</span>';
                                        echo '</div>';

                                        echo '<div class="overview-item">';
                                        echo '<span class="overview-label">Tipo operazione:</span>';
                                        echo '<span class="overview-value">' . $operazione->scambio->metodo . '</span>';
                                        echo '</div>';

                                        echo '<div class="overview-item">';
                                        echo '<span class="overview-label">Direzione:</span>';
                                        echo '<span class="overview-value">' . $operazione->trattativa->nome_squadra_1 . ' → ' . $operazione->trattativa->nome_squadra_2 . '</span>';
                                        echo '</div>';

                                        if (!empty($operazione->scambio->valore_riscatto)) {
                                            echo '<div class="overview-item">';
                                            echo '<span class="overview-label">Valore riscatto:</span>';
                                            echo '<span class="overview-value">€ ' . number_format($operazione->scambio->valore_riscatto, 2, ',', '.') . '</span>';
                                            echo '</div>';
                                        }

                                        if (!empty($operazione->finestra_mercato->nome)) {
                                            echo '<div class="overview-item">';
                                            echo '<span class="overview-label">Finestra di mercato:</span>';
                                            echo '<span class="overview-value">' . $operazione->finestra_mercato->nome . '</span>';
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                }
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-movements">';
                        echo '<p>Nessun movimento recente</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div
            <!-- Vista Rosa Giocatori -->
            <div class="grid-view" id="grid-view">
                <div class="grid-player-card-P">
                    <span class="overview-label">Valore FVM rosa:</span>
                    <span class="overview-value"><?php echo $json->squadra[0]->valore_fvm ?? '0'; ?> FVM</span>
                </div>
                <!-- I giocatori verranno inseriti qui via JavaScript -->
            </div>
            <!-- Vista Stadio -->
            <div class="stadium-view" id="stadium-view">
                <div class="overview-cards" id="upgradeCard" >
                    <div class="overview-card" style="
                                                display: flex;
                                                justify-content: space-between;
                                                align-items: center;
                                            ">
                        <h3>Ampliamento stadio</h3>
                        <button onclick="openStadiumUpgrade()">Amplia</button>
                    </div>
                </div>
                <div class="overview-cards" id="moduloStadium" style="display: none">
                    <div class="overview-card">
                        <div class="modulo-header" style="
                                            display: flex;
                                            flex-direction: row;
                                            width: 100%;
                                            align-items: center;
                                            justify-content: space-between;
                                            padding-bottom: 8px;
                                        ">
                            <h3>Modulo Ampliamento Stadio</h3>
                            <button onclick="closeStadiumUpgrade()">X</button>
                        </div>
                        <div class="modulo-content">
                            <div class="modulo-input">
                                <label for="livelloStadio"></label>
                                <input type="number" id="livelloStadio" min="1" max="10" placeholder="Inserisci livello (1-10)">
                            </div>
                            <button id="inviaModuloStadium" onclick="sendStadiumUpgrade('<?php echo addslashes($json->squadra[0]->nome_squadra); ?>')">Invia richiesta</button>
                        </div>
                    </div>
                </div>
                <div class="overview-cards">
                    <div class="overview-card">
                        <h3>Dettagli Stadio</h3>
                        <div class="overview-item">
                            <span class="overview-label">Nome:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->nome_stadio ?? 'N/A'; ?></span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Livello:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->livello_stadio ?? 'N/A'; ?></span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Manutenzione:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->costo_manutenzione ?? 'N/A'; ?> FVM</span>
                        </div>
                    </div>

                    <div class="overview-card">
                        <h3>Bonus Partite</h3>
                        <div class="overview-item">
                            <span class="overview-label">Nazionale:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->bonus_casa_nazionale ?? 'N/A'; ?> Pt</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">UEFA:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->bonus_casa_uefa ?? 'N/A'; ?> Pt</span>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Vista Albo d'Oro -->
            <div class="albo-view" id="albo-view">
                <div class="overview-card">
                    <h3>Titoli Vinti</h3>
                    <?php
                    if(!empty($json_albo->albo)) {
                        foreach ($json_albo->albo as $albo) {
                            echo '<div class="overview-item">';
                            echo '<span class="overview-label">Nome competizione:</span>';
                            echo '<span class="overview-value">' . $albo->nome_competizione . ' ' . $albo->stagione . '</span>';
                            echo '</div>';
                        }} else {
                        echo '<p>Nessun titolo vinto</p>';
                    }
                    ?>
                </div>
            </div>
            <!-- Vista Settore Giovanile -->
            <div class="sgs-view" id="sgs-view">
                <div class="overview-cards" id="buyCard" >
                    <div class="overview-card" style="
                                                                        display: flex;
                                                                        justify-content: space-between;
                                                                        align-items: center;
                                                                    ">
                        <h3>Acquista calciatori settore giovanile</h3>
                        <button onclick="openSgs()">Acquista</button>
                    </div>
                </div>
                <div class="overview-cards" id="moduloSgs" style="display: none">
                    <div class="overview-card">
                        <div class="modulo-header" style="
                                                                                    display: flex;
                                                                                    flex-direction: row;
                                                                                    width: 100%;
                                                                                    align-items: center;
                                                                                    justify-content: space-between;
                                                                                    padding-bottom: 8px;
                                                                                ">
                            <h3>Modulo Settore Giovanile</h3>
                            <button onclick="closeSgs()">X</button>
                        </div>
                        <div class="modulo-content" >
                            <div class="modulo-input">
                                <label for="nome1"></label>
                                <input type="text" id="nome1" placeholder="Nome calciatore 1">
                                <input type="number" id="offerta1" min="1" max="10" placeholder="Offerta calciatore 1">
                            </div>
                            <div class="modulo-input">
                                <label for="nome2"></label>
                                <input type="text" id="nome2" placeholder="Nome calciatore 2">
                                <input type="number" id="offerta2" min="1" max="10" placeholder="Offerta calciatore 2">
                            </div>
                            <div class="modulo-input">
                                <label for="nome3"></label>
                                <input type="text" id="nome3" placeholder="Nome calciatore 3">
                                <input type="number" id="offerta3" min="1" max="10" placeholder="Offerta calciatore 3">
                            </div>
                            <button id="inviaModuloSgs" onclick="sendSgs('<?php echo addslashes($json->squadra[0]->nome_squadra); ?>')">Invia</button>
                        </div>
                        <script>
                            // Gestione dei tab - VERSIONE DEFINITIVA
                            document.addEventListener('DOMContentLoaded', function() {
                                const tabs = document.querySelectorAll('.view-tab');

                                tabs.forEach(tab => {
                                    tab.addEventListener('click', function(e) {
                                        e.preventDefault();

                                        // Rimuovi active da tutti i tab
                                        tabs.forEach(t => t.classList.remove('active'));
                                        // Aggiungi active al tab cliccato
                                        this.classList.add('active');

                                        // Nascondi tutte le view
                                        document.querySelectorAll('.overview, .grid-view, .stadium-view, .albo-view, .sgs-view, .prl-view')
                                            .forEach(v => v.classList.remove('active'));

                                        // Mostra la view corretta
                                        const viewId = this.getAttribute('data-view') + '-view';
                                        const targetView = document.getElementById(viewId);
                                        if(targetView) {
                                            targetView.classList.add('active');
                                        }
                                    });
                                });

                                // Caricamento dei giocatori
                                fetch('endpoint/associazioni/read.php?id_squadra=<?php echo $id_squadra; ?>&prelazione=0')
                                    .then(response => response.json())
                                    .then(data => {
                                        const players = data.associazioni;
                                        const gridView = document.getElementById('grid-view');

                                        const sortedPlayers = players.sort((a, b) => {
                                            const order = { 'P': 0, 'D': 1, 'C': 2, 'A': 3 };
                                            return order[a.ruolo_calciatore] - order[b.ruolo_calciatore];
                                        });

                                        sortedPlayers.forEach(player => {
                                            const card = document.createElement('div');
                                            card.className = `grid-player-card-${player.ruolo_calciatore}`;
                                            card.innerHTML = `
                                            <div class="player-role-badge role-${player.ruolo_calciatore}">${player.ruolo_calciatore}</div>
                                            <div class="player-main-info">
                                                <div class="player-name">${player.nome_calciatore}</div>
                                                <div class="player-team">${player.nome_squadra_calciatore || 'N/A'}</div>
                                                <div class="player-stats">
                                                    <span>Costo: <span class="stat-value">${player.costo_calciatore} FVM</span></span>
                                                    <span>FVM: <span class="stat-value">${player.fvm} FVM</span></span>
                                                </div>
                                            </div>
                                        `;
                                            gridView.appendChild(card);
                                        });
                                    })
                                    .catch(error => console.error('Errore nel recupero dei dati:', error));

                            });
                        </script>
                    </div>
                </div>
                <div class="overview-cards">
                    <?php
                    if(!empty($json_settore_giovanile->settore_giovanile)) {
                        foreach ($json_settore_giovanile->settore_giovanile as $giocatore) {
                            echo '<div class="overview-card">';
                            echo '<div class="overview-item">';
                            echo '<span class="overview-label">Nome:</span>';
                            echo '<span class="overview-value">' . $giocatore->nome_calciatore . '</span>';
                            echo '<span class="overview-label">Stagione:</span>';
                            echo '<span class="overview-value">' . $giocatore->stagione . '</span>';
                            echo '<span class="overview-label">Fuori listone:</span>';
                            echo '<span class="overview-value">' . ($giocatore->fuori_listone == 1 ? 'Sì' : 'No') . '</span>';
                            echo '<span class="overview-label">Prima squadra:</span>';
                            echo '<span class="overview-value">' . ($giocatore->prima_squadra == 1 ? 'Sì' : 'No') . '</span>';
                            echo '</div>';
                            echo '</div>';
                        }} else {
                        echo ' <div class="overview-card">';
                        echo '<h3>Settore Giovanile</h3>';
                        echo '<p>Nessun giocatore nel settore giovanile</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            <!-- Vista Prelazioni -->
            <div class="prl-view" id="prl-view">
                <div class="overview-cards">
                    <div class="overview-card">
                        <h3>Prelazioni</h3>
                        <div class="overview-item">
                            <span class="overview-label">Nota 1:</span>
                            <span class="overview-value">Il valore di prelazione equivale al valore massimo tra FVM e Costo di acquisto del calciatore</span>
                        </div>
                        <div class="overview-item" style="margin-top: 15px;">
                            <span class="overview-label">Nota 2:</span>
                            <span class="overview-value" style="color: #ff4444; font-weight: bold;">
                                Prelazioni possibili fino al 1 Agosto 2026
                                <div id="countdown-prelazioni" style="display: inline-block; margin-left: 10px;"></div>
                            </span>
                        </div>
                    </div>

                    <?php if (count($calciatori_prelazione) > 0): ?>
                        <?php foreach ($calciatori_prelazione as $calciatore):
                            $valore_prelazione = max((int)$calciatore['fvm'], (int)$calciatore['costo_calciatore']);
                            ?>
                            <div class="overview-card grid-player-card-<?php echo $calciatore['ruolo_calciatore']; ?>">
                                <div class="player-role-badge role-<?php echo $calciatore['ruolo_calciatore']; ?>">
                                    <?php echo $calciatore['ruolo_calciatore']; ?>
                                </div>
                                <div class="player-main-info">
                                    <div class="player-name"><?php echo htmlspecialchars($calciatore['nome_calciatore']); ?></div>
                                    <div class="player-team"><?php echo htmlspecialchars($calciatore['nome_squadra_calciatore']); ?></div>
                                    <div class="player-stats">
                                        <span>Prelazione: <span class="stat-value"><?php echo $valore_prelazione; ?> FVM</span></span>
                                    </div>
                                    <button class="prelazione-btn"
                                            onclick="inviaRichiestaPrelazione(
                                                    '<?php echo $calciatore['id']; ?>',
                                                '<?php echo $squadraNome; ?>',
                                                    '<?php echo addslashes($calciatore['nome_calciatore']); ?>',
                                                    '<?php echo $calciatore['ruolo_calciatore']; ?>',
                                                    '<?php echo addslashes($calciatore['nome_squadra_calciatore']); ?>',
                                            <?php echo $valore_prelazione; ?>"
                                            style="margin-top: 10px;">
                                        Richiedi Prelazione
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="overview-card">
                            <div class="no-movements">
                                <p>Nessun calciatore disponibile per la prelazione</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <script>
                // Countdown per le prelazioni
                function updateCountdown() {
                    const endDate = new Date('August 1, 2026 23:59:59').getTime();
                    const now = new Date().getTime();
                    const distance = endDate - now;

                    // Calcoli per giorni, ore, minuti, secondi
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Visualizzazione
                    document.getElementById("countdown-prelazioni").innerHTML =
                        `<span style="color: #ff0000; font-weight: bold;">
                            [${days}g ${hours}h ${minutes}m ${seconds}s]
                         </span>`;

                    // Se la data è passata
                    if (distance < 0) {
                        document.getElementById("countdown-prelazioni").innerHTML =
                            `<span style="color: #ff0000; font-weight: bold;">TERMINE SCADUTO</span>`;
                    }
                }

                // Aggiorna il countdown ogni secondo
                setInterval(updateCountdown, 1000);
                updateCountdown(); // Esegui immediatamente

                function inviaRichiestaPrelazione(idAssociazione, nomeSquad, nomeCalciatore, ruolo, squadra, valorePrelazione) {

                    fetch()

                    // Crea il messaggio
                    const action = `Richiesta prelazione squadra ${nomeSquad}`;
                    const description = `Richiesta prelazione squadra ${nomeSquad}:\n
                        Calciatore: ${nomeCalciatore}\n
                        Ruolo: ${ruolo}\n
                        Squadra: ${squadra}\n
                        Valore prelazione: ${valorePrelazione} FVM.`;

                    fetch('sendMail.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ action: action, description: description })
                    })
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('status').innerHTML = data;
                        })
                        .catch(err => {
                            document.getElementById('status').innerHTML = '❌ Errore: ' + err;
                        });
                }
            </script>
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
    // Funzione per gestire l'acquisto di una squadra
    function openSgs() {
        const modulo = document.getElementById('moduloSgs');
        const buyCard = document.getElementById('buyCard');
        modulo.style.display = 'block';
        buyCard.style.display = 'none';
    }

    function closeSgs() {
        const modulo = document.getElementById('moduloSgs');
        const buyCard = document.getElementById('buyCard');
        modulo.style.display = 'none';
        buyCard.style.display = 'flex';
    }

    function sendSgs(nomeSquadra){
        const getNome1 = document.getElementById('nome1').value;
        const getNome2 = document.getElementById('nome2').value;
        const getNome3 = document.getElementById('nome3').value;
        const getOfferta1 = document.getElementById('offerta1').value || 0;
        const getOfferta2 = document.getElementById('offerta2').value || 0;
        const getOfferta3 = document.getElementById('offerta3').value || 0;

        const tot = parseInt(getOfferta1) + parseInt(getOfferta2) + parseInt(getOfferta3);

        if(tot>10){
            alert("L'offerta totale non può superare 10 FVM");
            return;
        }

        const numeroWhatsApp = "+393371447208";

        const messaggio = `Ciao, sono ${nomeSquadra} e interessato ai seguenti calciatori del settore giovanile:%0A` +
            `1. Nome: ${getNome1}, Offerta: ${getOfferta1} FVM%0A` +
            `2. Nome: ${getNome2}, Offerta: ${getOfferta2} FVM%0A` +
            `3. Nome: ${getNome3}, Offerta: ${getOfferta3} FVM`;
        const url = `https://wa.me/${numeroWhatsApp}?text=${messaggio}`;

        const conferma1 = confirm(`Sei sicuro di voler acquistare i calciatori indicati?`);
        if (!conferma1) return;

        const conferma2 = confirm("Questa operazione è irreversibile! Sei sicuro?");
        if (!conferma2) return;

        window.open(url);
    }

    function openStadiumUpgrade() {
        const modulo = document.getElementById('moduloStadium');
        const upgradeCard = document.getElementById('upgradeCard');
        modulo.style.display = 'block';
        upgradeCard.style.display = 'none';
    }

    function closeStadiumUpgrade() {
        const modulo = document.getElementById('moduloStadium');
        const upgradeCard = document.getElementById('upgradeCard');
        modulo.style.display = 'none';
        upgradeCard.style.display = 'flex';
    }

    function sendStadiumUpgrade(nomeSquadra) {
        const livelloStadio = document.getElementById('livelloStadio').value;

        if (!livelloStadio || livelloStadio < 1 || livelloStadio > 10) {
            alert("Inserisci un livello valido tra 1 e 10");
            return;
        }

        const numeroWhatsApp = "+393371447208";

        const messaggio = `Ciao, sono ${nomeSquadra} e vorrei richiedere un ampliamento dello stadio: ` + `Livello desiderato: ${livelloStadio} . `+ `Attendo conferma dei costi e dell'operazione.`;

        const url = `https://wa.me/${numeroWhatsApp}?text=${encodeURIComponent(messaggio)}`;

        const conferma1 = confirm(`Sei sicuro di voler richiedere l'ampliamento dello stadio al livello ${livelloStadio}?`);
        if (!conferma1) return;

        const conferma2 = confirm("Questa operazione comporterà dei costi! Sei sicuro di voler procedere?");
        if (!conferma2) return;

        window.open(url);
    }
</script>
