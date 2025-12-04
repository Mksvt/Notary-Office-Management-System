<?php
/**
 * Контролер для клієнтського порталу (без авторизації)
 */

require_once ROOT_PATH . '/app/Models/NotarialCase.php';
require_once ROOT_PATH . '/app/Models/Client.php';
require_once ROOT_PATH . '/app/Models/Service.php';
require_once ROOT_PATH . '/app/Models/Document.php';
require_once ROOT_PATH . '/app/Models/Payment.php';
require_once ROOT_PATH . '/app/Models/Notary.php';

class ClientPortalController {
    
    /**
     * Форма перевірки статусу справи
     */
    public function checkStatusForm() {
        $title = 'Перевірка статусу справи';
        $errors = [];
        $data = [];
        
        ob_start();
        include ROOT_PATH . '/app/Views/portal/check_status.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/auth.php';
    }
    
    /**
     * Перевірка статусу справи за номером та ІПН
     */
    public function checkStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/portal/check-status');
        }
        
        $caseNumber = trim($_POST['case_number'] ?? '');
        $taxId = trim($_POST['tax_id'] ?? '');
        $errors = [];
        
        if (empty($caseNumber)) {
            $errors['case_number'] = 'Введіть номер справи';
        }
        
        if (empty($taxId)) {
            $errors['tax_id'] = 'Введіть ІПН';
        } elseif (!preg_match('/^\d{10}$/', $taxId)) {
            $errors['tax_id'] = 'ІПН повинен містити 10 цифр';
        }
        
        if (!empty($errors)) {
            $title = 'Перевірка статусу справи';
            $data = ['case_number' => $caseNumber, 'tax_id' => $taxId];
            
            ob_start();
            include ROOT_PATH . '/app/Views/portal/check_status.php';
            $content = ob_get_clean();
            include ROOT_PATH . '/app/Views/layouts/auth.php';
            return;
        }
        
        // Знайти справу за номером
        $caseModel = new NotarialCase();
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT nc.*, 
                       c.tax_id as client_tax_id,
                       c.last_name as client_last_name,
                       c.first_name as client_first_name,
                       c.phone as client_phone,
                       s.name as service_name,
                       n.last_name as notary_last_name,
                       n.first_name as notary_first_name,
                       o.name as office_name,
                       o.address as office_address,
                       o.phone as office_phone
                FROM notarial_cases nc
                INNER JOIN clients c ON nc.client_id = c.client_id
                INNER JOIN services s ON nc.service_id = s.service_id
                INNER JOIN notaries n ON nc.notary_id = n.notary_id
                INNER JOIN offices o ON n.office_id = o.office_id
                WHERE nc.case_number = :case_number AND c.tax_id = :tax_id";
        
        $stmt = $db->prepare($sql);
        $stmt->execute(['case_number' => $caseNumber, 'tax_id' => $taxId]);
        $case = $stmt->fetch();
        
        if (!$case) {
            $errors['general'] = 'Справу не знайдено або ІПН не співпадає';
            $title = 'Перевірка статусу справи';
            $data = ['case_number' => $caseNumber, 'tax_id' => $taxId];
            
            ob_start();
            include ROOT_PATH . '/app/Views/portal/check_status.php';
            $content = ob_get_clean();
            include ROOT_PATH . '/app/Views/layouts/auth.php';
            return;
        }
        
        // Отримати документи
        $documentModel = new Document();
        $documents = $documentModel->getByCaseId($case['case_id']);
        
        // Отримати платежі
        $paymentModel = new Payment();
        $payments = $paymentModel->getByCaseId($case['case_id']);
        
        $title = 'Статус справи №' . $case['case_number'];
        
        ob_start();
        include ROOT_PATH . '/app/Views/portal/case_details.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/auth.php';
    }
    
    /**
     * Форма подання заяви
     */
    public function applicationForm() {
        $serviceModel = new Service();
        $services = $serviceModel->getActiveServices();
        
        $title = 'Подати заяву';
        $errors = [];
        $data = [];
        
        ob_start();
        include ROOT_PATH . '/app/Views/portal/application_form.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/auth.php';
    }
    
    /**
     * Подання заяви
     */
    public function submitApplication() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/portal/application');
        }
        
        $data = [
            'last_name' => trim($_POST['last_name'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'middle_name' => trim($_POST['middle_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'tax_id' => trim($_POST['tax_id'] ?? ''),
            'service_id' => $_POST['service_id'] ?? null,
            'message' => trim($_POST['message'] ?? '')
        ];
        
        $errors = [];
        
        // Валідація
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Прізвище є обов\'язковим';
        }
        
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'Ім\'я є обов\'язковим';
        }
        
        if (empty($data['phone'])) {
            $errors['phone'] = 'Телефон є обов\'язковим';
        }
        
        if (!empty($data['tax_id']) && !preg_match('/^\d{10}$/', $data['tax_id'])) {
            $errors['tax_id'] = 'ІПН повинен містити 10 цифр';
        }
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некоректний формат email';
        }
        
        if (empty($data['service_id'])) {
            $errors['service_id'] = 'Оберіть послугу';
        }
        
        if (!empty($errors)) {
            $serviceModel = new Service();
            $services = $serviceModel->getActiveServices();
            
            $title = 'Подати заяву';
            
            ob_start();
            include ROOT_PATH . '/app/Views/portal/application_form.php';
            $content = ob_get_clean();
            include ROOT_PATH . '/app/Views/layouts/auth.php';
            return;
        }
        
        // Створити або знайти клієнта
        $clientModel = new Client();
        $client = null;
        
        // Спробувати знайти клієнта ЛИШЕ за ІПН (якщо вказано)
        if (!empty($data['tax_id'])) {
            $client = $clientModel->findByTaxId($data['tax_id']);
        }
        
        try {
            // Якщо клієнт не знайдений (або ІПН не вказано), створити нового
            if (!$client) {
                $clientData = [
                    'last_name' => $data['last_name'],
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'] ?: null,
                    'phone' => $data['phone'],
                    'email' => $data['email'] ?: null,
                    'tax_id' => $data['tax_id'] ?: null,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $clientId = $clientModel->create($clientData);
            } else {
                $clientId = $client['client_id'];
            }
            
            // Створити справу
            $caseModel = new NotarialCase();
            $caseNumber = $caseModel->generateCaseNumber();
            
            // Взяти першого доступного нотаріуса
            $notaryModel = new Notary();
            $notaries = $notaryModel->getActiveNotaries();
            $notaryId = $notaries[0]['notary_id'] ?? 1;
            
            $caseData = [
                'case_number' => $caseNumber,
                'open_date' => date('Y-m-d'),
                'client_id' => $clientId,
                'notary_id' => $notaryId,
                'service_id' => $data['service_id'],
                'status' => 'open',
                'notes' => $data['message'] ?: 'Заява через портал'
            ];
            
            $caseId = $caseModel->create($caseData);
            
            // Показати успішне повідомлення з номером справи
            $title = 'Заяву подано';
            $success = true;
            $generatedCaseNumber = $caseNumber;
            
            ob_start();
            include ROOT_PATH . '/app/Views/portal/application_success.php';
            $content = ob_get_clean();
            include ROOT_PATH . '/app/Views/layouts/auth.php';
            
        } catch (PDOException $e) {
            $errors['general'] = 'Помилка при створенні справи. Спробуйте пізніше.';
            
            $serviceModel = new Service();
            $services = $serviceModel->getActiveServices();
            
            $title = 'Подати заяву';
            
            ob_start();
            include ROOT_PATH . '/app/Views/portal/application_form.php';
            $content = ob_get_clean();
            include ROOT_PATH . '/app/Views/layouts/auth.php';
        }
    }
}
