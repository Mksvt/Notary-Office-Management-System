<?php
/**
 * Модель для роботи з документами
 */

class Document extends BaseModel {
    protected $table = 'documents';

    public function validate($data, $file = null) {
        $errors = [];

        if (empty($data['case_id'])) {
            $errors['case_id'] = 'ID справи є обов\'язковим';
        }

        if (empty($data['doc_type'])) {
            $errors['doc_type'] = 'Тип документа є обов\'язковим';
        }

        // Перевірка файлу
        if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors['file'] = 'Помилка завантаження файлу';
            } else {
                $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if (!in_array($fileExt, ALLOWED_FILE_TYPES)) {
                    $errors['file'] = 'Недозволений тип файлу. Дозволені: ' . implode(', ', ALLOWED_FILE_TYPES);
                }
                
                if ($file['size'] > MAX_FILE_SIZE) {
                    $errors['file'] = 'Файл занадто великий. Максимальний розмір: ' . (MAX_FILE_SIZE / 1024 / 1024) . ' MB';
                }
            }
        }

        return $errors;
    }

    public function uploadFile($file, $caseId) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Створити директорію для файлів
        $year = date('Y');
        $month = date('m');
        $uploadDir = UPLOAD_PATH . "/{$year}/{$month}";
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Генерувати унікальне ім'я файлу
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = uniqid('doc_') . '_' . $caseId . '.' . $fileExt;
        $filePath = $uploadDir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Повернути відносний шлях для збереження в БД
            return "documents/{$year}/{$month}/{$fileName}";
        }

        return false;
    }

    public function getByCaseId($caseId) {
        $sql = "SELECT * FROM documents WHERE case_id = :case_id ORDER BY document_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['case_id' => $caseId]);
        return $stmt->fetchAll();
    }

    public function getFullPath($relativePath) {
        return ROOT_PATH . '/public/uploads/' . $relativePath;
    }
}
