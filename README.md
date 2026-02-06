# WAS Portal

## Запуск локально

1. Убедитесь, что установлен PHP 8+.
2. В корне проекта запустите встроенный сервер:

```bash
php -S 0.0.0.0:8000 -t .
```

3. Откройте в браузере:

```
http://localhost:8000/login.php
```

## Запуск через Docker Compose

1. Установите зависимости backend (нужно для корректной работы API):

```bash
./backend/bin/install-deps.sh
```

2. Соберите и запустите сервис:

```bash
docker compose up -d --build
```

3. Откройте в браузере:

```
http://localhost:8000/login.php
```

## Доступ

- Логин: `admin`
- Пароль: `admin`

## Примечания

- Все внутренние страницы защищены и требуют аутентификации.
- Для выхода используйте ссылку «выйти» в левом меню.

---

# Self-hosted конвертация файлов (Docker)

## Запуск

```bash
docker compose up --build
```

## URL сервиса

```
http://localhost:8080/convert.php
```

## API примеры

### Upload

```bash
curl -F "file=@sample.docx" http://localhost:8080/api/upload
```

### Convert

```bash
curl -X POST http://localhost:8080/api/convert \
  -H "Content-Type: application/json" \
  -d '{"file_id":"<file_id>","target_format":"pdf"}'
```

### Job status

```bash
curl http://localhost:8080/api/job/<job_id>
```

### Download result

```bash
curl -o result.pdf http://localhost:8080/api/download/<job_id>
```

## Где лежат файлы

- Загрузки: `backend/storage/uploads/<file_id>/`
- Результаты: `backend/storage/outputs/<job_id>/result.<ext>`

## Как добавить новые конвертации

1. Добавьте расширения в `backend/src/Uploads.php` в `conversionMapping()`.
2. Реализуйте обработку в `worker/worker.php` в `handleConvert()`.
3. Обновите UI в `frontend/assets/app.js` (mapping форматов).
