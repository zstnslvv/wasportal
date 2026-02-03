const root = document.documentElement;
const body = document.body;
const storedAccent = localStorage.getItem('accentColor');
const storedTitle = localStorage.getItem('portalTitle');
const storedAvatar = localStorage.getItem('portalAvatar');
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

const titleTarget = document.querySelector('[data-title]');
if (titleTarget && storedTitle) {
    titleTarget.textContent = storedTitle;
}

const avatarTarget = document.querySelector('[data-avatar]');
if (avatarTarget && storedAvatar) {
    avatarTarget.innerHTML = '';
    const img = document.createElement('img');
    img.src = storedAvatar;
    img.alt = 'avatar';
    avatarTarget.appendChild(img);
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

const profileForm = document.querySelector('[data-profile-form]');
if (profileForm) {
    const titleInput = profileForm.querySelector('input[name="portalTitle"]');
    const avatarInput = profileForm.querySelector('input[name="portalAvatar"]');

    if (titleInput && storedTitle) {
        titleInput.value = storedTitle;
    }
    if (avatarInput && storedAvatar) {
        avatarInput.value = storedAvatar;
    }

    profileForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const nextTitle = titleInput ? titleInput.value.trim() : '';
        const nextAvatar = avatarInput ? avatarInput.value.trim() : '';

        if (nextTitle) {
            localStorage.setItem('portalTitle', nextTitle);
            if (titleTarget) {
                titleTarget.textContent = nextTitle;
            }
        }

        if (nextAvatar) {
            localStorage.setItem('portalAvatar', nextAvatar);
            if (avatarTarget) {
                avatarTarget.innerHTML = '';
                const img = document.createElement('img');
                img.src = nextAvatar;
                img.alt = 'avatar';
                avatarTarget.appendChild(img);
            }
        }
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
