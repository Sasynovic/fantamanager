<?php
function fetchData($url): ?array {
    $context = stream_context_create([
        'http' => [
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        error_log("Errore nel recupero dati da: $url");
        return null;
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}
function getSquadre()
{
    return fetchData("https://barrettasalvatore.it/endpoint/squadra/read.php");
}
function getDivisioni()
{
    return fetchData("https://barrettasalvatore.it/endpoint/divisione/read.php");
}

function getCompetizioni($id_divisione){
    return fetchData("https://barrettasalvatore.it/endpoint/competizione/read.php?id_divisione=" . $id_divisione);
}

function getPartecipazioni($id_competizione){
    return fetchData("https://barrettasalvatore.it/endpoint/partecipazione/read.php?id=" . $id_competizione);
}

