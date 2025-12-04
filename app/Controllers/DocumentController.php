<?php
/**
 * Контролер для роботи з документами
 */

require_once ROOT_PATH . '/app/Models/Document.php';
require_once ROOT_PATH . '/app/Models/NotarialCase.php';

class DocumentController {
    public function index($case_id) {
        requireAuth();

        $caseModel = new NotarialCase();
        $case = $caseModel->findById($case_id);

        if (!$case) {
            setFlashMessage('Справу не знайдено', 'error');
            redirect('/cases');
        }

        $documentModel = new Document();
        $documents = $documentModel->getByCaseId($case_id);

        $title = 'Документи справи №' . $case['case_number'];
        $flash = getFlashMessage();
        
        ob_start();
        include ROOT_PATH . '/app/Views/documents/index.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function uploadForm($case_id) {
        requireAuth();

        $caseModel = new NotarialCase();
        $case = $caseModel->getByIdWithDetails($case_id);

        if (!$case) {
            setFlashMessage('Справу не знайдено', 'error');
            redirect('/cases');
        }

        // Перевірка доступу для нотаріуса
        if ($_SESSION['user_role'] === 'notary' && $case['notary_id'] != $_SESSION['related_id']) {
            die('Доступ заборонено');
        }

        $title = 'Завантаження документа';
        $errors = [];
        $data = ['case_id' => $case_id];
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/documents/upload.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function upload($case_id) {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/cases/' . $case_id . '/documents');
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

        // Перевірка доступу для нотаріуса
        if ($_SESSION['user_role'] === 'notary' && $case['notary_id'] != $_SESSION['related_id']) {
            die('Доступ заборонено');
        }

        $data = [
            'case_id' => $case_id,
            'doc_type' => trim($_POST['doc_type'] ?? ''),
            'doc_number' => trim($_POST['doc_number'] ?? ''),
            'issue_date' => $_POST['issue_date'] ?? null,
            'expiry_date' => $_POST['expiry_date'] ?? null,
            'is_original' => isset($_POST['is_original']) ? 1 : 0
        ];

        $file = $_FILES['document'] ?? null;

        $documentModel = new Document();
        $errors = $documentModel->validate($data, $file);

        if (empty($errors)) {
            // Завантажити файл
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $filePath = $documentModel->uploadFile($file, $case_id);
                
                if ($filePath) {
                    $data['file_path'] = $filePath;
                    
                    if ($documentModel->create($data)) {
                        setFlashMessage('Документ успішно завантажено', 'success');
                        redirect('/cases/view/' . $case_id);
                    } else {
                        $errors['general'] = 'Помилка при збереженні документа';
                    }
                } else {
                    $errors['file'] = 'Помилка при завантаженні файлу';
                }
            } else {
                $errors['file'] = 'Файл не вибрано';
            }
        }

        $title = 'Завантаження документа';
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include ROOT_PATH . '/app/Views/documents/upload.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/main.php';
    }

    public function download($id) {
        requireAuth();

        $documentModel = new Document();
        $document = $documentModel->findById($id);

        if (!$document) {
            setFlashMessage('Документ не знайдено', 'error');
            redirect('/cases');
        }

        $filePath = $documentModel->getFullPath($document['file_path']);

        if (!file_exists($filePath)) {
            setFlashMessage('Файл не знайдено', 'error');
            redirect('/cases');
        }

        // Відправити файл на завантаження
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
