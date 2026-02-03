const root = document.documentElement;
const storedAccent = localStorage.getItem('accentColor');

if (storedAccent) {
    root.style.setProperty('--accent', storedAccent);
    root.style.setProperty('--accent-soft', `${storedAccent}33`);
}

const themePicker = document.querySelector('[data-theme-picker]');
if (themePicker) {
    themePicker.querySelectorAll('.theme-swatch').forEach((swatch) => {
        const color = swatch.dataset.accent;
        swatch.style.background = color;

        if (color === storedAccent) {
            swatch.classList.add('is-active');
        }

        swatch.addEventListener('click', () => {
            root.style.setProperty('--accent', color);
            root.style.setProperty('--accent-soft', `${color}33`);
            localStorage.setItem('accentColor', color);
            themePicker.querySelectorAll('.theme-swatch').forEach((item) => item.classList.remove('is-active'));
            swatch.classList.add('is-active');
        });
    });
}

document.querySelectorAll('.nav-link').forEach((link) => {
    link.addEventListener('click', () => {
        document.querySelectorAll('.nav-link').forEach((item) => item.classList.remove('is-clicked'));
        link.classList.add('is-clicked');
    });
});

document.querySelectorAll('.icon-button').forEach((button) => {
    button.addEventListener('click', () => {
        document.querySelectorAll('.icon-button').forEach((item) => item.classList.remove('is-active'));
        button.classList.add('is-active');
    });
});

document.querySelectorAll('[data-toggle]').forEach((toggle) => {
    toggle.addEventListener('click', () => {
        const key = toggle.dataset.toggle;
        const submenu = document.querySelector(`[data-submenu="${key}"]`);
        if (submenu) {
            submenu.classList.toggle('is-open');
        }
    });
});
