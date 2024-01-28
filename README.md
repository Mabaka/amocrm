# Обмен с AmoCrm

## Требования

- PHP >= 7 версии
- Установленный пакетный менеджер npm, поддерживающий устанвоку Vue 3 + TypeScript + Vite
- Установленный пакетный менеджер composer для PHP
- Доступ к amoCRM и наличие интеграции с clientID(ID интеграции), client_secret(Секретный ключ), сайт с внешним доступом
- Желательно сервер Apache(можно использовать xampp)

## Запуск проекта

- Установить зависимости для frontend и api
- Перенести проект(или папку api) в htdocs Apache сервера(или настроить под свой)
- Запустить backend(папка api) и frontend
- Проверить что backend и frontend доступны(локально)
- В vite.config настроен прокси на backend, поэтому обращаться можно через <http://localhost:8080/api>. Таким образом, добавление лида будет по адресу (<http://localhost:8080/api/lead/add/>). Порт и адрес такие при стандартной настройки. У вас может отличаться, если укажите в перменных среды. localhost:8080 считается сервер фронта.
- Вывести во внешнюю сеть backend сервер или frontend. Указать в env на фронте для работы прокси. Если выведен frontend api вызывается только через
<http://frontserver.ru/api/lead/add>.

### Запуск frontend

- Установка зависимостей в корневой папке frontend
   `npm ci`
- Установить переменные среды(файл env), предварительно поставив точку перед именем файла
  - VITE_CLIENT_ID - ID интеграции
  - VITE_PORT - порт, на котором будет запущен фронтенд. 8080 по умолчанию
  - VITE_SERVER - путь, по которому будет доступен фронт. <http://localhost> по умолчанию
  - VITE_API_SERVER - адрес бэка. Пример: <http://yourserver.ru/amocrm/api/app/>
- Запустить frontend можно без сборки:
   `npm run dev`
- Если необходима сборка:
   `npm run build`

### Запуск backend

- Установить зависимости в корневой папке api:
   `composer install`
- На всякий случай:
   `composer dump-autoload`
- Установить переменные среды
  - CLIENT_ID - ID интеграции
  - CLIENT_SECRET - секретный ключ интеграции
  - CLIENT_REDIRECT_URI - редирект для отлова аутентификации(такой же должен быть в интеграции)
  - URL - url путь к фронту(по умолчанию <http://localhost:8080>)
  - Для корректировки запросов можно настроить VirtualHost и не указывать относительный путь. Может потребоваться настроить в index.php обработку путей.
  
### Необходимые действия в AmoCRM

- Создать новую внешнюю интеграцию
- Получить client_id(id интеграции) и secret_id(секретный ключ)
- Указать редирект в интергации, который будет обрабатывать аутентификацию

### Обращения при стандартной настройке

- <http://yourserver.ru/amocrm/api/app/auth/> вручную не вызывается, нужно указывать в интергации для аутентификации(редирект на этот адрес) или <http://localhost:8080/api/auth/>
- <http://yourserver.ru/amocrm/api/app/lead/add> или <http://localhost:8080/api/lead/add>(прокси) - POST запрос на добавление лида.

## Алгоритм работы

- Запустить frontend и backend
- Перейти по адресу frontend сервера
- Установить интеграцию(если все переменные среды заполнены правильно, появится имя интергации и выбор аккаунта, у которого есть доступ к интергации)
- Заполнить поля и нажать "отправить данные". Если все успешно, появится сообщение об успешном добавлении, иначе оповещение об ошибке. Тело ответа (response) можно посмотреть в консоли