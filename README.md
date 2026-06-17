Инструкция по запуску Лабораторной работы (MongoDB + NoSQL)

Шаг 1. Размещение файлов
Распакуй папку с проектом в директорию твоего локального веб-сервера (OpenServer).

Если у тебя старая версия OpenServer (5.x), это папка OSPanel\domains.

Если новая (6.0+), это папка OSPanel\home.

<img width="729" height="562" alt="image" src="https://github.com/user-attachments/assets/e433ce4c-114c-416f-86ba-654effc1d97c" />


Здесь не забудь создать папку mongodbclustiv.local, это обязательно, и закинь туда все наши файлы (index.php, db_mongo.php, .osp/project.ini):

<img width="1146" height="403" alt="image" src="https://github.com/user-attachments/assets/73b7bfe6-d72d-4dc3-8abc-2651c99511a1" />


Критически важно! Рядом с этими файлами обязательно должна лежать скопированная папка vendor и файл composer.lock. Без них драйвер MongoDB в PHP работать не будет.

Шаг 2. Заполнение базы данных в MongoDB Compass (ОБЯЗАТЕЛЬНО)
Проект работает с нереляционной NoSQL СУБД MongoDB и не будет отображать товары без наполнения коллекции.

Запусти OpenServer, открой MongoDB Compass и подключись к локальному хосту (mongodb://localhost:27017).

<img width="1056" height="548" alt="image" src="https://github.com/user-attachments/assets/4b273f9b-f127-4072-9121-984f1b1a342b" />


В левом меню нажми Create database (или выбери существующую), назови базу данных dbforlab.

<img width="883" height="660" alt="image" src="https://github.com/user-attachments/assets/385b775f-98c0-42e7-aaf0-856bf7ba16ab" />


Внутри базы данных создай коллекцию с именем goods.

Нажми кнопку Add Data ➡️ Insert Document, переключи режим отображения на иконку фигурных скобок { } (JSON mode).

<img width="894" height="379" alt="image" src="https://github.com/user-attachments/assets/e8ae07f7-d77d-423d-b2c5-d6bc1a55f5ef" />


Полностью удали стандартный текст, вставь наш красивый JSON-массив товаров с оригинальными геймерскими отзывами и нажми Insert.

```[
  {
    "name": "Intel Core i9-14900K",
    "price": 22500,
    "quantity": 5,
    "vendor": "Intel",
    "category": "CPU",
    "condition": "new",
    "reviews": ["Неймовірна потужність для геймінгу", "Дуже гарячий, потрібне гарне водяне охолодження"]
  },
  {
    "name": "AMD Ryzen 7 7800X3D",
    "price": 16800,
    "quantity": 0,
    "vendor": "AMD",
    "category": "CPU",
    "condition": "new",
    "reviews": ["Кращий ігровий процесор на ринку", "Топ за свої гроші для геймерів"]
  },
  {
    "name": "ASUS ROG Strix RTX 4070 Ti",
    "price": 38500,
    "quantity": 3,
    "vendor": "ASUS",
    "category": "Videocard",
    "condition": "new",
    "reviews": ["Дуже тиха та холодна система охолодження", "Плавний геймплей у 2K"]
  },
  {
    "name": "Gigabyte GeForce RTX 3060",
    "price": 10500,
    "quantity": 0,
    "vendor": "Gigabyte",
    "category": "Videocard",
    "condition": "used",
    "reviews": ["Стан ідеальний, кулери не шумлять", "Перевірена в тестах, працює без нарікань"]
  },
  {
    "name": "Samsung Odyssey G5 27\"",
    "price": 11200,
    "quantity": 7,
    "vendor": "Samsung",
    "category": "Display",
    "condition": "new",
    "reviews": ["Яскрава матриця та чудовий вигин екрану"]
  },
  {
    "name": "Kingston Fury DDR5 16GB",
    "price": 2600,
    "quantity": 12,
    "vendor": "Kingston",
    "category": "Memory",
    "condition": "new",
    "reviews": ["Швидка пам'ять, XMP профіль завівся без проблем", "Гарне RGB підсвічування"]
  }
]
```

Шаг 3. Настройка подключения (db_mongo.php)
Открой файл db_mongo.php в любом редакторе. Подключение к MongoDB по умолчанию использует стандартный порт локального хоста:

PHP
$client = new MongoDB\Client("mongodb://127.0.0.1:27017");
$collection = $client->selectDatabase('dbforlab')->selectCollection('goods');
Если твоя локальная MongoDB работает на другом порту или требует авторизации, измени строку подключения внутри блока try.

<img width="799" height="244" alt="image" src="https://github.com/user-attachments/assets/7d8a2fdd-5768-4035-b661-965431b777f0" />


Шаг 4. Запуск
Запусти проект (или перезапусти OpenServer, чтобы он увидел новую папку). Открой браузер и перейди по локальному адресу проекта: http://mongodbclustiv.local.

<img width="600" height="242" alt="image" src="https://github.com/user-attachments/assets/1c112966-97ed-4260-b9dc-b21c676f60bd" />

Также просмотри, чтобы в настройках твоего модуля в OpenServer стояли совместимые версии окружения:

PHP: Версия PHP 8.1 или выше (рекомендуется PHP 8.3), так как под них скомпилирован перенесённый vendor.

Шаг 5. Проверка приложения и NoSQL-технологий
Пройдись по всем трем запросам, разработанным под Вариант №5. Главная фишка этой лабораторной — демонстрация преимуществ документо-ориентированных баз данных и хранение сложных массивов данных:

<img width="1919" height="316" alt="image" src="https://github.com/user-attachments/assets/7b0af000-8090-43e7-83f5-55c426762602" />


Бренды магазина: Кнопка «Отримати перелік виробників». Собирает чистый список уникальных брендов напрямую из документов с помощью нативного NoSQL метода $collection->distinct('vendor').

<img width="1918" height="713" alt="image" src="https://github.com/user-attachments/assets/55151d9c-6b4a-4623-ba83-561255707e85" />


Дефицитные позиции: Кнопка «Товари з залишком 0». Ищет товары, которые в данный момент отсутствуют на складе ('quantity' => 0).

<img width="1919" height="514" alt="image" src="https://github.com/user-attachments/assets/e7a771dd-6daf-4e97-a3ba-d1d6f1b76793" />


Фильтр по цене: Кнопка «Пошук». Фильтрует документы в выбранном диапазоне цен, используя специальные NoSQL-операторы сравнения $gte (больше или равно) и $lte (меньше или равно).

<img width="1919" height="562" alt="image" src="https://github.com/user-attachments/assets/5cd542f2-c5be-479c-8709-bbacf0a6780a" />

Обрати внимание на отзывы (reviews): В отличие от реляционных баз данных (SQL), где для отзывов нужна отдельная таблица и связь JOIN, в MongoDB они хранятся прямо внутри документа товара в виде вложенного массива строк. На сайте они выводятся красивыми серыми плашками.

🛠 Частые ошибки:
Ошибка "Failed to open stream: No such file or directory": Ты забыл скопировать папку vendor в корень проекта mongodbclustiv.local, либо версия PHP в настройках OpenServer не совпадает с той, на которой собирался vendor.

Таблицы на сайте пустые: Коллекция goods в базе данных dbforlab пуста. Проверь в MongoDB Compass, успешно ли импортировался JSON-массив документов.

Ошибка "Class 'MongoDB\Client' not found": В PHP не подключено расширение mongodb.dll. Убедись, что используешь правильный модуль PHP в OSPanel, где этот драйвер уже активирован.
