<?php
require_once 'config/get.php';

$squadre = getSquadre();
$divisioni = getDivisioni();

function mostraSquadre($squadre) {
    if ($squadre === null) return "<p>Errore nel recupero delle squadre.</p>";

    $output = "<h1>Squadre</h1>";
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
    <title>Squadre di Calcio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const divisioni = document.querySelectorAll(".divisione");
            divisioni.forEach(div => {
                div.addEventListener("click", () => {
                    const competizioni = div.nextElementSibling;
                    competizioni.style.display = competizioni.style.display === "block" ? "none" : "block";
                });
            });
        });
    </script>
</head>
<body>

<div class="container">
    <main>
        <?= mostraSquadre($squadre); ?>
    </main>

    <aside class="elenco">
        <ul>
            <?php foreach ($divisioni['divisioni'] as $divisione): ?>
                <li>
                    <div class="divisione">
                        <span><?= htmlspecialchars($divisione['nome_divisione']); ?></span>
                        <img src="public/flag/<?= htmlspecialchars($divisione['bandiera']); ?>" alt="Bandiera" class="flag">
                    </div>
                    <ul class="competizioni">
                        <?php
                        $competizioni = getCompetizioni($divisione['id']);
                        if ($competizioni !== null) {
                            foreach ($competizioni['competizioni'] as $competizione): ?>
                                <li class="competizione" data-id="<?= $competizione['id']; ?>">
                                    <a href="competizione.php?id=<?= $competizione['id']; ?>">
                                        <?= htmlspecialchars($competizione['nome_competizione']); ?>
                                    </a>
                                </li>


                            <?php endforeach;
                        }
                        ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>
</div>

</body>
</html>
