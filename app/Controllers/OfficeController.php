<?php
/**
 * Контролер для роботи з офісами
 */

require_once ROOT_PATH . '/app/Models/Office.php';

class OfficeController {
    public function index() {
        requireAuth();

        $officeModel = new Office();
        
        // Нотаріус бачить тільки свій офіс
        if ($_SESSION['user_role'] === 'notary') {
            require_once ROOT_PATH . '/app/Models/Notary.php';
            $notaryModel = new Notary();
            $notary = $notaryModel->findById($_SESSION['related_id']);
            if ($notary) {
                $offices = [$officeModel->findById($notary['office_id'])];
            } else {
                $offices = [];
            }
        } else {
            $offices = $officeModel->findAll('name');
        }

        $title = 'Офіси';
        $flash = getFlashMessage();
        
        ob_start();
        include ROOT_PATH . '/app/Views/offices/index.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function createForm() {
        requireAuth();
        requireRole('admin');

        $title = 'Новий офіс';
        $errors = [];
        $data = [];
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/offices/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function store() {
        requireAuth();
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/offices');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'schedule' => trim($_POST['schedule'] ?? '')
        ];

        $data = array_filter($data, function($value) {
            return $value !== '';
        });

        $officeModel = new Office();
        $errors = $officeModel->validate($data);

        if (empty($errors)) {
            $officeId = $officeModel->create($data);
            
            if ($officeId) {
                setFlashMessage('Офіс успішно створено', 'success');
                redirect('/offices');
            } else {
                $errors['general'] = 'Помилка при створенні офісу';
            }
        }

        $title = 'Новий офіс';
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/offices/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function editForm($id) {
        requireAuth();
        requireRole('admin');

        $officeModel = new Office();
        $office = $officeModel->findById($id);

        if (!$office) {
            setFlashMessage('Офіс не знайдено', 'error');
            redirect('/offices');
        }

        $title = 'Редагування офісу';
        $data = $office;
        $errors = [];
        $csrfToken = generateCsrfToken();
        $isEdit = true;
        
        ob_start();
        include ROOT_PATH . '/app/Views/offices/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function update($id) {
        requireAuth();
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/offices');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $officeModel = new Office();
        $office = $officeModel->findById($id);

        if (!$office) {
            setFlashMessage('Офіс не знайдено', 'error');
            redirect('/offices');
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'schedule' => trim($_POST['schedule'] ?? '')
        ];

        $data = array_filter($data, function($value) {
            return $value !== '';
        });

        $errors = $officeModel->validate($data);

        if (empty($errors)) {
            if ($officeModel->update($id, $data)) {
                setFlashMessage('Офіс успішно оновлено', 'success');
                redirect('/offices');
            } else {
                $errors['general'] = 'Помилка при оновленні офісу';
            }
        }

        $title = 'Редагування офісу';
        $data = array_merge($office, $data);
        $csrfToken = generateCsrfToken();
        $isEdit = true;
        
        ob_start();
        include ROOT_PATH . '/app/Views/offices/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }
}
