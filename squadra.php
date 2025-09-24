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
            margin-bottom: 5px;
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

        .sgs-select{
            width: 50%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .button-container{
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sgs-input-group{
            margin-bottom: 15px;
        }

        .sgs-input{
            width: 49%;
        }

        /* Responsive design */
        @media (max-width: 1024px) {
            .modulo-content{
                display: grid;
                grid-auto-columns: min-content;
            }
            .sgs-input-group > *{
                width: 100%;
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
        <!-- Header -->
        <header class="main-header">
            <div class="main-text-header">
                <button class="back-button" onclick="window.history.back();">
                    <img src="public/chevron/chevronL.svg" alt="Indietro" height="40px" width="40px">
                </button>
                <h1>
                    <?php
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
                        $squadraCreditosgs = $json->squadra[0] -> finanze -> credito_sgs ?? 0;
                        $mercatoSgs = $json->squadra[0] -> finanze -> mercato_sgs ?? 0;
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
                        $json_prelazioni = json_decode($prelazioni, true);
                        if (!empty($json_prelazioni['associazioni'])) {
                            $calciatori_prelazione = $json_prelazioni['associazioni'];
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
                        $totale_crediti_bilancio = $json_finanze->finanze_squadra[0]->totale_crediti_bilancio ?? 0;

                    }

                    $id_divisione = 0;

                    $partecipazione = file_get_contents($baseUrl.'endpoint/partecipazione/read.php?id_squadra='.$id_squadra, false, stream_context_create([
                        'http' => ['method' => 'GET', 'header' => 'Content-Type: application/json']
                    ]));
                    if ($partecipazione) {
                        $json_partecipazione = json_decode($partecipazione);
                        $id_divisione = $json_partecipazione->id_divisione ?? 0;
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
                        <div class="overview-item">
                            <span class="overview-label">Punteggio ranking</span>
                            <span class="overview-value"><?php echo $json_finanze->finanze_squadra[0]->punteggio_ranking ?></span>
                        </div>
                    </div>

                    <div class="overview-card">
                        <h3>Finanze attuali</h3>

                        <?php if(!empty($json_finanze->finanze_squadra)) {
                            $finanze = $json_finanze->finanze_squadra[0]; // prendi la prima voce
                            ?>
                            <!-- Crediti attuali -->
                            <div class="overview-item">
                                <span class="overview-label">Indebitamento massimo residuo consentito:</span>
                                <span class="overview-value"><?php echo $json->squadra[0]->finanze->credito ?? '0'; ?> FVM</span>
                            </div>
                            <div class="overview-item">
                                <span class="overview-label">Totale crediti a bilancio:</span>
                                <span class="overview-value"><?php echo $finanze->totale_crediti_bilancio; ?></span>
                            </div>

                            <!-- Tutto il resto -->
                            <h3 class="overview-label">Finanze prossima stagione</h3>

                            <div class="overview-item">
                                <span class="overview-label">Guadagno crediti stadio campionato:</span>
                                <span class="overview-value"><?php echo $finanze->guadagno_crediti_stadio_league; ?></span>
                            </div>
                            <div class="overview-item">
                                <span class="overview-label">Guadagno crediti stadio coppa:</span>
                                <span class="overview-value"><?php echo $finanze->guadagno_crediti_stadio_cup; ?></span>
                            </div>
                            <div class="overview-item">
                                <span class="overview-label">Premi campionato:</span>
                                <span class="overview-value"><?php echo $finanze->premi_league; ?></span>
                            </div>
                            <div class="overview-item">
                                <span class="overview-label">Premi coppa:</span>
                                <span class="overview-value"><?php echo $finanze->premi_cup; ?></span>
                            </div>
                            <div class="overview-item">
                                <span class="overview-label">Prequalifiche UEFA stadio:</span>
                                <span class="overview-value"><?php echo $finanze->prequalifiche_uefa_stadio; ?></span>
                            </div>
                            <div class="overview-item">
                                <span class="overview-label">Prequalifiche UEFA premio:</span>
                                <span class="overview-value"><?php echo $finanze->prequalifiche_uefa_premio; ?></span>
                            </div>
                            <div class="overview-item">
                                <span class="overview-label">Competizione UEFA stadio:</span>
                                <span class="overview-value"><?php echo $finanze->competizioni_uefa_stadio; ?></span>
                            </div>
                            <div class="overview-item">
                                <span class="overview-label">Competizione UEFA premio:</span>
                                <span class="overview-value"><?php echo $finanze->competizioni_uefa_premio; ?></span>
                            </div>
                            <div class="overview-item">
                                <span class="overview-label">Totale crediti prossima stagione:</span>
                                <span class="overview-value"><?php echo $finanze->guadagno_crediti_stadio_league + $finanze->guadagno_crediti_stadio_cup + $finanze->competizioni_uefa_premio +$finanze->competizioni_uefa_stadio +$finanze->prequalifiche_uefa_premio + $finanze->prequalifiche_uefa_stadio + $finanze->premi_cup + $finanze->premi_league; ?></span>
                            </div>

                        <?php } else { ?>
                            <p>Nessuna finanza registrata</p>
                        <?php } ?>
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
                    <br>
                    <span class="overview-label">Costo rosa:</span>
                    <span class="overview-value">
                        <?php
                        $valore = 0;
                        foreach ($json_associazioni->associazioni as $associazione) {
                            $valore += $associazione->costo_calciatore;
                        }
                        echo $valore . ' FVM' ;
                     ?></span>
                </div>
                <!-- I giocatori verranno inseriti qui via JavaScript -->
            </div>
            <!-- Vista Stadio -->
            <div class="stadium-view" id="stadium-view">
                <!-- Modulo ampliamento -->
                <div class="overview-cards" id="moduloStadium" style="display: flex">
                    <div class="overview-card">
                        <div class="modulo-header" style="display: flex; flex-direction: row; width: 100%; align-items: center; justify-content: center; padding-bottom: 8px;">
                            <h3>Modulo Ampliamento Stadio - <span class="overview-value"><?php echo $json->squadra[0]->stadio->nome_stadio ?? 'N/A'; ?></span>
                            </h3>
                        </div>
                        <div class="modulo-content">
                            <div class="modulo-input">
                                <label for="livelloStadio">Seleziona livello:</label>
                                <input type="number" id="livelloStadio" min="<?php echo (1 + intval($json->squadra[0]->stadio->livello_stadio))?>" max="10" placeholder="Inserisci livello (1-10)" onchange="updateNewStadiumDetails()">
                            </div>
                            <button disabled class="view-tab" style="margin: 5px; background-color: var(--accento)" id="inviaModuloStadium" onclick="sendStadiumUpgrade(<?php echo $id_squadra ?>, <?php echo  $finanze->totale_crediti_bilancio ?>, '<?php echo $squadraNome?>')">Invia richiesta</button>
                        </div>
                    </div>
                </div>
                <!-- Valori attuali -->
                <div class="overview-cards">
                    <div class="overview-card">
                        <h3>Valori Attuali</h3>
                        <div class="overview-item">
                            <span class="overview-label">Livello:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->livello_stadio ?? 'N/A'; ?></span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Manutenzione:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->costo_manutenzione ?? 'N/A'; ?> FVM</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Costo Costruzione:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->costo_costruzione ?? 'N/A'; ?> FVM</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Bonus Casa Nazionale:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->bonus_casa_nazionale ?? 'N/A'; ?> Pt</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Bonus Casa UEFA:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->bonus_casa_uefa ?? 'N/A'; ?> Pt</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Incasso Sold Out:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->sold_out ?? 'N/A'; ?> FVM</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Abbonati:</span>
                            <span class="overview-value"><?php echo $json->squadra[0]->stadio->abbonati ?? 'N/A'; ?>%</span>
                        </div>
                    </div>
                    <div class="overview-card">
                        <h3>Valori Nuovi</h3>
                        <div class="overview-item">
                            <span class="overview-label">Livello:</span>
                            <span class="overview-value" id="newLevel">-</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Manutenzione:</span>
                            <span class="overview-value" id="newMaintenance">-</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Costo Costruzione:</span>
                            <span class="overview-value" id="newConstruction">-</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Bonus Casa Nazionale:</span>
                            <span class="overview-value" id="newBonusNazionale">-</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Bonus Casa UEFA:</span>
                            <span class="overview-value" id="newBonusUEFA">-</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Incasso Sold Out:</span>
                            <span class="overview-value" id="newSoldOut">-</span>
                        </div>
                        <div class="overview-item">
                            <span class="overview-label">Abbonati:</span>
                            <span class="overview-value" id="newAbbonati">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                const stadiumData = {
                    1: {manutenzione:0, costruzione:0, bonusN:0, bonusU:0, soldOut:1, abbonati:'3'},
                    2: {manutenzione:64, costruzione:82, bonusN:1, bonusU:1, soldOut:5, abbonati:'6'},
                    3: {manutenzione:140, costruzione:97, bonusN:1.5, bonusU:1, soldOut:11, abbonati:'9'},
                    4: {manutenzione:246, costruzione:132, bonusN:2, bonusU:1, soldOut:19, abbonati:'13'},
                    5: {manutenzione:384, costruzione:166, bonusN:2.5, bonusU:1, soldOut:29, abbonati:'17'},
                    6: {manutenzione:552, costruzione:203, bonusN:3, bonusU:1.5, soldOut:41, abbonati:'21'},
                    7: {manutenzione:760, costruzione:241, bonusN:3.5, bonusU:1.5, soldOut:55, abbonati:'26'},
                    8: {manutenzione:1006, costruzione:279, bonusN:4, bonusU:2, soldOut:71, abbonati:'31'},
                    9: {manutenzione:1292, costruzione:317, bonusN:4.5, bonusU:2, soldOut:89, abbonati:'36'},
                    10:{manutenzione:1651, costruzione:371, bonusN:5, bonusU:3, soldOut:109, abbonati:'45'}
                };
                function updateNewStadiumDetails() {
                    const livelloStadioAttuale = <?php echo $json->squadra[0]->stadio->livello_stadio ?? 0; ?>;
                    const livelloNew = document.getElementById('livelloStadio').value;

                    const dati = stadiumData[livelloNew];

                    let costoCostruzione = 0;

                    for (let i = livelloStadioAttuale + 1; i <= livelloNew; i++) {
                        costoCostruzione += stadiumData[i].costruzione;
                    }

                    document.getElementById('newLevel').innerText = livelloNew;
                    document.getElementById('newMaintenance').innerText = dati.manutenzione + ' FVM';
                    document.getElementById('newConstruction').innerText = costoCostruzione + ' FVM';
                    document.getElementById('newBonusNazionale').innerText = dati.bonusN + ' Pt';
                    document.getElementById('newBonusUEFA').innerText = dati.bonusU + ' Pt';
                    document.getElementById('newSoldOut').innerText = dati.soldOut + ' FVM';
                    document.getElementById('newAbbonati').innerText = dati.abbonati + '%';
                }

                function sendStadiumUpgrade(idSquadra, totaleCreditiBilancio, nomeSquadra) {
                    const passkeyInput = prompt('Inserisci la passkey per confermare la richiesta di prelazione:');
                    if (!passkeyInput) {
                        alert('⚠️ Operazione annullata: nessuna passkey inserita');
                        return;
                    }
                    fetch('endpoint/squadra/readPasskey.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            idSquadra1: idSquadra,
                            idSquadra2: idSquadra,
                            passkey: passkeyInput
                        })
                    })
                        .then(response => response.json())
                        .then(passkeyData => {
                            if (!passkeyData.success) {
                                throw new Error("❌ Passkey non valida!");
                            }
                            const livelloStadioAttuale = <?php echo $json->squadra[0]->stadio->livello_stadio; ?>;
                            const livelloStadioDesiderato = parseInt(document.getElementById('livelloStadio').value, 10);

                            if (!livelloStadioDesiderato || livelloStadioDesiderato < 1 || livelloStadioDesiderato > 10) {
                                alert('⚠️ Livello stadio non valido. Inserisci un valore tra 1 e 10.');
                                return;
                            }

                            let costoTotale = stadiumData[livelloStadioAttuale].manutenzione;

                            for (let i = livelloStadioAttuale + 1; i <= livelloStadioDesiderato; i++) {
                                costoTotale += stadiumData[i].costruzione;
                            }

                            if (costoTotale > totaleCreditiBilancio) {
                                alert('⚠️ Fondi insufficienti per l\'ampliamento dello stadio.');
                                return;
                            }

                            const nuovoValoreFinanza = totaleCreditiBilancio - costoTotale;

                            if (!confirm(`Il costo per aumentare lo stadio è di ${costoTotale} FVM. Il nuovo valore di finanza sarà ${nuovoValoreFinanza} FVM. Procedere?`)) {
                                return;
                            }

                            // Aggiorna finanze
                            return fetch(`endpoint/finanze_squadra/update.php?id=${idSquadra}`, {
                                method: 'PUT',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    id: idSquadra,
                                    totale_crediti_bilancio: nuovoValoreFinanza
                                })
                            })
                                .then(() => {
                                    // Aggiorna stadio
                                    return fetch(`endpoint/stadio/update.php?id=${idSquadra}`, {
                                        method: 'PUT',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({
                                            livello_stadio: livelloStadioDesiderato,
                                            costo_manutenzione: stadiumData[livelloStadioDesiderato].manutenzione,
                                            bonus_casa_n: stadiumData[livelloStadioDesiderato].bonusN,
                                            bonus_casa_u: stadiumData[livelloStadioDesiderato].bonusU,
                                            sold_out: stadiumData[livelloStadioDesiderato].soldOut,
                                            abbonati: stadiumData[livelloStadioDesiderato].abbonati,
                                            costo_costruzione: stadiumData[livelloStadioDesiderato].costruzione,
                                        })
                                    });
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alert('✅ Richiesta di ampliamento stadio inviata con successo!');
                                        const action = 'Ampliamento stadio '+ nomeSquadra;
                                        const description = `Richiesta di ampliamento stadio per ${nomeSquadra} da livello ${livelloStadioAttuale} a ${livelloStadioDesiderato}. Costo totale: ${costoTotale} FVM. Nuovo valore finanza: ${nuovoValoreFinanza} FVM.`;
                                        fetch('sendMail.php', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            body: JSON.stringify({ action: action, description: description })
                                        });
                                        location.reload();
                                    } else {
                                        throw new Error("❌ Errore nell'invio della richiesta di ampliamento stadio.");
                                    }
                                });
                        })
                        .catch(error => {
                            console.error('Errore:', error);
                            alert('⚠️ ' + error.message);
                        });
                }


            </script>
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
                    <div class="overview-card" id="sgsOffer">
                        <h3>Acquista calciatori settore giovanile</h3>
                        <p>Puoi offrire al max: <b><?php echo $squadraCreditosgs ?> FVM</b></p><br>

                        <div class="filter-list">
                            <select class="sgs-select" id="ruolo">
                                <option value="0" selected>Tutti i ruoli</option>
                                <option value="P">Portiere</option>
                                <option value="D">Difensore</option>
                                <option value="C">Centrocampista</option>
                                <option value="A">Attaccante</option>
                            </select>
                            <select class="sgs-select" id="squadra_reale">
                                <option value="0" selected>Tutte le squadre</option>
                                <option value="Atalanta">Atalanta</option>
                                <option value="Bologna">Bologna</option>
                                <option value="Cagliari">Cagliari</option>
                                <option value="Cremonese">Cremonese</option>
                                <option value="Como">Como</option>
                                <option value="Fiorentina">Fiorentina</option>
                                <option value="Genoa">Genoa</option>
                                <option value="Hellas Verona">Hellas Verona</option>
                                <option value="Inter">Inter</option>
                                <option value="Juventus">Juventus</option>
                                <option value="Lazio">Lazio</option>
                                <option value="Lecce">Lecce</option>
                                <option value="Milan">Milan</option>
                                <option value="Napoli">Napoli</option>
                                <option value="Parma">Parma</option>
                                <option value="Pisa">Pisa</option>
                                <option value="Roma">Roma</option>
                                <option value="Sassuolo">Sassuolo</option>
                                <option value="Torino">Torino</option>
                                <option value="Udinese">Udinese</option>
                            </select>
                        </div>

                        <div>
                            <div class="sgs-input-group">
                                <select id="sgs-select-1" class="sgs-select"></select>
                                <input  class="sgs-input"type="number" id="sgs-input-1" placeholder="Inserisci crediti" min="0" max="<?php echo $squadraCreditosgs ?>">
                            </div>

                            <div class="sgs-input-group">
                                <select id="sgs-select-2" class="sgs-select"></select>
                                <input class="sgs-input" type="number" id="sgs-input-2" placeholder="Inserisci crediti" min="0" max="<?php echo $squadraCreditosgs ?>">
                            </div>

                            <div class="sgs-input-group">
                                <select id="sgs-select-3" class="sgs-select"></select>
                                <input class="sgs-input" type="number" id="sgs-input-3" placeholder="Inserisci crediti" min="0" max="<?php echo $squadraCreditosgs ?>">
                            </div>
                        </div>

                        <button id="send-offer" class="tablinks" style="background-color: var(--accento)" onclick="sendOffer(<?php echo $id_squadra ?>, <?php echo $squadraCreditosgs ?>)">
                            Invia Offerte
                        </button>
                        <script>
                            function fetchSelect(){
                                const squadra_reale_filter = document.getElementById("squadra_reale");
                                const ruolo_filter = document.getElementById("ruolo");

                                const selectIds = ["sgs-select-1", "sgs-select-2", "sgs-select-3"];
                                let calciatoriList = []; // memorizza tutti i calciatori

                                const idDivisione = <?php echo $id_divisione; ?>;
                                fetch(`../endpoint/settore_giovanile/read_offer.php?id_divisione=${idDivisione}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        calciatoriList = data.gestione_settore_giovanile.map(item => {
                                            const c = item.associazione.calciatore;
                                            return {
                                                id: item.associazione.id,
                                                nome: `${c.cognome} ${c.nome}`,
                                                squadra: c.squadra,
                                                ruolo: c.ruolo,
                                                text: `${c.cognome} ${c.nome} - ${c.squadra} - ${c.ruolo}`,
                                                assegnato: c.offerte.some(o => o.id_squadra === <?php echo $id_squadra; ?>)
                                                    || c.offerte.some(o => Number(o.assegnato) === 1)
                                            };
                                        });

                                        // popolamento iniziale già filtrato per rimuovere i calciatori gia assegnati
                                        selectIds.forEach(id => populateSelect(document.getElementById(id), getFilteredList()));
                                    })
                                    .catch(error => console.error("Errore nel fetch:", error));

                                // funzione per filtrare la lista
                                function getFilteredList(){
                                    const squadraSel = squadra_reale_filter.value;
                                    const ruoloSel = ruolo_filter.value;

                                    return calciatoriList.filter(c => {
                                        let ok = true;
                                        if (squadraSel !== "0") ok = ok && c.squadra.toLowerCase() === squadraSel.toLowerCase();
                                        if (ruoloSel !== "0") ok = ok && c.ruolo === ruoloSel;
                                        if(c.assegnato) ok = false; // escludo calciatori già assegnati
                                        return ok;
                                    });
                                }

                                // funzione per popolare una select con lista filtrata
                                function populateSelect(select, list){
                                    const currentValue = select.value; // provo a mantenere selezione
                                    select.innerHTML = "";
                                    const defaultOption = document.createElement("option");
                                    defaultOption.value = "0";
                                    defaultOption.textContent = "Seleziona un calciatore";
                                    select.appendChild(defaultOption);

                                    list.forEach(c => {
                                        const option = document.createElement("option");
                                        option.value = c.id;
                                        option.textContent = c.text;
                                        select.appendChild(option);
                                    });

                                    // se il valore corrente è ancora presente, lo rimetto selezionato
                                    if ([...select.options].some(o => o.value === currentValue)){
                                        select.value = currentValue;
                                    }
                                }

                                // funzione per aggiornare tutte le select dopo filtro
                                function filterSelects(){
                                    const filtered = getFilteredList();
                                    selectIds.forEach(id => {

                                        const select = document.getElementById(id);

                                        // se la select ha già un valore selezionato diverso da "0", NON la ripopoliamo
                                        if (select.value !== "0" && select.value !== "") {
                                            return;
                                        }

                                        // altrimenti la ripopoliamo col filtro
                                        populateSelect(select, filtered);

                                        populateSelect(document.getElementById(id), filtered);
                                    });
                                    updateSelects();
                                }

                                // funzione per disabilitare duplicati
                                function updateSelects(){
                                    const selectedValues = selectIds.map(id => document.getElementById(id).value);

                                    selectIds.forEach(id => {
                                        const select = document.getElementById(id);
                                        Array.from(select.options).forEach(option => {
                                            option.disabled = selectedValues.includes(option.value) && select.value !== option.value;
                                        });
                                    });
                                }

                                // listeners
                                squadra_reale_filter.addEventListener("change", filterSelects);
                                ruolo_filter.addEventListener("change", filterSelects);

                                selectIds.forEach(id => {
                                    document.getElementById(id).addEventListener("change", updateSelects);
                                });
                            }
                        </script>
                    </div>

                    <?php

                    date_default_timezone_set("Europe/Rome");

                    $oggi = new DateTime();
                    $giorno = (int)$oggi->format("d"); // giorno del mese
                    $mese  = (int)$oggi->format("m"); // mese (01-12)
                    $ora   = (int)$oggi->format("H"); // ora (00-23)

                    // Controllo intervallo: 23–25 settembre dalle 9 alle 14
                    if($oggi >= new DateTime($oggi->format("Y")."-09-23 08:59") && $oggi <= new DateTime($oggi->format("Y")."-09-23 14:01"))
                    {
                        $mercatoAperto = 1;
                    }else if($oggi >= new DateTime($oggi->format("Y")."-09-24 08:59") && $oggi <= new DateTime($oggi->format("Y")."-09-24 14:01"))
                    {
                        $mercatoAperto = 1;
                    }else if($oggi >= new DateTime($oggi->format("Y")."-09-25 08:59") && $oggi <= new DateTime($oggi->format("Y")."-09-25 14:01"))
                    {
                        $mercatoAperto = 1;
                    }else{
                        $mercatoAperto = 0;
                    }

                    if($mercatoSgs == 1 && $mercatoAperto == 1){
                        echo '
                            <script>
                                const selectIds = ["sgs-select-1", "sgs-select-2", "sgs-select-3"];
                                let calciatoriList = []; // memorizza tutti i calciatori
        
                                // Carico i calciatori
                                fetchSelect();
                            
                            </script>';
                    }else if($mercatoSgs == 1 && $mercatoAperto == 0){
                        echo '<script>
                               const sezione = document.getElementById("sgsOffer");
                                sezione.innerHTML = `
                                  <h3>Acquista calciatori settore giovanile</h3>
                                  <p>Il mercato del settore giovanile è chiuso.<br> Hai acquisito il diritto di parteciparvi in quanto hai pagato la tariffa di accesso. <br>Il mercato è aperto i giorni 23, 24 e 25 Settembre dalle 9:00 alle 14:00.</p>`;
                              </script>';
                    }else{
                        echo '<script>
            const sezione = document.getElementById("sgsOffer");
            sezione.innerHTML = \'<h3>Acquista calciatori settore giovanile</h3><p>Il mercato del settore giovanile è chiuso. <br>Clicca su <b>richiedi apertura mercato</b> per acquistare la prelazione di accesso.</p><br>\';
            
            const buyAccess = document.createElement("button");
            buyAccess.classList.add("tablinks");
            buyAccess.style.backgroundColor = "var(--rosso)";
            buyAccess.textContent = "Richiedi apertura mercato";
            buyAccess.id = "buyAccess";
            
            sezione.appendChild(buyAccess);
            
            buyAccess.onclick = function() {
                const passkey = prompt("Inserisci la passkey per richiedere l\'apertura del mercato del settore giovanile:");
                if(!passkey) {
                    alert("Operazione annullata: nessuna passkey inserita");
                    return;
                }
                
                fetch("../endpoint/squadra/readPasskey.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        idSquadra1: "'.$id_squadra.'",
                        idSquadra2: "'.$id_squadra.'",
                        passkey: passkey
                    })
                })
                .then(response => response.json())
                .then(passkeyData => {
                    if(!passkeyData.success) {
                        throw new Error("Passkey non valida!");
                    }
                    if('.$totale_crediti_bilancio.' < 5) {
                        throw new Error("Crediti insufficienti per richiedere l\'apertura del mercato del settore giovanile. Sono necessari almeno 5 FVM.");
                    } else {
                        if(!confirm("La richiesta di apertura del mercato del settore giovanile costa 5 FVM. Procedere?")) {
                            return;
                        }
                    }
                    
                    fetch("../endpoint/finanze_squadra/update.php?id='.$id_squadra.'", {
                        method: "PUT",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            id: "'.$id_squadra.'",
                            totale_crediti_bilancio: '.$totale_crediti_bilancio.' - 5
                        })
                    })
                    .then(() => {
                        fetch("../endpoint/squadra/update.php?id='.$id_squadra.'", {
                            method: "PUT",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({
                                id: "'.$id_squadra.'",
                                mercato_sgs: 1
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                alert("Richiesta di apertura mercato inviata con successo!");
                                window.location.reload();
                            } else {
                                throw new Error("Errore nell\'invio della richiesta di apertura mercato.");
                            }
                        })
                        .catch(error => {
                            console.error("Errore:", error);
                            alert("Errore: " + error.message);
                        });
                    });
                })
                .catch(error => {
                    console.error("Errore:", error);
                    alert("Errore: " + error.message);
                });
            };
          </script>';
                    }
                    ?>
                </div>

                <div class="overview-cards">
                    <div class="overview-card">
                        <h3>Offerte settore giovanile</h3>

                        <p>Il costo di acquisto del calciatore verrà scalato dalle finanze della squadra solo in caso di accettazione dell'offerta.</p>
                        <br>

                        <!-- Bottone unico per sbloccare tutte le offerte -->
                        <button class="tablinks" style="background-color: var(--oro); margin-bottom: 10px;" onclick="unlockAll()">
                            🔓 Sblocca tutte le offerte
                        </button>

                        <br>

                        <div class="grid-view active" id="offerteGiovaniliContainer" style="display: grid">
                            <!-- Le offerte verranno caricate qui via JavaScript -->
                        </div>
                    </div>
                </div>

                <script>
                    let contaOfferte = 0;
                    let offerteReali = {};

                    fetch(`../endpoint/settore_giovanile/read_offer.php?id_squadra=<?php echo $id_squadra; ?>`)
                        .then(response => response.json())
                        .then(data => {
                            const container = document.getElementById("offerteGiovaniliContainer");
                            container.innerHTML = "";

                            if (data.gestione_settore_giovanile && data.gestione_settore_giovanile.length > 0) {
                                let offerteTrovate = false;

                                data.gestione_settore_giovanile.forEach(item => {
                                    const calciatore = item.associazione.calciatore;

                                    if (calciatore.offerte && calciatore.offerte.length > 0) {
                                        offerteTrovate = true;

                                        calciatore.offerte.forEach(offerta => {
                                            contaOfferte++;

                                            // Salviamo i dati reali in memoria
                                            offerteReali[offerta.id_offerta] = {
                                                cognome: calciatore.cognome,
                                                nome: calciatore.nome,
                                                squadra: calciatore.squadra,
                                                divisione: item.associazione.nome_divisione,
                                                valore_offerta: offerta.valore_offerta,
                                                assegnato: offerta.assegnato
                                            };

                                            const playerCard = document.createElement("div");
                                            playerCard.classList.add(`grid-player-card-${calciatore.ruolo}`);
                                            playerCard.id = `offer-card-${offerta.id_offerta}`;

                                            // Card iniziale (blur + dati nascosti)
                                            playerCard.innerHTML = `
                                ${offerta.assegnato !== 1 ? `
                                    <button class="tablinks-delete" style="background-color: var(--rosso)"
                                        onclick="deleteOffer(${offerta.id_offerta}, ${offerta.valore_offerta})">X</button>` : ""}
                                <div class="player-main-info" style="filter: blur(8px);">
                                    <div class="player-name">*** ***</div>
                                    <div class="player-team">Squadra nascosta</div>
                                    <div class="player-team">
                                        <span>Offerta: <span class="stat-value">***</span></span>
                                    </div>
                                    ${offerta.assegnato !== 1 ? `
                                        <div class="button-container">
                                            <button class="tablinks" style="background-color: var(--oro);" id="edit-offer-${offerta.id_offerta}"
                                                onclick="editOffer(${offerta.id_offerta}, ${<?php echo (int)$squadraCreditosgs ?>})">
                                                Modifica Offerta
                                            </button>
                                        </div>` : ""}
                                    ${offerta.assegnato === 1 ? `
                                        <p style="color: var(--verde); font-weight: bold; margin-top: 10px;">Offerta accettata!</p>` : ""}
                                </div>
                            `;

                                            container.appendChild(playerCard);
                                        });
                                    }
                                });

                                if (!offerteTrovate) {
                                    container.innerHTML = '<p>Nessuna offerta in corso</p>';
                                }
                            } else {
                                container.innerHTML = '<p>Nessuna offerta in corso</p>';
                            }
                        })
                        .catch(error => {
                            console.error("Errore nel fetch delle offerte giovanili:", error);
                            document.getElementById("offerteGiovaniliContainer").innerHTML = '<p>Nessuna offerta trovata</p>';
                        });

                    // 🔓 Funzione per sbloccare TUTTE le card
                    function unlockAll() {
                        const passkeyInput = prompt("Inserisci la passkey per sbloccare tutte le offerte:");
                        if (!passkeyInput) {
                            alert("⚠️ Nessuna passkey inserita");
                            return;
                        }

                        fetch('../endpoint/squadra/readPasskey.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                idSquadra1: <?php echo $id_squadra ?>,
                                idSquadra2: <?php echo $id_squadra ?>,
                                passkey: passkeyInput
                            })
                        })
                            .then(response => response.json())
                            .then(passkeyData => {
                                if (!passkeyData.success) {
                                    alert("❌ Passkey non valida!");
                                    return;
                                }

                                // Sblocca tutte le card in un colpo
                                for (let idOfferta in offerteReali) {
                                    const dettagli = offerteReali[idOfferta];
                                    const card = document.getElementById(`offer-card-${idOfferta}`);
                                    if (!card) continue;

                                    const mainInfo = card.querySelector(".player-main-info");
                                    if (mainInfo) {
                                        mainInfo.style.filter = "none";
                                        mainInfo.innerHTML = `
                                        <div class="player-name">${dettagli.cognome} ${dettagli.nome}</div>
                                        <div class="player-team">${dettagli.squadra} - Div. ${dettagli.divisione}</div>
                                        <div class="player-team">
                                            <span>Offerta: <span class="stat-value">${dettagli.valore_offerta} FVM</span></span>
                                        </div>
                                           ${dettagli.assegnato === 1
                                            ? `<p style="color: var(--verde); font-weight: bold; margin-top: 10px;">Offerta accettata!</p>`
                                            : `
                                            <div class="button-container">
                                                <button class="tablinks" style="background-color: var(--oro);"
                                                    onclick="editOffer(${idOfferta}, ${<?php echo (int)$squadraCreditosgs ?>})">
                                                    Modifica Offerta
                                                </button>
                                            </div>
                                        `}
                                `;
                                    }
                                }
                            })
                            .catch(err => {
                                console.error("Errore:", err);
                                alert("⚠️ Errore durante lo sblocco");
                            });
                    }


                function deleteOffer(offerId, valore_offerta) {
                        const nuovoCredito = <?php echo (int)$squadraCreditosgs ?> + valore_offerta;
                        if (!confirm("Sei sicuro di voler eliminare questa offerta?")) return;

                        const passkey = prompt("Inserisci la passkey per confermare l'eliminazione dell'offerta:");
                        if (!passkey) {
                            alert("Operazione annullata: nessuna passkey inserita");
                            return;
                        }

                        fetch('../endpoint/squadra/readPasskey.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                idSquadra1: <?php echo $id_squadra; ?>,
                                idSquadra2: <?php echo $id_squadra; ?>,
                                passkey: passkey
                            })
                        })
                        .then(response => response.json())
                        .then(passkeyData => {
                            if (!passkeyData.success) {
                                throw new Error("Passkey non valida!");
                            }
                            fetch(`../endpoint/settore_giovanile/delete_offer.php?id=${offerId}`, {
                                method: "DELETE"
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        fetch('../endpoint/squadra/update.php?id=<?php echo $id_squadra; ?>', {
                                            method: 'PUT',
                                            headers: { 'Content-Type': 'application/json' },
                                            body: JSON.stringify({
                                                id: <?php echo $id_squadra; ?>,
                                                credito_sgs: nuovoCredito
                                        })
                                        })
                                        .then( response => response.json() )
                                        .then( data =>
                                            {
                                                if(data.success) {
                                                    alert("Offerta eliminata con successo!");
                                                    location.reload();
                                            }else {
                                                    alert("Errore nell'aggiornamento dei crediti SGS.");
                                                }
                                            }
                                        );
                                    } else {
                                        throw new Error("Errore nell'eliminazione dell'offerta.");
                                    }
                                })
                                .catch(error => {
                                    console.error("Errore:", error);
                                    alert("Errore: " + error.message);
                                });
                        })
                        .catch(error => {
                            console.error("Errore:", error);
                            alert("Errore: " + error.message);
                        });
                    }

                    function editOffer(offerId, maxOffer) {
                        // Implementa la logica per modificare l'offerta
                        const oldValueElement = document.getElementById(`offer-valure-${offerId}`);
                        oldValueElement.style.display = 'none';

                        const editButton = document.getElementById(`edit-offer-${offerId}`);
                        editButton.style.display = 'none';

                        const newValueInput = document.createElement('input');
                        newValueInput.type = 'number';
                        newValueInput.min = '0';
                        newValueInput.max = '10';
                        newValueInput.value = oldValueElement.textContent.replace(' FVM', '');
                        newValueInput.id = `new-offer-value-${offerId}`;
                        oldValueElement.parentNode.appendChild(newValueInput);

                        const saveButton = document.createElement('button');
                        saveButton.textContent = 'Salva';
                        saveButton.classList.add('tablinks');
                        saveButton.style.backgroundColor = 'var(--verde)';


                        const cancelButton = document.createElement('button');
                        cancelButton.textContent = 'Annulla';
                        cancelButton.classList.add('tablinks');
                        cancelButton.style.backgroundColor = 'var(--rosso)';
                        editButton.parentNode.appendChild(saveButton);
                        editButton.parentNode.appendChild(cancelButton);
                        editButton.style.display = 'none';

                        cancelButton.onclick = () => {
                            newValueInput.remove();
                            oldValueElement.style.display = '';
                            saveButton.remove();
                            cancelButton.remove();
                            editButton.style.display = 'block';
                        }

                        saveButton.onclick = () => {
                            const newValue = parseInt(newValueInput.value);
                            const oldValue = parseInt(oldValueElement.textContent.replace(' FVM', ''));

                            const maxOfferEdit = <?php echo (int)$squadraCreditosgs ?> + oldValue;

                            if (isNaN(newValue) || newValue < 0 || newValue > maxOfferEdit) {
                                alert(`Valore non valido. Inserisci un numero tra 0 e ${maxOfferEdit}`);
                                return;
                            }

                            const passkey = prompt("Inserisci la passkey per confermare la modifica dell'offerta:");
                            if (!passkey) {
                                alert("Operazione annullata: nessuna passkey inserita");
                                return;
                            }

                            fetch('../endpoint/squadra/readPasskey.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    idSquadra1: <?php echo $id_squadra; ?>,
                                    idSquadra2: <?php echo $id_squadra; ?>,
                                    passkey: passkey
                                })
                            })
                            .then(response => response.json())
                            .then(passkeyData => {
                                if (!passkeyData.success) {
                                    throw new Error("Passkey non valida!");
                                }
                                fetch(`../endpoint/settore_giovanile/update_offer.php?id=${offerId}`, {
                                    method: 'PUT',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ valore_offerta: newValue })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        fetch('../endpoint/squadra/update.php?id=<?php echo $id_squadra; ?>', {
                                            method: 'PUT',
                                            headers: { 'Content-Type': 'application/json' },
                                            body: JSON.stringify({
                                                id: <?php echo $id_squadra; ?>,
                                                credito_sgs: maxOfferEdit - newValue
                                            })
                                        })
                                        .then( response => response.json() )
                                        .then( data =>
                                            {
                                                if(data.success) {
                                                    alert('Offerta aggiornata con successo!');
                                                    location.reload();
                                                }else {
                                                    alert("Errore nell'aggiornamento dei crediti SGS.");
                                                }
                                            }
                                        );

                                    } else {
                                        throw new Error('Errore nell\'aggiornamento dell\'offerta.');
                                    }
                                })
                                .catch(error => {
                                    console.error('Errore:', error);
                                    alert('Errore: ' + error.message);
                                });
                            })
                            .catch(error => {
                                console.error('Errore:', error);
                                alert('Errore: ' + error.message);
                            });
                        };
                    }

                    function sendOffer(idSquadra, maxOffer) {
                        const selectIds = ["sgs-select-1", "sgs-select-2", "sgs-select-3"];
                        let totalOffer = 0;
                        let offers = [];

                        for (let i = 0; i < selectIds.length; i++) {
                            const select = document.getElementById(selectIds[i]);
                            const input = document.getElementById(`sgs-input-${i + 1}`);

                            const calciatoreId = parseInt(select.value);
                            const offerValue = parseInt(input.value);

                            if (calciatoreId && !isNaN(offerValue) && offerValue > 0) {
                                totalOffer += offerValue;
                                offers.push({ calciatoreId, offerValue });
                            }
                        }

                        if (offers.length === 0) {
                            alert("Inserisci almeno un'offerta valida.");
                            return;
                        }

                        if (totalOffer > maxOffer) {
                            alert(`Il totale delle offerte (${totalOffer} FVM) supera il credito disponibile (${maxOffer} FVM).`);
                            return;
                        }
                        if(contaOfferte + offers.length > 3){
                            alert("Puoi avere un massimo di 3 offerte attive contemporaneamente. Elimina alcune offerte prima di inviarne di nuove.");
                            return;
                        }

                        const passkey = prompt("Inserisci la passkey per confermare l'invio delle offerte:");
                        if (!passkey) {
                            alert("Operazione annullata: nessuna passkey inserita");
                            return;
                        }

                        fetch('../endpoint/squadra/readPasskey.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                idSquadra1: idSquadra,
                                idSquadra2: idSquadra,
                                passkey: passkey
                            })
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Errore nella verifica passkey');
                                }
                                return response.json();
                            })
                            .then(passkeyData => {
                                if (!passkeyData.success) {
                                    throw new Error("Passkey non valida!");
                                }

                                // Invia tutte le offerte
                                return Promise.all(offers.map(offer => {
                                    return fetch('../endpoint/settore_giovanile/create_offer.php', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({
                                            id_squadra: idSquadra,
                                            id_associazione_g: offer.calciatoreId,
                                            valore_offerta: offer.offerValue
                                        })
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Errore nell\'invio dell\'offerta');
                                        }
                                        return response.json();
                                    });
                                }));


                            })
                            .then(results => {
                                // Verifica che tutte le offerte siano state create con successo
                                const allSuccess = results.every(result => result && result.success);

                                if (!allSuccess) {
                                    throw new Error('Errore nell\'invio di una o più offerte');
                                }

                                // AGGIORNA I CREDITI UNA VOLTA SOLA DOPO TUTTE LE OFFERTE
                                return fetch('../endpoint/squadra/update.php?id=' + idSquadra, {
                                    method: 'PUT',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({
                                        id: idSquadra,
                                        credito_sgs: maxOffer - totalOffer
                                    })
                                });
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Errore nell\'aggiornamento dei crediti');
                                }
                                return response.json();
                            })
                            .then(creditData => {
                                if (!creditData.success) {
                                    throw new Error('Errore nell\'aggiornamento dei crediti SGS');
                                }

                                alert('Offerte inviate con successo!');
                                location.reload();
                            })
                            .catch(error => {
                                console.error('Errore:', error);
                                alert('Errore: ' + error.message);
                            });
                    }

                </script>

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
                                    <div class="player-stats"><span>Data fine prelazione: <span><span class="stat-value"><?php echo htmlspecialchars(date('Y-m-d', strtotime($calciatore['fine_prelazione'])));?></span></span>
                                    </div>
                                    </div>
                                <button disabled class="tablinks" style="background-color: var(--accento)"
                                        onclick="inviaRichiestaPrelazione(
                                        <?php echo $id_squadra; ?>,
                                        <?php echo $calciatore['id']; ?>,
                                        <?php echo $calciatore['fuori_listone']?>,
                                                '<?php echo $squadraNome; ?>',
                                                '<?php echo addslashes($calciatore['nome_calciatore']); ?>',
                                                '<?php echo $calciatore['ruolo_calciatore']; ?>',
                                                '<?php echo addslashes($calciatore['nome_squadra_calciatore']); ?>',
                                        <?php echo intval($valore_prelazione); ?>,
                                        <?php echo intval($finanze->totale_crediti_bilancio);?>,
                                        <?php echo intval($calciatore->costo_calciatore); ?>
                                                )">Richiedi Prelazione</button>
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
            <!--script prelazioni-->
            <script>
                // function inviaRichiestaPrelazione(idSquadra, idAssociazione, fuoriListone, nomeSquad, nomeCalciatore, ruolo, squadra, valorePrelazione, finanzeSquadra, costoCalciatore) {
                //     if (fuoriListone == 0) {
                //         const passkeyInput = prompt('Inserisci la passkey per confermare la richiesta di prelazione:');
                //         if (!passkeyInput) {
                //             alert('⚠️ Operazione annullata: nessuna passkey inserita');
                //             return;
                //         }
                //         // 🔑 Verifica passkey
                //         fetch('endpoint/squadra/readPasskey.php', {
                //             method: 'POST',
                //             headers: { 'Content-Type': 'application/json' },
                //             body: JSON.stringify({
                //                 idSquadra1: idSquadra,
                //                 idSquadra2: idSquadra,
                //                 passkey: passkeyInput
                //             })
                //         })
                //             .then(response => response.json())
                //             .then(passkeyData => {
                //                 if (!passkeyData.success) {
                //                     throw new Error("❌ Passkey non valida!");
                //                 }
                //                 if (valorePrelazione > finanzeSquadra) {
                //                     throw new Error("❌ Non hai abbastanza crediti per richiedere questa prelazione!");
                //                 }
                //
                //                 const val = finanzeSquadra - valorePrelazione;
                //                 // Aggiorna le finanze
                //                 return fetch(`../endpoint/finanze_squadra/update.php?id=${idSquadra}`, {
                //                     method: 'PUT',
                //                     headers: { 'Content-Type': 'application/json' },
                //                     body: JSON.stringify({
                //                         id: idSquadra,
                //                         totale_crediti_bilancio: val
                //                     })
                //                 });
                //             })
                //             .then(() => {
                //                 // Aggiorna l'associazione
                //                 return fetch(`../endpoint/associazioni/update.php?id=${idAssociazione}`, {
                //                     method: 'PUT',
                //                     headers: { 'Content-Type': 'application/json' },
                //                     body: JSON.stringify({
                //                         id: idAssociazione,
                //                         timestamp : 0,
                //                         prelazione: 0
                //                     })
                //                 });
                //             })
                //             .then(response => response.json())
                //             .then(data => {
                //                 if (!data.success) {
                //                     throw new Error("❌ Errore nell'invio della richiesta di prelazione: " + data.message);
                //                 }
                //
                //                 const action = `Richiesta prelazione squadra ${nomeSquad}`;
                //                 const description = `
                //                 <p>Richiesta prelazione squadra <strong>${nomeSquad}</strong> (ID: <strong>${idSquadra}</strong>)</p>
                //                 <ul>
                //                     <li><b>Calciatore:</b> ${nomeCalciatore}</li>
                //                     <li><b>Ruolo:</b> ${ruolo}</li>
                //                     <li><b>Squadra:</b> ${squadra}</li>
                //                     <li><b>Costo calciatore:</b> ${costoCalciatore} FVM</li>
                //                     <li><b>Valore prelazione:</b> ${valorePrelazione} FVM</li>
                //                 </ul>
                //             `;
                //
                //                 alert('✅ Richiesta di prelazione inviata con successo!');
                //
                //                 return fetch('sendMail.php', {
                //                     method: 'POST',
                //                     headers: { 'Content-Type': 'application/json' },
                //                     body: JSON.stringify({ action: action, description: description })
                //                 });
                //             })
                //             .then(response => response.text())
                //             .then(data => {
                //                  // 🔄 Aggiorna la schermata dopo 1 secondo
                //                 setTimeout(() => {
                //                     location.reload();
                //                 }, 1000);
                //             })
                //             .catch(err => {
                //                 alert(err.message || ('❌ Errore: ' + err));
                //             });
                //     } else {
                //         alert('⚠️ Il calciatore è fuori listone, non è possibile richiedere la prelazione');
                //     }
                // }

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
                                                <div class="player-stats">
                                                    <span>Movimenti: <span class="stat-value">${player.n_movimenti} </span></span>
                                                    <span>Scambiato: <span class="stat-value">${player.scambiato ? "Sì" : "No"}</span></span>
                                                </div>
                                            </div>
                                        `;
                    gridView.appendChild(card);
                });
            })
            .catch(error => console.error('Errore nel recupero dei dati:', error));
    });
</script>

