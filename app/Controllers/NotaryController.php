<?php
/**
 * Контролер для роботи з нотаріусами
 */

require_once ROOT_PATH . '/app/Models/Notary.php';
require_once ROOT_PATH . '/app/Models/Office.php';

class NotaryController {
    public function index() {
        requireAuth();

        $notaryModel = new Notary();
        $notaries = $notaryModel->getAllWithOffices();

        $title = 'Нотаріуси';
        $flash = getFlashMessage();
        
        ob_start();
        include ROOT_PATH . '/app/Views/notaries/index.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function createForm() {
        requireAuth();
        requireRole('admin');

        $officeModel = new Office();
        $offices = $officeModel->findAll('name');

        $title = 'Новий нотаріус';
        $errors = [];
        $data = [];
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/notaries/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function store() {
        requireAuth();
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/notaries');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $data = [
            'last_name' => trim($_POST['last_name'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'middle_name' => trim($_POST['middle_name'] ?? ''),
            'license_number' => trim($_POST['license_number'] ?? ''),
            'license_issue_date' => $_POST['license_issue_date'] ?? null,
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'office_id' => $_POST['office_id'] ?? null,
            'hired_at' => $_POST['hired_at'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 1
        ];

        $data = array_filter($data, function($value) {
            return $value !== '';
        });

        $notaryModel = new Notary();
        $errors = $notaryModel->validate($data);

        if (empty($errors)) {
            $notaryId = $notaryModel->create($data);
            
            if ($notaryId) {
                setFlashMessage('Нотаріуса успішно створено', 'success');
                redirect('/notaries');
            } else {
                $errors['general'] = 'Помилка при створенні нотаріуса';
            }
        }

        $officeModel = new Office();
        $offices = $officeModel->findAll('name');
        
        $title = 'Новий нотаріус';
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/notaries/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function editForm($id) {
        requireAuth();
        requireRole('admin');

        $notaryModel = new Notary();
        $notary = $notaryModel->findById($id);

        if (!$notary) {
            setFlashMessage('Нотаріуса не знайдено', 'error');
            redirect('/notaries');
        }

        $officeModel = new Office();
        $offices = $officeModel->findAll('name');

        $title = 'Редагування нотаріуса';
        $data = $notary;
        $errors = [];
        $csrfToken = generateCsrfToken();
        $isEdit = true;
        
        ob_start();
        include ROOT_PATH . '/app/Views/notaries/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function update($id) {
        requireAuth();
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/notaries');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $notaryModel = new Notary();
        $notary = $notaryModel->findById($id);

        if (!$notary) {
            setFlashMessage('Нотаріуса не знайдено', 'error');
            redirect('/notaries');
        }

        $data = [
            'last_name' => trim($_POST['last_name'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'middle_name' => trim($_POST['middle_name'] ?? ''),
            'license_number' => trim($_POST['license_number'] ?? ''),
            'license_issue_date' => $_POST['license_issue_date'] ?? null,
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'office_id' => $_POST['office_id'] ?? null,
            'hired_at' => $_POST['hired_at'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        $data = array_filter($data, function($value) {
            return $value !== '';
        });

        $errors = $notaryModel->validate($data, true, $id);

        if (empty($errors)) {
            if ($notaryModel->update($id, $data)) {
                setFlashMessage('Нотаріуса успішно оновлено', 'success');
                redirect('/notaries');
            } else {
                $errors['general'] = 'Помилка при оновленні нотаріуса';
            }
        }

        $officeModel = new Office();
        $offices = $officeModel->findAll('name');
        
        $title = 'Редагування нотаріуса';
        $data = array_merge($notary, $data);
        $csrfToken = generateCsrfToken();
        $isEdit = true;
        
        ob_start();
        include ROOT_PATH . '/app/Views/notaries/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function deactivate($id) {
        requireAuth();
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/notaries');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $notaryModel = new Notary();
        
        if ($notaryModel->deactivate($id)) {
            setFlashMessage('Нотаріуса деактивовано', 'success');
        } else {
            setFlashMessage('Помилка при деактивації нотаріуса', 'error');
        }

        redirect('/notaries');
    }
}
