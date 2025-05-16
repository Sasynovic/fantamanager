const hamburgerMenu = document.getElementById('hamburger-menu');
const menu = document.querySelector('.main-menu');
const content = document.querySelector('.main-content');

hamburgerMenu.addEventListener('click', () => {
    const isActive = menu.classList.toggle('active');
    content.classList.toggle('active');
    hamburgerMenu.innerHTML = isActive ? '×' : '≡';
});