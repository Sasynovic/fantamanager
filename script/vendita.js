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

function visualizzaDettagli(id) {
    const modale = document.getElementById('modaleDettagli');
    const contenuto = document.getElementById('contenutoModale');
    contenuto.innerHTML = "<p>Caricamento dettagli...</p>";
    modale.style.display = 'block';

    Promise.all([
        fetch(`https://barrettasalvatore.it/endpoint/associazioni/read.php?id_squadra=${id}`).then(res => res.json()),
        fetch(`https://barrettasalvatore.it/endpoint/albo/read.php?id_squadra=${id}`).then(res => res.json()),
        fetch (`https://barrettasalvatore.it/endpoint/scambi/read.php?id=${id}`).then(res => res.json())
    ])
        .then(([calciatori, albo, scambi]) => {
            let html = `<h2>Dettagli Squadra</h2>`;

            const ruoli = {
                P: [], D: [], C: [], A: [], Altro: []
            };

            if (calciatori.associazioni?.length) {
                calciatori.associazioni.forEach(a => {
                    const ruolo = a.ruolo_calciatore || "Altro";
                    (ruoli[ruolo] || ruoli.Altro).push(a);
                });
            }

            html += "<h3>⚽ Giocatori:</h3>";
            for (const [ruolo, lista] of Object.entries(ruoli)) {
                if (!lista.length) continue;
                let classe = ruolo.toLowerCase();
                html += `<h4 class="${classe}">${ruolo} (${lista.length})</h4><ul style='list-style: none; padding-left: 0;'>`;
                lista.forEach(a => {
                    html += `<li style="margin-bottom: 6px;">
                <span style="font-weight: bold;">${a.nome_calciatore}</span> –💰 ${a.costo_calciatore} crediti -  ${a.eta} anni -  ${a.fvm} fvm
            </li>`;
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
                    html += `<div style="margin-bottom: 1em; border: 1px solid #ccc; border-radius: 8px; padding: 10px;">`;
                    html += `<p style="white-space: pre-line;"><strong>📝 Descrizione:</strong><br>${trattativa.descrizione}</p>`;
                    html += `<p><strong>📅 Fine:</strong> ${trattativa.data_fine || 'N/D'}</p>`;

                    if (trattativa.scambi?.length) {
                        html += `<table style="width: 100%; border-collapse: collapse; font-size: 0.9em; margin-top: 0.5em;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 6px;">Calciatore</th>
                        <th style="text-align: left; padding: 6px;">Da</th>
                        <th style="text-align: left; padding: 6px;">A</th>
                        <th style="text-align: left; padding: 6px;">Credito</th>
                    </tr>
                </thead>
                <tbody>`;
                        trattativa.scambi.forEach(s => {
                            html += `<tr>
                    <td style="padding: 6px;">${s.nome_calciatore ?? '💸 Solo credito'}</td>
                    <td style="padding: 6px;">${s.nome_squadra_cedente}</td>
                    <td style="padding: 6px;">${s.nome_squadra_ricevente}</td>
                    <td style="padding: 6px;">${s.credito_debito} 💰</td>
                </tr>`;
                        });
                        html += `</tbody></table>`;
                    } else {
                        html += "<p>Nessuno scambio registrato per questa trattativa.</p>";
                    }

                    html += `</div>`;
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

        let tabella = `<table>
                    <thead>
                        <tr>
                            <th>Nome Squadra</th>
                            <th>Stadio</th>
                            <th>Rate</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>`;

        data.squadre.forEach(sq => {
            const rate = parseInt(sq.rate) || 0;
            const id = sq.id;
            const numeroWhatsapp = "+393371447208"; // ← inserisci il tuo numero
            const messaggio = `Salve, vorrei maggiori informazioni per acquistare la squadra ${encodeURIComponent(sq.nome_squadra)}.`;
            const linkWhatsapp = `https://wa.me/${numeroWhatsapp}?text=${messaggio}`;

            tabella += `<tr>
                        <td>${sq.nome_squadra}</td>
                        <td>${sq.stadio}</td>
                        <td>${generaStelle(rate)}</td>
                        <td>
                            <a href="${linkWhatsapp}" target="_blank">
                                ${creaBottone("Acquista")}
                            </a>
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