document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    if (!id) {
        document.getElementById("nomeCompetizione").textContent = "ID competizione non valido.";
        return;
    }

    fetch(`endpoint/partecipazione/read.php?id_competizione=${id}`)
        .then(res => res.json())
        .then(data => {
            // Aggiorna il titolo e l'header con il nome della competizione
            const nomeCompetizione = data.nome_competizione || "Competizione";
            document.getElementById("nomeCompetizione").textContent = nomeCompetizione;
            document.title = nomeCompetizione;

            const tbody = document.querySelector("#squadreTable tbody");
            tbody.innerHTML = "";

            if (data.squadre && data.squadre.length > 0) {
                data.squadre.forEach(squadra => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                            <td>${squadra.nome_squadra}</td>
                            <td>${squadra.presidente}</td>
                            <td>${squadra.vicepresidente}</td>
                             <td>${creaBottone("Dettagli", '', `visualizzaDettagli(${squadra.id})`)}</td>
                        `;
                    tbody.appendChild(tr);
                });
            } else {
                const tr = document.createElement("tr");
                tr.innerHTML = `<td colspan="3">Nessuna squadra trovata.</td>`;
                tbody.appendChild(tr);
            }
        })
        .catch(error => {
            console.error("Errore nel caricamento:", error);
            document.getElementById("nomeCompetizione").textContent = "Errore nel caricamento dei dati.";
            document.title = "Errore - Dettaglio Competizione";
        });
});