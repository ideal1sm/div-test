## About
Проект разворачивается при помощи официальной библиотеки Laravel sail, котоаря явялется оберткой над docker. Она помогает легко создать контейнеры и манипулировать ими.

Версия Laravel - 10.15.0<br>
Версия PHP - не нижне 8.1

## Запуск проекта

- git pull git@github.com:ideal1sm/div-test.git
- cd div-test.git
- composer i
- cp .env.example .env
- ./vendor/bin/sail up -d
- ./vendor/bin/sail artisan key:generate
- ./vendor/bin/sail artisan migrate --seed
- ./vendor/bin/sail artisan l5-swagger:generate
- ./vendor/bin/sail artisan queue:listen

## Как просматировать отправленные почтовые сообщения

При помощи docker поднят mailpit, который позволяет просматривать отправленные email'ы. Веб интерфейс расположен по адресу:

- [localhost:8025]()

## Документация

- [localhost/api/documentation]()

## Также

Данные для тестового пользователя:<br>
testuser@gmail.com<br>
user

Данные для тестового менеджера:<br>
testmanager@gmail.com<br>
manager
