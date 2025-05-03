// Funzioni di utility
function generaStelle(rate) {
    let stelle = '';
    for (let i = 1; i <= 5; i++) {
        stelle += i <= rate ? '★' : '☆';
    }
    return `<span style="color: gold; font-size: 1.2em;">${stelle}</span>`;
}

function creaBottone(label, classe = '', onclick = '') {
    return `<button class="${classe}" onclick="${onclick}">${label}</button>`;
}

// Array globale per memorizzare i dati delle squadre
let DATI_SQUADRE = [];

// Funzione per visualizzare i dettagli di una squadra
function visualizzaDettagli(id) {
    const modale = document.getElementById('modaleDettagli');
    const contenuto = document.getElementById('contenutoModale');
    contenuto.innerHTML = "<p>Caricamento dettagli...</p>";
    modale.style.display = 'block';

    // Trova i dati della squadra salvati prima
    const squadra = DATI_SQUADRE.find(sq => sq.id == id);

    Promise.all([
        fetch(`https://barrettasalvatore.it/endpoint/associazioni/read.php?id_squadra=${id}`).then(res => res.json()),
        fetch(`https://barrettasalvatore.it/endpoint/albo/read.php?id_squadra=${id}`).then(res => res.json()),
        fetch(`https://barrettasalvatore.it/endpoint/scambi/read.php?id=${id}`).then(res => res.json()),
        fetch(`https://barrettasalvatore.it/endpoint/settore_giovanile/read.php?id_squadra=${id}`).then(res => res.json()),
        fetch(`https://barrettasalvatore.it/endpoint/finanze_squadra/read.php?id_squadra=${id}`).then(res => res.json())
    ])
        .then(([calciatori, albo, scambi, settoreGiovanile, finanzeData]) => {
            let html = `<h2>Dettagli Squadra: ${squadra.nome_squadra}</h2>`;

            html += `<p><strong>Valore FVM rosa:</strong> ${squadra.valore_fvm} </p>`;

            html += "<h3>💰 Finanze del Club:</h3>";
            const finanze = finanzeData.finanze_squadra?.[0];
            if (finanze) {
                html += `<ul style='list-style: none; padding-left: 0;'>
        <li><strong>Stadio League:</strong> ${finanze.guadagno_crediti_stadio_league} 💰</li>
        <li><strong>Stadio Cup:</strong> ${finanze.guadagno_crediti_stadio_cup} 💰</li>
        <li><strong>Premi League:</strong> ${finanze.premi_league} 💰</li>
        <li><strong>Premi Cup:</strong> ${finanze.premi_cup} 💰</li>
        <li><strong>Prequalifiche UEFA (Stadio):</strong> ${finanze.prequalifiche_uefa_stadio} 💰</li>
        <li><strong>Prequalifiche UEFA (Premio):</strong> ${finanze.prequalifiche_uefa_premio} 💰</li>
        <li><strong>Competizioni UEFA (Stadio):</strong> ${finanze.competizioni_uefa_stadio} 💰</li>
        <li><strong>Competizioni UEFA (Premio):</strong> ${finanze.competizioni_uefa_premio} 💰</li>
        <li><strong>Crediti Residui in Cassa:</strong> ${finanze.crediti_residui_cassa} 💰</li>
        <li><strong>Totale Bilancio:</strong> <strong>${finanze.totale_crediti_bilancio} 💰</strong></li>
        <li><strong>Punteggio Ranking:</strong> ${finanze.punteggio_ranking}</li>
    </ul>`;
            } else {
                html += "<p>Dati finanziari non disponibili.</p>";
            }


            html += "<h3>🏟️ Informazioni Stadio:</h3>";
            html += `<ul style='list-style: none; padding-left: 0;'>
                    <li><strong>Nome:</strong> ${squadra.stadio?.trim() || "N/D"}</li>
                    <li><strong>Livello:</strong> ${squadra.livello_stadio}</li>
                    <li><strong>Manutenzione:</strong> ${squadra.costo_manutenzione} 💰</li>
                    <li><strong>Bonus casa Nazionale:</strong> ${squadra.bonus_casa_nazionale} 💰</li>
                    <li><strong>Bonus casa Uefa:</strong> ${squadra.bonus_casa_uefa} 💰</li>
                 </ul>`;

            const ruoli = { P: [], D: [], C: [], A: [], Altro: [] };
            if (calciatori.associazioni?.length) {
                calciatori.associazioni.forEach(a => {
                    const ruolo = a.ruolo_calciatore || "Altro";
                    (ruoli[ruolo] || ruoli.Altro).push(a);
                });
            }

            html += "<h3>⚽ Giocatori:</h3>";
            for (const [ruolo, lista] of Object.entries(ruoli)) {
                if (!lista.length) continue;
                html += `<h4>${ruolo} (${lista.length})</h4><ul style='list-style: none; padding-left: 0;'>`;
                lista.forEach(a => {
                    html += `<li><strong>${a.nome_calciatore}</strong> – 💰 ${a.costo_calciatore} crediti - ${a.eta} anni - ${a.fvm} fvm</li>`;
                });
                html += "</ul>";
            }

            html += "<h3>🏆 Albo d'Oro:</h3><ul style='list-style: none; padding-left: 0;'>";
            if (albo.albo?.length) {
                albo.albo.forEach(c => {
                    html += `<li>🥇 ${c.nome_competizione} - ${c.anno}</li>`;
                });
            } else {
                html += "<li>Nessuna coppa registrata.</li>";
            }
            html += "</ul>";

            html += "<h3>🧒 Settore Giovanile:</h3>";
            if (settoreGiovanile.settore_giovanile?.length) {
                html += `<ul style='list-style: none; padding-left: 0;'>`;
                settoreGiovanile.settore_giovanile.forEach(g => {
                    const nome = g.nome_calciatore || 'Giocatore sconosciuto';
                    const stagione = g.stagione || 'N/D';
                    const fuoriListone = g.fuori_listone == 1 ? "❌ Fuori listone" : "✅ Nel listone";
                    const primaSquadra = g.prima_squadra == 1 ? "✅ Aggregato alla prima squadra" : "❌ Non aggregato alla prima squadra";

                    html += `<li><strong>${nome}</strong> – 🗓️ Stagione: ${stagione} – ${fuoriListone} – ${primaSquadra}</li>`;
                });
                html += "</ul>";
            } else {
                html += "<p>Nessun calciatore presente nel settore giovanile.</p>";
            }


            html += "<h3>🔁 Trattative recenti:</h3>";
            if (scambi.trattive?.length) {
                scambi.trattive.forEach(trattativa => {
                    html += `<div style="margin-bottom: 1em; border: 1px solid #ccc; border-radius: 8px; padding: 10px;">
                            <p><strong>📝 Descrizione:</strong><br>${trattativa.descrizione}</p>
                            <p><strong>📅 Fine:</strong> ${trattativa.data_fine || 'N/D'}</p>`;
                    if (trattativa.scambi?.length) {
                        html += `<table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr><th>Calciatore</th><th>Da</th><th>A</th><th>Credito</th></tr>
                                </thead><tbody>`;
                        trattativa.scambi.forEach(s => {
                            html += `<tr>
                            <td>${s.nome_calciatore || '💸 Solo credito'}</td>
                            <td>${s.nome_squadra_cedente}</td>
                            <td>${s.nome_squadra_ricevente}</td>
                            <td>${s.credito_debito} 💰</td>
                        </tr>`;
                        });
                        html += "</tbody></table>";
                    } else {
                        html += "<p>Nessuno scambio registrato.</p>";
                    }
                    html += "</div>";
                });
            } else {
                html += "<p>Nessuna trattativa recente.</p>";
            }

            contenuto.innerHTML = html;
        })
        .catch(error => {
            contenuto.innerHTML = `<p class='error'>Errore: ${error.message}</p>`;
        });
}

// Funzione per aggiornare la tabella in base ai filtri
function aggiornaTabella() {
    const filtroPrezzo = parseFloat(document.getElementById("filtroPrezzo")?.value) || Infinity;
    const filtroRate = document.getElementById("filtroRate")?.value || "";
    const filtroLega = document.getElementById("filtroLega")?.value || "";

    const container = document.getElementById("output");

    // Se non ci sono dati, mostra un messaggio di errore
    if (!DATI_SQUADRE.length) {
        container.innerHTML = "<p class='error'>Nessuna squadra disponibile.</p>";
        return;
    }

    // Filtra le squadre in base ai criteri
    const squadreFiltrate = DATI_SQUADRE.filter(sq => {
        const prezzoValido = !filtroPrezzo || parseFloat(sq.prezzo) <= filtroPrezzo;
        const rateValido = !filtroRate || parseInt(sq.rate) == parseInt(filtroRate);
        const legaValida = !filtroLega || sq.lega === filtroLega;
        return prezzoValido && rateValido && legaValida;
    });

    // Se non ci sono risultati per i filtri applicati
    if (!squadreFiltrate.length) {
        container.innerHTML = "<p class='error'>Nessuna squadra corrisponde ai filtri selezionati.</p>";
        return;
    }

    // Crea la tabella con i risultati
    let tabella = `<table>
        <thead>
            <tr>
                <th>Nome Squadra</th>
                <th>Stadio</th>
                <th>Rate</th>
                <th>Prezzo</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>`;

    squadreFiltrate.forEach(sq => {
        const rate = parseInt(sq.rate) || 0;
        const id = sq.id;
        const linkWhatsapp = `https://wa.me/+393371447208?text=${encodeURIComponent("Salve, vorrei maggiori informazioni per acquistare la squadra " + sq.nome_squadra)}.`;

        tabella += `<tr>
            <td>${sq.nome_squadra}</td>
            <td>${sq.stadio?.trim() || "N/D"}</td>
            <td>${generaStelle(rate)}</td>
            <td>${sq.prezzo} €</td>
            <td>
                <a href="${linkWhatsapp}" target="_blank">${creaBottone("Acquista")}</a>
                ${creaBottone("Dettagli", '', `visualizzaDettagli(${id})`)}
            </td>
        </tr>`;
    });

    tabella += "</tbody></table>";
    container.innerHTML = tabella;
}

// Inizializza i filtri
function inizializzaFiltri(squadre) {
    // Popola il selettore delle leghe
    const legheUniche = [...new Set(squadre.map(s => s.lega).filter(Boolean))];
    const selectLega = document.getElementById("filtroLega");

    if (selectLega) {
        // Aggiungi l'opzione vuota
        const optionVuota = document.createElement("option");
        optionVuota.value = "";
        optionVuota.textContent = "Tutte le leghe";
        selectLega.appendChild(optionVuota);

        // Aggiungi le opzioni delle leghe
        legheUniche.forEach(lega => {
            const opt = document.createElement("option");
            opt.value = lega;
            opt.textContent = lega;
            selectLega.appendChild(opt);
        });
    }

    // Aggiungi gli event listener ai filtri
    const filtroPrezzo = document.getElementById("filtroPrezzo");
    const filtroRate = document.getElementById("filtroRate");

    if (filtroPrezzo) filtroPrezzo.addEventListener("input", aggiornaTabella);
    if (filtroRate) filtroRate.addEventListener("change", aggiornaTabella);
    if (selectLega) selectLega.addEventListener("change", aggiornaTabella);
}

// Caricamento iniziale delle squadre
function caricaSquadre() {
    fetch("https://barrettasalvatore.it/endpoint/squadra/read.php?vendita=1")
        .then(response => {
            if (!response.ok) throw new Error("Errore nel recupero dei dati.");
            return response.json();
        })
        .then(data => {
            const container = document.getElementById("output");

            if (!data.squadre?.length) {
                container.innerHTML = "<p class='error'>Nessuna squadra in vendita.</p>";
                return;
            }

            // Salva i dati delle squadre nell'array globale
            DATI_SQUADRE = data.squadre;

            // Inizializza i filtri
            inizializzaFiltri(DATI_SQUADRE);

            // Visualizza la tabella iniziale
            aggiornaTabella();
        })
        .catch(error => {
            document.getElementById("output").innerHTML = `<p class='error'>Errore: ${error.message}</p>`;
        });
}

// Avvia l'applicazione
document.addEventListener('DOMContentLoaded', function() {
    caricaSquadre();
});