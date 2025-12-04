<?php
/**
 * Контролер для роботи з клієнтами
 */

require_once ROOT_PATH . '/app/Models/Client.php';
require_once ROOT_PATH . '/app/Models/NotarialCase.php';
require_once ROOT_PATH . '/app/Models/Service.php';

class ClientController {
    public function index() {
        requireAuth();

        $clientModel = new Client();
        $search = $_GET['search'] ?? '';

        if ($search) {
            $clients = $clientModel->search($search);
        } else {
            $clients = $clientModel->findAll('last_name, first_name');
        }
        
        // Фільтрувати клієнтів для нотаріуса - показувати тільки своїх
        if ($_SESSION['user_role'] === 'notary') {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT DISTINCT c.* FROM clients c
                    INNER JOIN notarial_cases nc ON c.client_id = nc.client_id
                    WHERE nc.notary_id = :notary_id
                    ORDER BY c.last_name, c.first_name";
            $stmt = $db->prepare($sql);
            $stmt->execute(['notary_id' => $_SESSION['related_id']]);
            $clients = $stmt->fetchAll();
        }

        $title = 'Клієнти';
        $flash = getFlashMessage();
        
        ob_start();
        include ROOT_PATH . '/app/Views/clients/index.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function createForm() {
        requireAuth();

        $title = 'Новий клієнт';
        $errors = [];
        $data = [];
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/clients/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function store() {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/clients');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $data = [
            'last_name' => trim($_POST['last_name'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'middle_name' => trim($_POST['middle_name'] ?? ''),
            'birth_date' => $_POST['birth_date'] ?? null,
            'passport_series' => trim($_POST['passport_series'] ?? ''),
            'passport_number' => trim($_POST['passport_number'] ?? ''),
            'tax_id' => trim($_POST['tax_id'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? '')
        ];

        // Видалити порожні поля
        $data = array_filter($data, function($value) {
            return $value !== '';
        });

        $clientModel = new Client();
        $errors = $clientModel->validate($data);

        if (empty($errors)) {
            $clientId = $clientModel->create($data);
            
            if ($clientId) {
                setFlashMessage('Клієнта успішно створено', 'success');
                redirect('/clients/view/' . $clientId);
            } else {
                $errors['general'] = 'Помилка при створенні клієнта';
            }
        }

        // Відобразити форму з помилками
        $title = 'Новий клієнт';
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/clients/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function editForm($id) {
        requireAuth();

        $clientModel = new Client();
        $client = $clientModel->findById($id);

        if (!$client) {
            setFlashMessage('Клієнта не знайдено', 'error');
            redirect('/clients');
        }

        $title = 'Редагування клієнта';
        $data = $client;
        $errors = [];
        $csrfToken = generateCsrfToken();
        $isEdit = true;
        
        ob_start();
        include ROOT_PATH . '/app/Views/clients/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function update($id) {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/clients');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $clientModel = new Client();
        $client = $clientModel->findById($id);

        if (!$client) {
            setFlashMessage('Клієнта не знайдено', 'error');
            redirect('/clients');
        }

        $data = [
            'last_name' => trim($_POST['last_name'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'middle_name' => trim($_POST['middle_name'] ?? ''),
            'birth_date' => $_POST['birth_date'] ?? null,
            'passport_series' => trim($_POST['passport_series'] ?? ''),
            'passport_number' => trim($_POST['passport_number'] ?? ''),
            'tax_id' => trim($_POST['tax_id'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? '')
        ];

        $data = array_filter($data, function($value) {
            return $value !== '';
        });

        $errors = $clientModel->validate($data, true, $id);

        if (empty($errors)) {
            if ($clientModel->update($id, $data)) {
                setFlashMessage('Клієнта успішно оновлено', 'success');
                redirect('/clients/view/' . $id);
            } else {
                $errors['general'] = 'Помилка при оновленні клієнта';
            }
        }

        $title = 'Редагування клієнта';
        $data = array_merge($client, $data);
        $csrfToken = generateCsrfToken();
        $isEdit = true;
        
        ob_start();
        include ROOT_PATH . '/app/Views/clients/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function show($id) {
        requireAuth();

        $clientModel = new Client();
        $client = $clientModel->findById($id);

        if (!$client) {
            setFlashMessage('Клієнта не знайдено', 'error');
            redirect('/clients');
        }

        // Отримати справи клієнта
        $caseModel = new NotarialCase();
        $cases = $caseModel->findWhere(['client_id' => $id], 'open_date DESC');

        $title = 'Перегляд клієнта';
        $flash = getFlashMessage();
        
        ob_start();
        include ROOT_PATH . '/app/Views/clients/view.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function delete($id) {
        requireAuth();
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/clients');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $clientModel = new Client();
        
        // Перевірити чи немає справ
        if ($clientModel->getCasesCount($id) > 0) {
            setFlashMessage('Неможливо видалити клієнта, який має справи', 'error');
        } else {
            if ($clientModel->delete($id)) {
                setFlashMessage('Клієнта успішно видалено', 'success');
            } else {
                setFlashMessage('Помилка при видаленні клієнта', 'error');
            }
        }

        redirect('/clients');
    }
}
