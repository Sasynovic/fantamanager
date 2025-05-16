const hamburgerMenu = document.getElementById('hamburger-menu');
const menu = document.querySelector('.main-menu');
const content = document.querySelector('.main-content');

// Funzione per aggiornare l'icona in base alla larghezza
function updateHamburger() {
    if (window.innerWidth < 1024) {
        hamburgerMenu.innerHTML = '≡';
        hamburgerMenu.style.display = 'block';
    } else {
        hamburgerMenu.innerHTML = '';
        menu.classList.remove('active');
        content.classList.remove('active');
    }
}

// Inizializza il contenuto all’avvio
updateHamburger();

// Aggiungi evento resize per gestire dinamicamente
window.addEventListener('resize', updateHamburger);

// Gestione click sull’hamburger
hamburgerMenu.addEventListener('click', () => {
    const isActive = menu.classList.toggle('active');
    content.classList.toggle('active');
    hamburgerMenu.innerHTML = isActive ? '×' : '≡';
});
