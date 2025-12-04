<?php
/**
 * Конфігурація системи нотаріальної контори
 */

// Налаштування бази даних
define('DB_HOST', 'localhost');
define('DB_NAME', 'notary_office');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Базовий URL проєкту
define('BASE_URL', 'http://localhost/coursework2/public');

// Шлях до кореневої директорії
define('ROOT_PATH', dirname(__DIR__));

// Шлях до папки завантажень
define('UPLOAD_PATH', ROOT_PATH . '/public/uploads/documents');

// Дозволені типи файлів для завантаження
define('ALLOWED_FILE_TYPES', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']);

// Максимальний розмір файлу (5MB)
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// Часовий пояс
date_default_timezone_set('Europe/Kyiv');

// Відображення помилок (для розробки)
error_reporting(E_ALL);
ini_set('display_errors', 1);
