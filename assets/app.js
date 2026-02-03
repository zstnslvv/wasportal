const root = document.documentElement;
const body = document.body;
const storedAccent = localStorage.getItem('accentColor');
const storedTitle = localStorage.getItem('portalTitle');
const storedAvatar = localStorage.getItem('portalAvatar');
const storedLogo = localStorage.getItem('portalLogo') || storedAvatar;
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
if (avatarTarget && storedLogo) {
    avatarTarget.innerHTML = '';
    const img = document.createElement('img');
    img.src = storedLogo;
    img.alt = 'logo';
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

    if (titleInput && storedTitle) {
        titleInput.value = storedTitle;
    }

    profileForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const nextTitle = titleInput ? titleInput.value.trim() : '';

        if (nextTitle) {
            localStorage.setItem('portalTitle', nextTitle);
            if (titleTarget) {
                titleTarget.textContent = nextTitle;
            }
        }
    });
}

const logoUploader = document.querySelector('[data-logo-uploader]');
if (logoUploader) {
    const input = logoUploader.querySelector('[data-logo-input]');
    const preview = logoUploader.querySelector('[data-logo-preview] img');
    const feedback = logoUploader.querySelector('[data-logo-feedback]');
    const saveButton = logoUploader.querySelector('[data-logo-save]');
    const resetButton = logoUploader.querySelector('[data-logo-reset]');
    let cropper = null;
    let currentObjectUrl = null;

    const showFeedback = (message, isError = false) => {
        if (!feedback) {
            return;
        }
        feedback.textContent = message;
        feedback.classList.toggle('form-feedback--error', isError);
        feedback.classList.toggle('form-feedback--success', !isError);
    };

    const clearFeedback = () => {
        if (feedback) {
            feedback.textContent = '';
            feedback.classList.remove('form-feedback--error', 'form-feedback--success');
        }
    };

    const destroyCropper = () => {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    };

    const setAvatar = (url) => {
        if (!avatarTarget) {
            return;
        }
        avatarTarget.innerHTML = '';
        const img = document.createElement('img');
        img.src = url;
        img.alt = 'logo';
        avatarTarget.appendChild(img);
    };

    if (storedLogo && preview) {
        preview.src = storedLogo;
    }

    input?.addEventListener('change', () => {
        clearFeedback();
        const file = input.files?.[0];
        if (!file) {
            return;
        }
        if (!['image/png', 'image/jpeg'].includes(file.type)) {
            showFeedback('Загрузите PNG или JPG файл.', true);
            input.value = '';
            return;
        }
        if (currentObjectUrl) {
            URL.revokeObjectURL(currentObjectUrl);
        }
        currentObjectUrl = URL.createObjectURL(file);
        if (preview) {
            preview.src = currentObjectUrl;
        }
        destroyCropper();
        cropper = new Cropper(preview, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
        });
    });

    saveButton?.addEventListener('click', async () => {
        clearFeedback();
        if (!cropper) {
            showFeedback('Сначала выберите изображение.', true);
            return;
        }
        const canvas = cropper.getCroppedCanvas({ width: 256, height: 256 });
        if (!canvas) {
            showFeedback('Не удалось подготовить изображение.', true);
            return;
        }
        canvas.toBlob(async (blob) => {
            if (!blob) {
                showFeedback('Не удалось подготовить изображение.', true);
                return;
            }
            const formData = new FormData();
            formData.append('logo', blob, 'logo.png');
            try {
                const response = await fetch('/upload-logo.php', {
                    method: 'POST',
                    body: formData,
                });
                if (!response.ok) {
                    throw new Error('upload failed');
                }
                const data = await response.json();
                if (!data?.ok || !data?.url) {
                    throw new Error('invalid response');
                }
                localStorage.setItem('portalLogo', data.url);
                setAvatar(data.url);
                if (preview) {
                    preview.src = data.url;
                }
                showFeedback('Логотип обновлён.');
            } catch (error) {
                showFeedback('Не удалось загрузить логотип.', true);
            }
        }, 'image/png');
    });

    resetButton?.addEventListener('click', () => {
        clearFeedback();
        input.value = '';
        destroyCropper();
        if (preview) {
            preview.src = '/assets/logo-placeholder.svg';
        }
        localStorage.removeItem('portalLogo');
        if (storedAvatar) {
            localStorage.removeItem('portalAvatar');
        }
        if (avatarTarget) {
            avatarTarget.innerHTML = '<span>WP</span>';
        }
    });
}

const usersForm = document.querySelector('[data-users-form]');
if (usersForm) {
    const feedback = usersForm.querySelector('[data-users-feedback]');
    const loginInput = usersForm.querySelector('input[name="login"]');
    const passwordInput = usersForm.querySelector('input[name="password"]');
    const confirmInput = usersForm.querySelector('input[name="confirmPassword"]');
    const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{16}$/;

    const showUsersFeedback = (message, isError = false) => {
        if (!feedback) {
            return;
        }
        feedback.textContent = message;
        feedback.classList.toggle('form-feedback--error', isError);
        feedback.classList.toggle('form-feedback--success', !isError);
    };

    usersForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const login = loginInput?.value.trim() || '';
        const password = passwordInput?.value || '';
        const confirm = confirmInput?.value || '';

        if (!login) {
            showUsersFeedback('Login обязателен.', true);
            return;
        }
        if (!passwordPattern.test(password)) {
            showUsersFeedback('Пароль должен быть длиной 16 символов и содержать буквы, цифры и спецсимволы.', true);
            return;
        }
        if (password !== confirm) {
            showUsersFeedback('Пароли не совпадают.', true);
            return;
        }
        showUsersFeedback('Пользователь подготовлен к созданию. Назначение ролей будет доступно позже.');
        usersForm.reset();
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
