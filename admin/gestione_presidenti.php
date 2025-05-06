<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // reindirizza se non loggato
    exit;
}

require_once 'heading.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Presidenti</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-container">
    <div class="header">
        <h1>Gestione Presidenti</h1>
        <button id="toggle-add-form" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Nuovo presidente
        </button>
    </div>

    <p class="subtitle">Gestisci i presidenti presenti sul tuo sito web</p>

    <div class="card-all">
        <div class="filter-section">


            <div class="form-group">
                <label for="filter-news">Presidenti da mostrare</label>
                <select id="filter-news">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
            </div>

            <div class="form-group">
                <label for="search-news">Cerca</label>
                <input type="text" id="search-news" placeholder="Cerca presidenti...">
            </div>

            <div class="form-group" style="align-self: flex-end;">
                <button id="search-button" class="btn btn-primary">Applica filtri</button>
            </div>
        </div>



        <ul id="news-list" class="news-grid"></ul>
    </div>

    <div class="card hidden" id="add-form">
        <div class="card-header">
            <h2 class="card-title">Aggiungi nuova presidente</h2>
        </div>

        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" placeholder="Nome del presidente">
        </div>

        <div class="form-group">
            <label for="cognome">Cognome</label>
            <input type="text" id="cognome" placeholder="Cognome del presidente">
        </div>

        <div class="btn-group">
            <button id="submit" class="btn btn-primary">Inserisci presidente</button>
            <button id="cancel-form" class="btn btn-outline">Annulla</button>
        </div>
    </div>
</div>

<script>
    const newsList = document.getElementById('news-list');
    const filterCompetizione = document.getElementById('filter-competizione');
    const filterLimit = document.getElementById('filter-news');
    const searchInput = document.getElementById('search-news');
    const toggleAddForm = document.getElementById('toggle-add-form');
    const addForm = document.getElementById('add-form');
    const cancelForm = document.getElementById('cancel-form');
    const cardAll = document.querySelector('.card-all');

    toggleAddForm.addEventListener('click', () => {
        addForm.classList.toggle('hidden');
        cardAll.classList.add('hidden');
        toggleAddForm.classList.toggle('hidden');
        window.scrollTo({
            top: addForm.offsetTop - 20,
            behavior: 'smooth'
        });
    });

    cancelForm.addEventListener('click', (e) => {
        e.preventDefault();
        addForm.classList.add('hidden');
        cardAll.classList.remove('hidden');
        toggleAddForm.classList.remove('hidden');
    });



    function caricaPresidenti() {
        const limit = filterLimit.value;
        const search = searchInput.value.trim();

        let url = `https://barrettasalvatore.it/endpoint/presidenti/read.php?limit=${encodeURIComponent(limit)}`;
        if(search) {
            url += `&search=${encodeURIComponent(search)}`;
        }

        console.log('URL:', url);
        // Mostra lo stato di caricamento
        newsList.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">Caricamento presidenti...</div>';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.presidenti && Array.isArray(data.presidenti)) {
                    newsList.innerHTML = ''; // Pulisce la lista

                    if (data.presidenti.length === 0) {
                        newsList.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">Nessuna presidente trovato</div>';
                        return;
                    }

                    data.presidenti.forEach(p => {

                        const li = document.createElement('li');
                        li.classList.add('news-item');

                        li.innerHTML = `
                                    <div class="news-meta">
                                        <span>ID: ${p.id}</span>
                                        <span>Nome: ${p.nome}</span>
                                        <span>Cognome: ${p.cognome}</span>
                                    </div>
                                    <div class="news-actions">
                                        <button class="btn btn-warning edit-btn" onclick="editPresidente(${p.id})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 6px;">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                            </svg>
                                            Modifica
                                        </button>
                                        <button class="btn btn-danger" onclick="deletePresidenti(${p.id})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 6px;">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                            </svg>
                                            Elimina
                                        </button>
                                    </div>
                                `;
                        newsList.appendChild(li);

                    });
                } else {
                    newsList.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">Nessuna presidente trovata</div>';
                }
            })
            .catch(error => {
                console.error('Errore nel caricamento dei presidenti:', error);
                newsList.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--danger);">Errore nel caricamento dei presidenti</div>';
            });
    }


    function deletePresidenti(id) {
        if (confirm('Sei sicuro di voler eliminare questo Presidente?')) {
            fetch(`https://barrettasalvatore.it/endpoint/presidenti/delete.php?id=${id}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        caricaPresidenti();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Errore nella cancellazione dei presidenti:', error);
                    alert('Errore nella comunicazione con il server');
                });
        }
    }


    document.getElementById('search-button').addEventListener('click', caricaPresidenti);
    filterLimit.addEventListener('change', caricaPresidenti);

    document.getElementById("submit").addEventListener("click", function (e) {
        e.preventDefault();
        const nome = document.getElementById("nome").value.trim();
        const cognome = document.getElementById("cognome").value.trim();

        if (!nome || !cognome) {
            alert("Compila tutti i campi obbligatori.");
            return;
        }

        fetch('https://barrettasalvatore.it/endpoint/presidenti/create.php', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ nome, cognome})
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Presidente inserito con successo!");
                    document.getElementById("nome").value = "";
                    document.getElementById("cognome").value = "";
                    addForm.classList.add('hidden');
                    cardAll.classList.remove('hidden');
                    toggleAddForm.classList.remove('hidden');
                    caricaPresidenti();
                } else {
                    alert("Errore: " + data.message);
                }
            });
    });

    // Inizializzazione
    caricaPresidenti();
</script>
</body>
</html>