<?php
/**
 * Контролер домашньої сторінки
 */

require_once ROOT_PATH . '/app/Models/NotarialCase.php';
require_once ROOT_PATH . '/app/Models/Client.php';
require_once ROOT_PATH . '/app/Models/Payment.php';

class HomeController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }

        $caseModel = new NotarialCase();
        $clientModel = new Client();
        $paymentModel = new Payment();

        // Статистика
        $stats = [
            'total_cases' => $caseModel->count(),
            'open_cases' => $caseModel->count(['status' => 'open']),
            'in_progress_cases' => $caseModel->count(['status' => 'in_progress']),
            'closed_cases' => $caseModel->count(['status' => 'closed']),
            'total_clients' => $clientModel->count(),
            'total_paid' => $paymentModel->getTotalPaid()
        ];

        // Останні справи
        if ($_SESSION['user_role'] === 'notary') {
            $recentCases = $caseModel->getAllWithDetails($_SESSION['related_id']);
        } else {
            $recentCases = $caseModel->getAllWithDetails();
        }
        
        $recentCases = array_slice($recentCases, 0, 10);

        $title = 'Головна сторінка';
        $flash = getFlashMessage();
        
        ob_start();
        include ROOT_PATH . '/app/Views/home/index.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }
}
