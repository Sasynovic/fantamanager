<?php

require_once 'config/get.php';

$idCompetizione = $_GET['id'] ?? null;
$squadre = fetchData("https://barrettasalvatore.it/endpoint/partecipazione/read.php?id=" . $idCompetizione);

function mostraSquadre($squadre) {
    global $idCompetizione;
    if (($squadre === null) || !isset($squadre['squadre']) || empty($squadre['squadre'])) {
        echo $idCompetizione;
        return "<p>Nessuna squadra trovata per questa competizione. ID competizione ></p>";

    }

    $output = "<h1>Squadre Partecipanti</h1>";
    foreach ($squadre['squadre'] as $squadra) {
        $output .= "<div class='squadra'>";
        $output .= "<h3>" . htmlspecialchars($squadra['nome_squadra']) . "</h3>";
        $output .= "<p><strong>Presidente:</strong> " . htmlspecialchars($squadra['presidente']) . "</p>";
        $output .= "<p><strong>Vice Presidente:</strong> " . htmlspecialchars($squadra['vicepresidente']) . "</p>";
        $output .= "<p><strong>Stadio:</strong> " . htmlspecialchars($squadra['stadio']) . "</p>";
        $output .= "</div>";
    }
    return $output;
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Squadre Competizione</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <main>
        <?php
        if (!$idCompetizione) {
            echo "<p>ID competizione mancante.</p>";
        } else {
            echo mostraSquadre($squadre);
        }
        ?>
    </main>

    <!-- DEBUG: stampa i dati grezzi -->
    <!-- <pre><?php print_r($squadre); ?></pre> -->
</div>

</body>
</html>
