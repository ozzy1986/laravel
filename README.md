# Планировщик задач (Laravel)

Веб-приложение «список задач» на русском языке: CRUD, статусы, поиск по названию, фильтр по статусу, пагинация. Интерфейс — Blade + встроенные стили в layout, без отдельного SPA.

## Стек

- **PHP** ≥ 8.3, **Laravel** 13
- **БД:** MySQL в проде; локально и в тестах обычно SQLite (`phpunit.xml` задаёт `DB_DATABASE=:memory:`)
- **Тесты:** PHPUnit (`tests/Feature/TaskCrudTest.php` и др.)

## Карта проекта (что за что отвечает)

| Путь | Назначение |
|------|------------|
| [`routes/web.php`](routes/web.php) | Маршруты: редирект `/` → `/tasks`, ресурс `tasks` (все действия CRUD). |
| [`app/Http/Controllers/TaskController.php`](app/Http/Controllers/TaskController.php) | Список (фильтр, поиск, пагинация 6 на страницу), создание/просмотр/редактирование/удаление. Для AJAX-запросов к индексу (`X-Requested-With: XMLHttpRequest`, `Accept: application/json`) возвращает JSON `{ html, total }` — HTML фрагмента списка без полной перезагрузки. |
| [`app/Http/Requests/TaskRequest.php`](app/Http/Requests/TaskRequest.php) | Общая валидация для `store` и `update` (title, description, status). |
| [`app/Models/Task.php`](app/Models/Task.php) | Модель задачи; скоупы `filterStatus`, `search` (по названию); метод `excerpt()` для превью в списке. |
| [`app/Enums/TaskStatus.php`](app/Enums/TaskStatus.php) | Статусы: `new`, `in_progress`, `done` — подписи для UI, классы чипов, класс цветной полоски карточки. |
| [`database/migrations/`](database/migrations) | Таблица `tasks` и стандартные миграции Laravel. |
| [`database/factories/TaskFactory.php`](database/factories/TaskFactory.php) | Фабрика для тестов и сидов. |
| [`resources/views/layouts/app.blade.php`](resources/views/layouts/app.blade.php) | Общий layout: шапка, стили, футер, стек `@stack('scripts')`. |
| [`resources/views/tasks/`](resources/views/tasks) | Страницы: `index`, `create`, `edit`, `show`, частичные `_form`, `_results` (только тело списка + пагинация для AJAX). |
| [`public/favicon.ico`](public/favicon.ico) и [`public/favicon-chaos-v3.png`](public/favicon-chaos-v3.png) | Актуальные иконки сайта; [`public/logo-chaos-v2.png`](public/logo-chaos-v2.png) используется в шапке. |
| [`tests/Feature/TaskCrudTest.php`](tests/Feature/TaskCrudTest.php) | Основные сценарии: CRUD, валидация, фильтр, поиск по названию, AJAX-индекс, пагинация. |

Пользователи Laravel по умолчанию (`User`) в миграциях есть, в этом приложении **не используются** (нет авторизации).

## Локальный запуск

1. `composer install`
2. Скопировать окружение: `cp .env.example .env` (или вручную), затем `php artisan key:generate`
3. Настроить `DB_*` в `.env` (для SQLite: `DB_CONNECTION=sqlite`, путь к файлу в `config/database.php` по умолчанию `database/database.sqlite`, создать файл: `touch database/database.sqlite`)
4. `php artisan migrate`
5. `php artisan serve` — приложение по адресу из вывода команды

Node/Vite для этого проекта не нужны: основной UI полностью работает на Blade, встроенных стилях и небольшом inline-JS.

## Тесты

```bash
php artisan test
```

В `phpunit.xml` для тестов заданы `APP_ENV=testing`, SQLite in-memory и отключены лишние сервисы — **не полагайтесь на продовый `.env` в тестах**.

## Переменные окружения

- **Не коммитьте** `.env` (в `.gitignore`).
- На проде: свой `APP_KEY`, `APP_URL`, настройки БД, при необходимости `APP_DEBUG=false`.
- После деплоя кода обычно: `php artisan migrate --force`, затем `php artisan config:cache`, `route:cache`, `view:cache` (и `optimize:clear` при смене конфига).

## Поведение списка задач (индекс)

- Обычный GET `/tasks` — полная страница.
- Запрос с заголовками `X-Requested-With: XMLHttpRequest` и `Accept: application/json` — ответ JSON с полем `html` (рендер `tasks._results`) и `total` (число записей с учётом фильтров) для обновления списка без перезагрузки. JS лежит в [`resources/views/tasks/index.blade.php`](resources/views/tasks/index.blade.php) в `@push('scripts')`.

## Расширение функционала

- Новые поля задачи: миграция → `$fillable` / casts в `Task` → `TaskRequest` → Blade-формы → тесты.
- Новые статусы: только `TaskStatus` + миграция при смене хранения (если нужно) + подписи/стили в enum и CSS.
- API отдельно от Blade: завести `routes/api.php` и контроллеры/ресурсы, не смешивать с текущими web-маршрутами без необходимости.

## Лицензия

Каркас Laravel — см. репозиторий [laravel/laravel](https://github.com/laravel/laravel); код приложения — по договорённости с владельцем репозитория.
