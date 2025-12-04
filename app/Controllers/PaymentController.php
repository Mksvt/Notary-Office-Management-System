<?php
/**
 * Контролер для роботи з платежами
 */

require_once ROOT_PATH . '/app/Models/Payment.php';
require_once ROOT_PATH . '/app/Models/NotarialCase.php';
require_once ROOT_PATH . '/app/Models/Service.php';

class PaymentController {
    public function index() {
        requireAuth();

        $paymentModel = new Payment();
        $payments = $paymentModel->getAllWithDetails();

        // Фільтрація за статусом
        $statusFilter = $_GET['status'] ?? '';
        if ($statusFilter) {
            $payments = array_filter($payments, function($payment) use ($statusFilter) {
                return $payment['status'] === $statusFilter;
            });
        }

        $title = 'Платежі';
        $flash = getFlashMessage();
        
        ob_start();
        include ROOT_PATH . '/app/Views/payments/index.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function createForm($case_id) {
        requireAuth();

        $caseModel = new NotarialCase();
        $case = $caseModel->getByIdWithDetails($case_id);

        if (!$case) {
            setFlashMessage('Справу не знайдено', 'error');
            redirect('/cases');
        }

        $title = 'Новий платіж';
        $errors = [];
        $data = [
            'case_id' => $case_id,
            'payment_date' => date('Y-m-d'),
            'amount' => $case['base_price'],
            'method' => 'cash',
            'status' => 'paid'
        ];
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/payments/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function store($case_id) {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/cases/' . $case_id . '/payments/create');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token verification failed');
        }

        $caseModel = new NotarialCase();
        $case = $caseModel->findById($case_id);

        if (!$case) {
            setFlashMessage('Справу не знайдено', 'error');
            redirect('/cases');
        }

        $data = [
            'case_id' => $case_id,
            'payment_date' => $_POST['payment_date'] ?? date('Y-m-d'),
            'amount' => $_POST['amount'] ?? 0,
            'method' => $_POST['method'] ?? 'cash',
            'receipt_number' => trim($_POST['receipt_number'] ?? ''),
            'status' => $_POST['status'] ?? 'paid',
            'comment' => trim($_POST['comment'] ?? '')
        ];

        $data = array_filter($data, function($value) {
            return $value !== '';
        });

        $paymentModel = new Payment();
        $errors = $paymentModel->validate($data);

        if (empty($errors)) {
            // Генерувати номер квитанції якщо не вказаний
            if (empty($data['receipt_number'])) {
                $data['receipt_number'] = 'RCP-' . date('Ymd') . '-' . uniqid();
            }
            
            $paymentId = $paymentModel->create($data);
            
            if ($paymentId) {
                setFlashMessage('Платіж успішно створено', 'success');
                redirect('/cases/view/' . $case_id);
            } else {
                $errors['general'] = 'Помилка при створенні платежу';
            }
        }

        $caseModel = new NotarialCase();
        $case = $caseModel->getByIdWithDetails($case_id);
        
        $title = 'Новий платіж';
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/payments/form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function receiptHtml($id) {
        requireAuth();

        $paymentModel = new Payment();
        $payment = $paymentModel->getByIdWithDetails($id);

        if (!$payment) {
            setFlashMessage('Платіж не знайдено', 'error');
            redirect('/payments');
        }

        $title = 'Квитанція №' . $payment['receipt_number'];
        
        include ROOT_PATH . '/app/Views/payments/receipt.php';
    }
}
