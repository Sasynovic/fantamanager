/**
 * CRUD Library - Una libreria generale per operazioni CRUD
 * @author Claude
 */

class CRUDManager {
    /**
     * Crea un'istanza del gestore CRUD
     * @param {string} endpoint_type - Il tipo di endpoint (es. 'presidenti', 'notizie', ecc.)
     * @param {string} baseURL - URL base per le API (opzionale)
     * @param {Object} elements - Elementi DOM da utilizzare (opzionale)
     * @param {Object} callbacks - Funzioni di callback per eventi specifici (opzionale)
     */
    constructor(endpoint_type, baseURL = window.location.origin, elements = {}, callbacks = {}) {
        this.endpoint_type = endpoint_type;
        this.baseURL = baseURL;

        // Definizioni API
        this.apiUrl = {
            create: `${baseURL}/endpoint/${endpoint_type}/create.php`,
            read: `${baseURL}/endpoint/${endpoint_type}/read.php`,
            update: `${baseURL}/endpoint/${endpoint_type}/update.php`,
            delete: `${baseURL}/endpoint/${endpoint_type}/delete.php`
        };

        this.apiMethod = {
            create: 'POST',
            read: 'GET',
            update: 'PUT',
            delete: 'DELETE'
        };

        this.apiHeaders = {
            'Content-Type': 'application/json'
        };

        // Elementi DOM
        this.elements = {
            cardList: document.getElementById('item-list'),
            filterLimit: document.getElementById('filter-limit'),
            searchInput: document.getElementById('search-input'),
            searchButton: document.getElementById('search-button'),
            toggleAddForm: document.getElementById('toggle-add-form'),
            addForm: document.getElementById('add-form'),
            editForm: document.getElementById('edit-form'),
            cancelForm: document.getElementById('cancel-form'),
            submitForm: document.getElementById('submit-form'),
            pagination: document.getElementById('pagination'),
            cardAll: document.querySelector('.card-all'),
            ...elements
        };

        // Callbacks personalizzati
        this.callbacks = {
            beforeLoad: null,
            afterLoad: null,
            renderItem: null,
            beforeCreate: null,
            afterCreate: null,
            beforeUpdate: null,
            afterUpdate: null,
            beforeDelete: null,
            afterDelete: null,
            ...callbacks
        };

        // Inizializzazione degli event listeners
        this.initEventListeners();
    }

    /**
     * Inizializza tutti gli event listeners
     */
    initEventListeners() {
        // Search and filter
        if (this.elements.searchButton) {
            this.elements.searchButton.addEventListener('click', () => this.loadData());
        }

        if (this.elements.filterLimit) {
            this.elements.filterLimit.addEventListener('change', () => this.loadData());
        }

        // Form toggle
        if (this.elements.toggleAddForm) {
            this.elements.toggleAddForm.addEventListener('click', () => this.showAddForm());
        }

        if (this.elements.cancelForm) {
            this.elements.cancelForm.addEventListener('click', (e) => {
                e.preventDefault();
                this.hideAddForm();
            });
        }

        // Submit form
        if (this.elements.submitForm) {
            this.elements.submitForm.addEventListener('click', (e) => this.handleFormSubmit(e));
        }
    }

    /**
     * Gestisce l'invio del form
     * @param {Event} e - Evento submit
     */
    handleFormSubmit(e) {
        e.preventDefault();

        // Raccogli tutti gli input dal form
        const formData = {};
        const form = this.elements.addForm;
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            if (input.name) {
                formData[input.name] = input.value.trim();
            }
        });

        // Validazione base
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
            }
        });

        if (!isValid) {
            alert('Compila tutti i campi obbligatori.');
            return;
        }

        // Callback before create
        if (this.callbacks.beforeCreate && typeof this.callbacks.beforeCreate === 'function') {
            const shouldContinue = this.callbacks.beforeCreate(formData);
            if (shouldContinue === false) return;
        }

        // Invia dati
        this.createItem(formData);
    }

    /**
     * Mostra il form di aggiunta
     */
    showAddForm() {
        if (!this.elements.addForm || !this.elements.cardAll || !this.elements.toggleAddForm) return;

        this.elements.addForm.classList.remove('hidden');
        this.elements.cardAll.classList.add('hidden');
        this.elements.toggleAddForm.classList.add('hidden');
        this.elements.pagination.classList.add('hidden');
        this.elements.editForm.classList.add('hidden');

        window.scrollTo({
            top: this.elements.addForm.offsetTop - 20,
            behavior: 'smooth'
        });
    }

    /**
     * Nasconde il form di aggiunta
     */
    hideAddForm() {
        if (!this.elements.addForm || !this.elements.cardAll || !this.elements.toggleAddForm) return;

        this.elements.addForm.classList.add('hidden');
        this.elements.cardAll.classList.remove('hidden');
        this.elements.toggleAddForm.classList.remove('hidden');
        this.elements.pagination.classList.remove('hidden');


    }

    /**
     * Carica i dati dal server
     * @param {Object} additionalParams - Parametri aggiuntivi per la query
     */
    loadData(additionalParams = {}) {
        if (!this.elements.cardList) return;

        // Callback before load
        if (this.callbacks.beforeLoad && typeof this.callbacks.beforeLoad === 'function') {
            const shouldContinue = this.callbacks.beforeLoad();
            if (shouldContinue === false) return;
        }

        // Costruisci parametri query
        const params = new URLSearchParams();

        // Aggiungi limit dalla select se esiste
        if (this.elements.filterLimit) {
            params.append('limit', this.elements.filterLimit.value);
        }

        // Aggiungi search dal campo input se esiste
        if (this.elements.searchInput && this.elements.searchInput.value.trim()) {
            params.append('search', this.elements.searchInput.value.trim());
        }

        // Aggiungi parametri aggiuntivi
        for (const [key, value] of Object.entries(additionalParams)) {
            params.append(key, value);
        }

        // Costruisci URL
        const url = `${this.apiUrl.read}?${params.toString()}`;

        // Mostra stato di caricamento
        this.elements.cardList.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">Caricamento dati...</div>';

        // Fetch data
        fetch(url)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(data => {
                // Convenzione: i dati vengono restituiti in un array con lo stesso nome dell'endpoint_type
                const items = data[this.endpoint_type] || [];

                if (this.updatePagination && typeof this.updatePagination === 'function' && data.pagination) {
                    this.updatePagination(data.pagination);
                }

                this.renderItems(items);

                // Callback after load
                if (this.callbacks.afterLoad && typeof this.callbacks.afterLoad === 'function') {
                    this.callbacks.afterLoad(items);
                }
            })
            .catch(error => {
                console.error(`Errore nel caricamento dei ${this.endpoint_type}:`, error);
                this.elements.cardList.innerHTML = `<div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--danger);">Errore nel caricamento dei dati</div>`;
            });
    }

    /**
     * Renderizza gli elementi nella lista
     * @param {Array} items - Array di elementi da renderizzare
     */
    renderItems(items) {
        if (!this.elements.cardList) return;

        this.elements.cardList.innerHTML = ''; // Pulisce la lista

        if (items.length === 0) {
            this.elements.cardList.innerHTML = `<div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">Nessun ${this.endpoint_type} trovato</div>`;
            return;
        }

        items.forEach(item => {
            const li = document.createElement('li');
            li.classList.add('card-item');

            // Usa il callback personalizzato o il renderer predefinito
            if (this.callbacks.renderItem && typeof this.callbacks.renderItem === 'function') {
                li.innerHTML = this.callbacks.renderItem(item);
            } else {
                // Renderer predefinito generale
                let metaHtml = '<div class="item-meta">';
                for (const [key, value] of Object.entries(item)) {
                    metaHtml += `<span>${key}: ${value}</span>`;
                }
                metaHtml += '</div>';

                li.innerHTML = `
                    ${metaHtml}
                    <div class="item-actions">
                        <button class="btn btn-warning edit-btn" onclick="editItem(${item.id})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 6px;">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                            Modifica
                        </button>
                        <button class="btn btn-danger" onclick="crudManager.deleteItem(${item.id})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 6px;">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                            </svg>
                            Elimina
                        </button>
                    </div>
                `;
            }

            this.elements.cardList.appendChild(li);
        });
    }

    /**
     * Crea un nuovo elemento
     * @param {Object} data - Dati dell'elemento da creare
     */
    createItem(data) {

        fetch(this.apiUrl.create, {
            method: this.apiMethod.create,
            headers: this.apiHeaders,
            body: JSON.stringify(data)
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(response => {
                if (response.success) {
                    alert(`${this.endpoint_type} inserito con successo!`);

                    // Reset del form
                    const form = this.elements.addForm;
                    const inputs = form.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        input.value = '';
                    });

                    this.hideAddForm();
                    this.loadData();

                    // Callback after create
                    if (this.callbacks.afterCreate && typeof this.callbacks.afterCreate === 'function') {
                        this.callbacks.afterCreate(response);
                    }
                } else {
                    alert("Errore: " + response.message);
                }
            })
            .catch(error => {
                console.error(`Errore nella creazione del ${this.endpoint_type}:`, error);
                alert('Errore nella comunicazione con il server');
            });
    }

    /**
     * Carica un elemento per la modifica
     * @param {number} id - ID dell'elemento da modificare
     */
    editItem(id) {
        fetch(`${this.apiUrl.read}?id=${id}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(data => {
                // Assumiamo che il server restituisca un singolo elemento
                const item = data[this.endpoint_type] ? data[this.endpoint_type][0] : null;

                if (!item) {
                    alert(`${this.endpoint_type} non trovato`);
                    return;
                }

                // Callback before update
                if (this.callbacks.beforeUpdate && typeof this.callbacks.beforeUpdate === 'function') {
                    const shouldContinue = this.callbacks.beforeUpdate(item);
                    if (shouldContinue === false) return;
                }

                // Popolamento del form
                const form = this.elements.addForm;
                if (!form) return;

                // Aggiungiamo un campo nascosto per l'ID
                let idField = form.querySelector('input[name="id"]');
                if (!idField) {
                    idField = document.createElement('input');
                    idField.type = 'hidden';
                    idField.name = 'id';
                    form.appendChild(idField);
                }
                idField.value = item.id;

                // Popoliamo tutti gli altri campi
                for (const [key, value] of Object.entries(item)) {
                    if (key === 'id') continue;

                    const field = form.querySelector(`[name="${key}"]`);
                    if (field) {
                        field.value = value;
                    }
                }

                // Modifichiamo il testo del pulsante submit
                const submitBtn = this.elements.submitForm;
                if (submitBtn) {
                    submitBtn.textContent = 'Aggiorna';
                    submitBtn.dataset.mode = 'update';
                }

                this.showAddForm();
            })
            .catch(error => {
                console.error(`Errore nel caricamento del ${this.endpoint_type} per modifica:`, error);
                alert('Errore nella comunicazione con il server');
            });
    }

    /**
     * Aggiorna un elemento esistente
     * @param {Object} data - Dati dell'elemento da aggiornare (deve includere id)
     */
    updateItem(data) {
        fetch(this.apiUrl.update, {
            method: this.apiMethod.update,
            headers: this.apiHeaders,
            body: JSON.stringify(data)
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(response => {
                if (response.success) {
                    alert(`${this.endpoint_type} aggiornato con successo!`);

                    // Reset del form
                    const form = this.elements.addForm;
                    const inputs = form.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        input.value = '';
                    });

                    // Reset del pulsante submit
                    const submitBtn = this.elements.submitForm;
                    if (submitBtn) {
                        submitBtn.textContent = 'Invia';
                        submitBtn.dataset.mode = 'create';
                    }

                    this.hideAddForm();
                    this.loadData();

                    // Callback after update
                    if (this.callbacks.afterUpdate && typeof this.callbacks.afterUpdate === 'function') {
                        this.callbacks.afterUpdate(response);
                    }
                } else {
                    alert("Errore: " + response.message);
                }
            })
            .catch(error => {
                console.error(`Errore nell'aggiornamento del ${this.endpoint_type}:`, error);
                alert('Errore nella comunicazione con il server');
            });
    }

    /**
     * Elimina un elemento
     * @param {number} id - ID dell'elemento da eliminare
     */
    deleteItem(id) {
        // Callback before delete
        if (this.callbacks.beforeDelete && typeof this.callbacks.beforeDelete === 'function') {
            const shouldContinue = this.callbacks.beforeDelete(id);
            if (shouldContinue === false) return;
        }

        if (confirm(`Sei sicuro di voler eliminare questo ${this.endpoint_type}?`)) {
            fetch(`${this.apiUrl.delete}?id=${id}`, {
                method: this.apiMethod.delete
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.json();
                })
                .then(response => {
                    if (response.success) {
                        alert(response.message);
                        this.loadData();

                        // Callback after delete
                        if (this.callbacks.afterDelete && typeof this.callbacks.afterDelete === 'function') {
                            this.callbacks.afterDelete(id);
                        }
                    } else {
                        alert(response.message);
                    }
                })
                .catch(error => {
                    console.error(`Errore nell'eliminazione del ${this.endpoint_type}:`, error);
                    alert('Errore nella comunicazione con il server');
                });
        }
    }

    updatePagination(pagination) {
        const container = this.elements.pagination;
        container.innerHTML = ''; // pulisci

        const { current_page, total_pages } = pagination;

        // Bottone "Precedente"
        const prevButton = document.createElement('button');
        prevButton.textContent = '« Precedente';
        prevButton.disabled = current_page === 1;
        prevButton.classList.add('btn', 'btn-secondary');
        prevButton.addEventListener('click', () => {
            this.loadData({ page: current_page - 1 });
        });
        container.appendChild(prevButton);

        // Info pagina corrente
        const pageInfo = document.createElement('span');
        pageInfo.textContent = ` Pagina ${current_page} di ${total_pages} `;
        pageInfo.style.margin = '0 10px';
        container.appendChild(pageInfo);

        // Bottone "Successivo"
        const nextButton = document.createElement('button');
        nextButton.textContent = 'Successivo »';
        nextButton.disabled = current_page === total_pages;
        nextButton.classList.add('btn', 'btn-secondary');
        nextButton.addEventListener('click', () => {
            this.loadData({ page: current_page + 1 });
        });
        container.appendChild(nextButton);
    }


}