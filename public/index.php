<?php
/**
 * Головна точка входу в систему
 */

// Налаштування сесії (до session_start!)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();

// Підключення конфігурації
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/router.php';
require_once __DIR__ . '/../app/Models/BaseModel.php';

// Функція для захисту від CSRF
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Функція перевірки авторизації
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

// Функція перевірки ролі
function requireRole($role) {
    requireAuth();
    if ($_SESSION['user_role'] !== $role && $_SESSION['user_role'] !== 'admin') {
        die("Доступ заборонено");
    }
}

// Функція для відображення flash-повідомлень
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Функція для безпечного перенаправлення
function redirect($path) {
    header('Location: ' . BASE_URL . $path);
    exit;
}

// Створення роутера
$router = new Router();

// === МАРШРУТИ АВТЕНТИФІКАЦІЇ ===
$router->get('/', 'HomeController', 'index');
$router->get('/login', 'AuthController', 'loginForm');
$router->post('/login', 'AuthController', 'login');
$router->get('/logout', 'AuthController', 'logout');

// === МАРШРУТИ КЛІЄНТІВ ===
$router->get('/clients', 'ClientController', 'index');
$router->get('/clients/create', 'ClientController', 'createForm');
$router->post('/clients/create', 'ClientController', 'store');
$router->get('/clients/edit/{id}', 'ClientController', 'editForm');
$router->post('/clients/edit/{id}', 'ClientController', 'update');
$router->post('/clients/delete/{id}', 'ClientController', 'delete');
$router->get('/clients/view/{id}', 'ClientController', 'show');

// === МАРШРУТИ НОТАРІУСІВ ===
$router->get('/notaries', 'NotaryController', 'index');
$router->get('/notaries/create', 'NotaryController', 'createForm');
$router->post('/notaries/create', 'NotaryController', 'store');
$router->get('/notaries/edit/{id}', 'NotaryController', 'editForm');
$router->post('/notaries/edit/{id}', 'NotaryController', 'update');
$router->post('/notaries/deactivate/{id}', 'NotaryController', 'deactivate');

// === МАРШРУТИ ОФІСІВ ===
$router->get('/offices', 'OfficeController', 'index');
$router->get('/offices/create', 'OfficeController', 'createForm');
$router->post('/offices/create', 'OfficeController', 'store');
$router->get('/offices/edit/{id}', 'OfficeController', 'editForm');
$router->post('/offices/edit/{id}', 'OfficeController', 'update');

// === МАРШРУТИ ПОСЛУГ ===
$router->get('/services', 'ServiceController', 'index');
$router->get('/services/create', 'ServiceController', 'createForm');
$router->post('/services/create', 'ServiceController', 'store');
$router->get('/services/edit/{id}', 'ServiceController', 'editForm');
$router->post('/services/edit/{id}', 'ServiceController', 'update');

// === МАРШРУТИ СПРАВ ===
$router->get('/cases', 'CaseController', 'index');
$router->get('/cases/create', 'CaseController', 'createForm');
$router->post('/cases/create', 'CaseController', 'store');
$router->get('/cases/view/{id}', 'CaseController', 'show');
$router->post('/cases/status/{id}', 'CaseController', 'changeStatus');

// === МАРШРУТИ ДОКУМЕНТІВ ===
$router->get('/cases/{case_id}/documents/upload', 'DocumentController', 'uploadForm');
$router->post('/cases/{case_id}/documents/upload', 'DocumentController', 'upload');
$router->get('/cases/{case_id}/documents', 'DocumentController', 'index');
$router->get('/documents/download/{id}', 'DocumentController', 'download');

// === МАРШРУТИ ПЛАТЕЖІВ ===
$router->get('/payments', 'PaymentController', 'index');
$router->get('/cases/{case_id}/payments/create', 'PaymentController', 'createForm');
$router->post('/cases/{case_id}/payments/create', 'PaymentController', 'store');
$router->get('/payments/receipt/{id}', 'PaymentController', 'receiptHtml');

// === КЛІЄНТСЬКИЙ ПОРТАЛ (БЕЗ АВТОРИЗАЦІЇ) ===
$router->get('/portal/check-status', 'ClientPortalController', 'checkStatusForm');
$router->post('/portal/check-status', 'ClientPortalController', 'checkStatus');
$router->get('/portal/application', 'ClientPortalController', 'applicationForm');
$router->post('/portal/application', 'ClientPortalController', 'submitApplication');

// Обробка 404
$router->setNotFound(function() {
    http_response_code(404);
    echo "<h1>404 - Сторінку не знайдено</h1>";
});

// Запуск роутера
$router->dispatch();
