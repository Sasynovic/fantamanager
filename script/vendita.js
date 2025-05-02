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

let DATI_SQUADRE = []; // Salviamo le squadre per accedere ai dettagli successivamente

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
        fetch(`https://barrettasalvatore.it/endpoint/scambi/read.php?id=${id}`).then(res => res.json())
    ])
        .then(([calciatori, albo, scambi]) => {
            let html = `<h2>Dettagli Squadra: ${squadra.nome_squadra}</h2>`;

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

// Caricamento iniziale squadre
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

        DATI_SQUADRE = data.squadre; // Salva per accesso ai dettagli

        let tabella = `<table>
                        <thead>
                            <tr><th>Nome Squadra</th><th>Stadio</th><th>Rate</th><th>Prezzo</th><th>Azioni</th></tr>
                        </thead><tbody>`;

        data.squadre.forEach(sq => {
            const rate = parseInt(sq.rate) || 0;
            const id = sq.id;
            const linkWhatsapp = `https://wa.me/+393371447208?text=${encodeURIComponent("Salve, vorrei maggiori informazioni per acquistare la squadra " + sq.nome_squadra)}.`;

            tabella += `<tr>
                            <td>${sq.nome_squadra}</td>
                            <td>${sq.stadio?.trim() || "N/D"}</td>
                            <td>${generaStelle(rate)}</td>
                            <td>${sq.prezzo} 💰</td>
                            <td>
                                <a href="${linkWhatsapp}" target="_blank">${creaBottone("Acquista")}</a>
                                ${creaBottone("Dettagli", '', `visualizzaDettagli(${id})`)}
                            </td>
                        </tr>`;
        });

        tabella += "</tbody></table>";
        container.innerHTML = tabella;
    })
    .catch(error => {
        document.getElementById("output").innerHTML = `<p class='error'>Errore: ${error.message}</p>`;
    });


