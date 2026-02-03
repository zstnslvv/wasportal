<?php
require __DIR__ . '/auth.php';

$settingsDir = __DIR__ . '/data';
$uploadsDir = __DIR__ . '/uploads';
$settingsPath = $settingsDir . '/settings.json';

if (!is_dir($settingsDir)) {
    mkdir($settingsDir, 0755, true);
}
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

$settings = [
    'portalTitle' => 'WAS Portal',
    'portalLogo' => null,
];

if (file_exists($settingsPath)) {
    $stored = json_decode(file_get_contents($settingsPath), true);
    if (is_array($stored)) {
        $settings = array_merge($settings, $stored);
    }
}

$titleMessage = '';
$logoMessage = '';
$titleError = '';
$logoError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'title') {
        $portalTitle = trim($_POST['portalTitle'] ?? '');
        $length = mb_strlen($portalTitle, 'UTF-8');
        if ($length < 3 || $length > 40) {
            $titleError = 'Длина названия: от 3 до 40 символов.';
        } elseif (!preg_match('/^[\\p{L}\\p{N}\\s_-]+$/u', $portalTitle)) {
            $titleError = 'Разрешены только буквы, цифры, пробелы, дефис и подчёркивание.';
        } else {
            $settings['portalTitle'] = $portalTitle;
            file_put_contents($settingsPath, json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $titleMessage = 'Название обновлено.';
        }
    }

    if ($action === 'logo') {
        if (!isset($_FILES['portalLogo']) || $_FILES['portalLogo']['error'] !== UPLOAD_ERR_OK) {
            $logoError = 'Загрузите файл логотипа.';
        } else {
            $file = $_FILES['portalLogo'];
            $allowed = ['png', 'jpg', 'jpeg', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $maxSize = 1024 * 1024;

            $imageInfo = getimagesize($file['tmp_name']);
            if (!$imageInfo) {
                $logoError = 'Файл должен быть изображением.';
            } elseif (!in_array($ext, $allowed, true)) {
                $logoError = 'Разрешены форматы: png, jpg, jpeg, webp.';
            } elseif ($file['size'] > $maxSize) {
                $logoError = 'Максимальный размер логотипа: 1MB.';
            } elseif ($imageInfo[0] > 512 || $imageInfo[1] > 512) {
                $logoError = 'Максимальные размеры: 512x512.';
            } else {
                if (!empty($settings['portalLogo'])) {
                    $oldPath = __DIR__ . $settings['portalLogo'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $fileName = 'logo-' . time() . '.' . $ext;
                $targetPath = $uploadsDir . '/' . $fileName;
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $settings['portalLogo'] = '/uploads/' . $fileName;
                    file_put_contents($settingsPath, json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                    $logoMessage = 'Логотип обновлен.';
                } else {
                    $logoError = 'Не удалось сохранить файл.';
                }
            }
        }
    }
}

$pageTitle = 'Настройки';
$activePage = 'settings';
require __DIR__ . '/partials/layout-start.php';
?>
<section class="grid">
    <div class="panel">
        <h2>Смена названия</h2>
        <p class="panel-note">Длина 3–40 символов, без спецсимволов.</p>
        <form class="settings-form" method="post" action="/settings.php">
            <input type="hidden" name="action" value="title">
            <label>
                <span>Название портала</span>
                <input type="text" name="portalTitle" value="<?php echo htmlspecialchars($settings['portalTitle'], ENT_QUOTES); ?>" maxlength="40" required>
            </label>
            <?php if ($titleError): ?>
                <div class="form-error"><?php echo htmlspecialchars($titleError, ENT_QUOTES); ?></div>
            <?php elseif ($titleMessage): ?>
                <div class="form-success"><?php echo htmlspecialchars($titleMessage, ENT_QUOTES); ?></div>
            <?php endif; ?>
            <button type="submit" class="primary-btn">сохранить</button>
        </form>
    </div>
    <div class="panel">
        <h2>Загрузка логотипа ресурса</h2>
        <p class="panel-note">Форматы: png/jpg/webp, до 1MB, максимум 512x512.</p>
        <form class="settings-form" method="post" action="/settings.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="logo">
            <label>
                <span>Логотип</span>
                <input type="file" name="portalLogo" accept=".png,.jpg,.jpeg,.webp" required>
            </label>
            <?php if (!empty($settings['portalLogo'])): ?>
                <div class="logo-preview">
                    <img src="<?php echo htmlspecialchars($settings['portalLogo'], ENT_QUOTES); ?>" alt="логотип">
                </div>
            <?php endif; ?>
            <?php if ($logoError): ?>
                <div class="form-error"><?php echo htmlspecialchars($logoError, ENT_QUOTES); ?></div>
            <?php elseif ($logoMessage): ?>
                <div class="form-success"><?php echo htmlspecialchars($logoMessage, ENT_QUOTES); ?></div>
            <?php endif; ?>
            <button type="submit" class="primary-btn">загрузить</button>
        </form>
    </div>
    <div class="panel">
        <h2>Валидатор названия</h2>
        <ul class="rules-list">
            <li>Длина: от 3 до 40 символов.</li>
            <li>Допустимы буквы, цифры, пробелы, дефис, подчёркивание.</li>
            <li>Спецсимволы запрещены.</li>
        </ul>
    </div>
    <div class="panel">
        <h2>Валидатор логотипа</h2>
        <ul class="rules-list">
            <li>Форматы: PNG, JPG, JPEG, WEBP.</li>
            <li>Размер файла: до 1MB.</li>
            <li>Максимальное разрешение: 512x512.</li>
            <li>При обновлении старый логотип удаляется.</li>
        </ul>
    </div>
</section>
<?php require __DIR__ . '/partials/layout-end.php'; ?>
