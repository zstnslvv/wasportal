const root = document.documentElement;
const body = document.body;
const storedAccent = localStorage.getItem('accentColor');
const storedContrast = localStorage.getItem('themeContrast') === 'true';
const storedCompact = localStorage.getItem('themeCompact') === 'true';

if (storedAccent) {
    root.style.setProperty('--accent', storedAccent);
    root.style.setProperty('--accent-soft', `${storedAccent}33`);
}

if (storedContrast) {
    body.classList.add('theme-contrast');
}

if (storedCompact) {
    body.classList.add('theme-compact');
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

document.querySelectorAll('[data-toggle-theme]').forEach((button) => {
    const mode = button.dataset.toggleTheme;
    const isActive = (mode === 'contrast' && storedContrast) || (mode === 'compact' && storedCompact);
    if (isActive) {
        button.classList.add('is-active');
    }

    button.addEventListener('click', () => {
        if (mode === 'contrast') {
            const next = !body.classList.contains('theme-contrast');
            body.classList.toggle('theme-contrast', next);
            localStorage.setItem('themeContrast', next);
            button.classList.toggle('is-active', next);
        }
        if (mode === 'compact') {
            const next = !body.classList.contains('theme-compact');
            body.classList.toggle('theme-compact', next);
            localStorage.setItem('themeCompact', next);
            button.classList.toggle('is-active', next);
        }
    });
});

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
