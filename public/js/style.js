const handleWindowSize = () => {
    const w = document.documentElement.clientWidth;
    const h = document.documentElement.clientHeight;
    const profileStyles = document.querySelector('#profileStyle');

    if (w >= h) {
        profileStyles.href = '/css/profile.css';
    } else {
        profileStyles.href = '/css/verticalProfile.css';
    }
}

document.addEventListener('DOMContentLoaded', handleWindowSize);
window.addEventListener('resize', handleWindowSize);
