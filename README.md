# Yii1 Books Catalog (Docker-first)

Реализация тестового задания: каталог книг и авторов на Yii1 + MySQL с запуском через Docker, подписками гостей, SMS-уведомлениями и отчетом TOP-10.

## Стек

- PHP 8.1 + Apache
- Yii1 (`yiisoft/yii` 1.1.31)
- MySQL 8.0
- Adminer (опционально)
- GuzzleHTTP (`guzzlehttp/guzzle`) для внешних HTTP-запросов (SMS API)

## Быстрый старт

1. Скопируйте env-файл (опционально):

```bash
cp .env.example .env
```

2. Запустите проект:

```bash
docker compose up --build
```

3. Откройте:

- Приложение: [http://localhost:8080](http://localhost:8080)
- Adminer: [http://localhost:8081](http://localhost:8081)

Миграции применяются автоматически при старте контейнера через `php yiic migrate --interactive=0`.

## Тестовые учетные данные

- Логин: `user`
- Пароль: `user12345`

Страница входа: `/site/login`

## Реализованный функционал

1. CRUD для книг и авторов.
2. Возможности гостя:

- просмотр книг и авторов
- подписка на автора по номеру телефона
- получение SMS при добавлении новых книг выбранного автора

3. Возможности авторизованного пользователя:

- полный CRUD для книг и авторов

4. Отчет для всех:

- `/report/top-authors?year=YYYY` (TOP-10 авторов по количеству книг за выбранный год)

5. Контроль доступа через `accessRules()` в контроллерах.
6. CSRF включен для POST-запросов.
7. Проверка паролей через `password_hash`/`password_verify`.

## Переменные окружения

- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`
- `SMSPILOT_API_KEY`, `SMSPILOT_SENDER`
- `APP_ENV`, `YII_DEBUG`

`SMSPILOT_SENDER` — имя отправителя (`from` в SMSPilot). Параметр опциональный, можно оставить пустым.

После изменения `.env` перезапустите `app` контейнер, чтобы переменные перечитались:

```bash
docker compose up -d --force-recreate app
```

Если SMSPilot возвращает `code=223` (antispam), это означает, что запрос дошел до API, но отклонен политикой SMSPilot/аккаунта.

## Схема базы данных

Таблицы:

- `users`
- `authors`
- `books`
- `book_author`
- `author_subscriptions`

Ограничения/индексы:

- `books.isbn` unique
- `books.published_year` index
- `book_author (book_id, author_id)` unique (составной PK)
- `author_subscriptions (author_id, phone)` unique
- FK с каскадными действиями там, где это уместно

## Полезные команды

```bash
# Повторно применить миграции вручную
docker compose exec app php yiic migrate --interactive=0

# Логи приложения
docker compose logs -f app

# Запуск тестов (вариант 1)
docker compose exec app vendor/bin/phpunit --configuration phpunit.xml

# Запуск тестов (вариант 2)
docker compose exec app composer test
```

## Сид-данные для отчета TOP-10

Чтобы можно было сразу проверить отчет `/report/top-authors`, в миграциях добавлен сид за 2026 год:

- миграция: `protected/migrations/m260224_000003_seed_report_2026.php`
- данные: 12 авторов и набор книг за 2026 год с разным количеством книг на автора

Как проверить:

1. Примените миграции:

```bash
docker compose exec app php yiic migrate --interactive=0
```

2. Откройте отчет:

- [http://localhost:8080/report/top-authors?year=2026](http://localhost:8080/report/top-authors?year=2026)

Если раньше база уже была поднята и вы хотите пересоздать ее полностью с нуля:

```bash
docker compose down -v
docker compose up --build
```

## Тесты

В проект добавлены интеграционные тесты (PHPUnit):

- `tests/BookServiceTest.php` — создание/обновление книги с авторами, проверка связей и вызова нотификатора
- `tests/ReportServiceTest.php` — корректность сортировки, лимита и фильтрации отчета по году
- `tests/AuthorSubscriptionTest.php` — валидация телефона и защита от дублей подписок на уровне БД
