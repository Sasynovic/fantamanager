<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // reindirizza se non loggato
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Notizie</title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
            <div class="menu">
                <a href="dashboard_admin.php">Dashboard</a>
                <a href="gestione_news.php">Gestione Notizie</a>
                <a href="logout.php">Logout</a>
            </div>
<div class="app-container">
    <div class="header">
        <h1>Gestione Notizie</h1>
        <button id="toggle-add-form" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Nuova Notizia
        </button>
    </div>

    <p class="subtitle">Gestisci le notizie pubblicate sul tuo sito web</p>

    <div class="card-all">
        <div class="filter-section">
            <div class="form-group">
                <label for="filter-competizione">Competizione</label>
                <select id="filter-competizione">
                    <option value="">Tutte</option>
                </select>
            </div>

            <div class="form-group">
                <label for="filter-news">Notizie da mostrare</label>
                <select id="filter-news">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
            </div>

            <div class="form-group">
                <label for="search-news">Cerca</label>
                <input type="text" id="search-news" placeholder="Cerca notizie...">
            </div>

            <div class="form-group" style="align-self: flex-end;">
                <button id="search-button" class="btn btn-primary">Applica filtri</button>
            </div>
        </div>



        <ul id="news-list" class="news-grid"></ul>
    </div>

    <div class="card hidden" id="add-form">
        <div class="card-header">
            <h2 class="card-title">Aggiungi nuova notizia</h2>
        </div>

        <div class="form-group">
            <label for="title">Titolo</label>
            <input type="text" id="title" placeholder="Inserisci il titolo della notizia">
        </div>

        <div class="form-group">
            <label for="author">Autore</label>
            <input type="text" id="author" placeholder="Nome dell'autore">
        </div>

        <div class="checkbox-label">
            <span>Visibile</span>
            <label class="toggle-switch">
                <input type="checkbox" id="visibile" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group">
            <label for="id_competizione">Competizione</label>
            <select id="id_competizione"></select>
        </div>

        <div class="form-group">
            <label for="editor-container">Contenuto</label>
            <div id="editor-container"></div>
        </div>

        <div class="btn-group">
            <button id="submit" class="btn btn-primary">📰Pubblica</button>
            <button id="cancel-form" class="btn btn-outline">Annulla</button>
        </div>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    const quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['link'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                ['blockquote', 'code-block']
            ]
        },
        placeholder: 'Scrivi il contenuto della notizia qui...'
    });

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

    function caricaCompetizioni() {
        fetch('https://barrettasalvatore.it/endpoint/competizione/read.php')
            .then(res => res.json())
            .then(data => {
                data.competizioni.forEach(c => {
                    const opt1 = new Option(c.nome_competizione, c.id);
                    const opt2 = new Option(c.nome_competizione, c.id);
                    filterCompetizione.appendChild(opt1);
                    document.getElementById('id_competizione').appendChild(opt2);
                });
            });
    }

    function caricaNotizie() {
        const competizione = filterCompetizione.value;
        const limit = filterLimit.value;
        const search = searchInput.value.trim();

        let url = `https://barrettasalvatore.it/endpoint/news/read.php?limit=${limit}`;
        if (competizione) {
            url += `&id_competizione=${competizione}`;
        }
        if(search) {
            url += `&search=${encodeURIComponent(search)}`;
        }

        console.log('URL:', url);
        // Mostra lo stato di caricamento
        newsList.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">Caricamento notizie...</div>';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.news && Array.isArray(data.news)) {
                    newsList.innerHTML = ''; // Pulisce la lista

                    if (data.news.length === 0) {
                        newsList.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">Nessuna notizia trovata</div>';
                        return;
                    }

                    data.news.forEach(n => {

                            const li = document.createElement('li');
                            li.classList.add('news-item');

                            const content = new DOMParser().parseFromString(n.contenuto, 'text/html').body.textContent || '';
                            const shortContent = content.length > 150 ? content.substring(0, 150) + '...' : content;

                            li.innerHTML = `
                                    <h3>${n.titolo}</h3>
                                    <div class="news-content">${shortContent}</div>
                                    <div class="news-meta">
                                        <span>Autore: ${n.autore || 'Sconosciuto'}</span>
                                        <span>Data: ${formatDate(n.data_pubblicazione)}</span>
                                    </div>
                                    <div class="news-actions">
                                        <button class="btn btn-warning edit-btn" onclick="editNews(${n.id})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 6px;">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                            </svg>
                                            Modifica
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteNews(${n.id})">
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
                    newsList.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">Nessuna notizia trovata</div>';
                }
            })
            .catch(error => {
                console.error('Errore nel caricamento delle notizie:', error);
                newsList.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--danger);">Errore nel caricamento delle notizie</div>';
            });
    }

    function editNews(id) {
        // Aggiungi la logica per modificare una notizia
        alert('Modifica la notizia con ID ' + id);
    }

    function deleteNews(id) {
        if (confirm('Sei sicuro di voler eliminare questa notizia?')) {
            fetch(`https://barrettasalvatore.it/endpoint/news/delete.php?id=${id}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        caricaNotizie();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Errore nella cancellazione della notizia:', error);
                    alert('Errore nella comunicazione con il server');
                });
        }
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('it-IT', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    document.getElementById('search-button').addEventListener('click', caricaNotizie);
    filterCompetizione.addEventListener('change', caricaNotizie);
    filterLimit.addEventListener('change', caricaNotizie);

    document.getElementById("submit").addEventListener("click", function (e) {
        e.preventDefault();
        const titolo = document.getElementById("title").value.trim();
        const autore = document.getElementById("author").value.trim();
        const visibile = document.getElementById("visibile").checked ? 1 : 0;
        const id_competizione = document.getElementById("id_competizione").value;
        const contenuto = quill.root.innerHTML.trim();

        if (!titolo || !autore || !contenuto || !id_competizione) {
            alert("Compila tutti i campi obbligatori.");
            return;
        }

        fetch('https://barrettasalvatore.it/endpoint/news/create.php', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ titolo, autore, contenuto, visibile, id_competizione })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Notizia pubblicata con successo!");
                    document.getElementById("title").value = "";
                    document.getElementById("author").value = "";
                    quill.setContents([]);
                    addForm.classList.add('hidden');
                    cardAll.classList.remove('hidden');
                    toggleAddForm.classList.remove('hidden');
                    caricaNotizie();

                } else {
                    alert("Errore: " + data.message);
                }
            });
    });

    // Inizializzazione
    caricaCompetizioni();
    caricaNotizie();
</script>
</body>
</html>