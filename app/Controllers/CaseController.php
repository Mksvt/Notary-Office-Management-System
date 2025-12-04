<?php
/**
 * Контролер для роботи зі справами
 */

require_once ROOT_PATH . '/app/Models/NotarialCase.php';
require_once ROOT_PATH . '/app/Models/Client.php';
require_once ROOT_PATH . '/app/Models/Notary.php';
require_once ROOT_PATH . '/app/Models/Service.php';
require_once ROOT_PATH . '/app/Models/Document.php';
require_once ROOT_PATH . '/app/Models/Payment.php';

class CaseController {
    public function index() {
        requireAuth();

        $caseModel = new NotarialCase();
        
        // Якщо користувач - нотаріус, показати тільки його справи
        if ($_SESSION['user_role'] === 'notary') {
            $cases = $caseModel->getAllWithDetails($_SESSION['related_id']);
        } else {
            $cases = $caseModel->getAllWithDetails();
        }

        // Фільтрація за статусом
        $statusFilter = $_GET['status'] ?? '';
        if ($statusFilter) {
            $cases = array_filter($cases, function($case) use ($statusFilter) {
                return $case['status'] === $statusFilter;
            });
        }

        $title = 'Справи';
        $flash = getFlashMessage();
        
        ob_start();
        include ROOT_PATH . '/app/Views/cases/index.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function createForm() {
        requireAuth();

        $clientModel = new Client();
        $notaryModel = new Notary();
        $serviceModel = new Service();

        // Нотаріус бачить тільки своїх клієнтів
        if ($_SESSION['user_role'] === 'notary') {
            $clients = $clientModel->getClientsByNotary($_SESSION['related_id']);
        } else {
            $clients = $clientModel->findAll('last_name, first_name');
        }
        
        $notaries = $notaryModel->getActiveNotaries();
        $services = $serviceModel->getActiveServices();

        $title = 'Нова справа';
        $errors = [];
        $data = ['open_date' => date('Y-m-d')];
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/cases/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function store() {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/cases');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $caseModel = new NotarialCase();
        
        $data = [
            'case_number' => $caseModel->generateCaseNumber(),
            'client_id' => $_POST['client_id'] ?? null,
            'notary_id' => $_POST['notary_id'] ?? null,
            'service_id' => $_POST['service_id'] ?? null,
            'open_date' => $_POST['open_date'] ?? date('Y-m-d'),
            'status' => 'open',
            'notes' => trim($_POST['notes'] ?? '')
        ];

        $errors = $caseModel->validate($data);

        if (empty($errors)) {
            $caseId = $caseModel->create($data);
            
            if ($caseId) {
                setFlashMessage('Справу успішно створено. Номер справи: ' . $data['case_number'], 'success');
                redirect('/cases/view/' . $caseId);
            } else {
                $errors['general'] = 'Помилка при створенні справи';
            }
        }

        $clientModel = new Client();
        $notaryModel = new Notary();
        $serviceModel = new Service();

        // Нотаріус бачить тільки своїх клієнтів
        if ($_SESSION['user_role'] === 'notary') {
            $clients = $clientModel->getClientsByNotary($_SESSION['related_id']);
        } else {
            $clients = $clientModel->findAll('last_name, first_name');
        }
        
        $notaries = $notaryModel->getActiveNotaries();
        $services = $serviceModel->getActiveServices();
        
        $title = 'Нова справа';
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/cases/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function show($id) {
        requireAuth();

        $caseModel = new NotarialCase();
        $case = $caseModel->getByIdWithDetails($id);

        if (!$case) {
            setFlashMessage('Справу не знайдено', 'error');
            redirect('/cases');
        }

        // Перевірка доступу для нотаріуса
        if ($_SESSION['user_role'] === 'notary' && $case['notary_id'] != $_SESSION['related_id']) {
            die('Доступ заборонено');
        }

        // Отримати документи та платежі
        $documentModel = new Document();
        $paymentModel = new Payment();
        
        $documents = $documentModel->getByCaseId($id);
        $payments = $paymentModel->getByCaseId($id);

        $title = 'Справа №' . $case['case_number'];
        $flash = getFlashMessage();
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/cases/view.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function changeStatus($id) {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/cases');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $caseModel = new NotarialCase();
        $case = $caseModel->findById($id);

        if (!$case) {
            setFlashMessage('Справу не знайдено', 'error');
            redirect('/cases');
        }

        // Перевірка доступу для нотаріуса
        if ($_SESSION['user_role'] === 'notary' && $case['notary_id'] != $_SESSION['related_id']) {
            die('Доступ заборонено');
        }

        $newStatus = $_POST['status'] ?? '';

        if ($caseModel->changeStatus($id, $newStatus)) {
            setFlashMessage('Статус справи змінено на "' . $caseModel->getStatusLabel($newStatus) . '"', 'success');
        } else {
            setFlashMessage('Помилка при зміні статусу справи', 'error');
        }

        redirect('/cases/view/' . $id);
    }
}
