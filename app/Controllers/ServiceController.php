<?php
/**
 * Контролер для роботи з послугами
 */

require_once ROOT_PATH . '/app/Models/Service.php';

class ServiceController {
    public function index() {
        requireAuth();

        $serviceModel = new Service();
        $services = $serviceModel->findAll('name');

        $title = 'Послуги';
        $flash = getFlashMessage();
        
        ob_start();
        include ROOT_PATH . '/app/Views/services/index.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function createForm() {
        requireAuth();
        requireRole('admin');

        $title = 'Нова послуга';
        $errors = [];
        $data = [];
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/services/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function store() {
        requireAuth();
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/services');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'base_price' => $_POST['base_price'] ?? 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 1
        ];

        $serviceModel = new Service();
        $errors = $serviceModel->validate($data);

        if (empty($errors)) {
            $serviceId = $serviceModel->create($data);
            
            if ($serviceId) {
                setFlashMessage('Послугу успішно створено', 'success');
                redirect('/services');
            } else {
                $errors['general'] = 'Помилка при створенні послуги';
            }
        }

        $title = 'Нова послуга';
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/services/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function editForm($id) {
        requireAuth();
        requireRole('admin');

        $serviceModel = new Service();
        $service = $serviceModel->findById($id);

        if (!$service) {
            setFlashMessage('Послугу не знайдено', 'error');
            redirect('/services');
        }

        $title = 'Редагування послуги';
        $data = $service;
        $errors = [];
        $csrfToken = generateCsrfToken();
        $isEdit = true;
        
        ob_start();
        include ROOT_PATH . '/app/Views/services/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function update($id) {
        requireAuth();
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/services');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $serviceModel = new Service();
        $service = $serviceModel->findById($id);

        if (!$service) {
            setFlashMessage('Послугу не знайдено', 'error');
            redirect('/services');
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'base_price' => $_POST['base_price'] ?? 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        $errors = $serviceModel->validate($data);

        if (empty($errors)) {
            if ($serviceModel->update($id, $data)) {
                setFlashMessage('Послугу успішно оновлено', 'success');
                redirect('/services');
            } else {
                $errors['general'] = 'Помилка при оновленні послуги';
            }
        }

        $title = 'Редагування послуги';
        $data = array_merge($service, $data);
        $csrfToken = generateCsrfToken();
        $isEdit = true;
        
        ob_start();
        include ROOT_PATH . '/app/Views/services/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }
}
