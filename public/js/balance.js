document.addEventListener('DOMContentLoaded', () => {
    const balanceElement = document.querySelector('div .balance h3');
    const balance = parseInt(balanceElement.textContent.split(':')[1].trim());
    if (balance <= 0) {
        balanceElement.parentElement.classList.add('blink');
    } else {
        balanceElement.parentElement.classList.remove('blink');
    }
})
