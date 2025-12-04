<?php
/**
 * Модель для роботи з нотаріальними справами
 */

class NotarialCase extends BaseModel {
    protected $table = 'notarial_cases';
    protected $primaryKey = 'case_id';

    public function validate($data) {
        $errors = [];

        if (empty($data['client_id'])) {
            $errors['client_id'] = 'Клієнт є обов\'язковим';
        }

        if (empty($data['notary_id'])) {
            $errors['notary_id'] = 'Нотаріус є обов\'язковим';
        }

        if (empty($data['service_id'])) {
            $errors['service_id'] = 'Послуга є обов\'язковою';
        }

        if (empty($data['open_date'])) {
            $errors['open_date'] = 'Дата відкриття є обов\'язковою';
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $data['open_date']);
            if (!$date || $date > new DateTime()) {
                $errors['open_date'] = 'Дата відкриття не може бути в майбутньому';
            }
        }

        if (!empty($data['close_date'])) {
            $closeDate = DateTime::createFromFormat('Y-m-d', $data['close_date']);
            $openDate = DateTime::createFromFormat('Y-m-d', $data['open_date']);
            
            if ($closeDate < $openDate) {
                $errors['close_date'] = 'Дата закриття не може бути раніше дати відкриття';
            }
        }

        return $errors;
    }

    public function generateCaseNumber() {
        $year = date('Y');
        
        // Знайти максимальний номер справи за поточний рік
        $sql = "SELECT MAX(CAST(SUBSTRING(case_number, 6) AS UNSIGNED)) as max_num 
                FROM notarial_cases 
                WHERE case_number LIKE :pattern";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pattern' => $year . '-%']);
        $result = $stmt->fetch();
        
        $nextNum = ($result['max_num'] ?? 0) + 1;
        
        return sprintf('%s-%06d', $year, $nextNum);
    }

    public function getAllWithDetails($notaryId = null) {
        $sql = "SELECT 
                    nc.*,
                    CONCAT(c.last_name, ' ', c.first_name) as client_name,
                    CONCAT(n.last_name, ' ', n.first_name) as notary_name,
                    s.name as service_name,
                    s.base_price
                FROM notarial_cases nc
                INNER JOIN clients c ON nc.client_id = c.client_id
                INNER JOIN notaries n ON nc.notary_id = n.notary_id
                INNER JOIN services s ON nc.service_id = s.service_id";
        
        if ($notaryId) {
            $sql .= " WHERE nc.notary_id = :notary_id";
        }
        
        $sql .= " ORDER BY nc.open_date DESC, nc.case_id DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($notaryId) {
            $stmt->execute(['notary_id' => $notaryId]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }

    public function getByIdWithDetails($caseId) {
        $sql = "SELECT 
                    nc.*,
                    c.last_name as client_last_name,
                    c.first_name as client_first_name,
                    c.middle_name as client_middle_name,
                    c.tax_id as client_tax_id,
                    c.phone as client_phone,
                    c.email as client_email,
                    n.last_name as notary_last_name,
                    n.first_name as notary_first_name,
                    n.middle_name as notary_middle_name,
                    s.name as service_name,
                    s.base_price,
                    o.name as office_name
                FROM notarial_cases nc
                INNER JOIN clients c ON nc.client_id = c.client_id
                INNER JOIN notaries n ON nc.notary_id = n.notary_id
                INNER JOIN services s ON nc.service_id = s.service_id
                INNER JOIN offices o ON n.office_id = o.office_id
                WHERE nc.case_id = :case_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['case_id' => $caseId]);
        return $stmt->fetch();
    }

    public function changeStatus($caseId, $newStatus) {
        $allowedStatuses = ['open', 'in_progress', 'closed', 'cancelled'];
        
        if (!in_array($newStatus, $allowedStatuses)) {
            return false;
        }

        $data = ['status' => $newStatus];
        
        if ($newStatus === 'closed' || $newStatus === 'cancelled') {
            $data['close_date'] = date('Y-m-d');
        }

        $sql = "UPDATE notarial_cases SET status = :status";
        
        if (isset($data['close_date'])) {
            $sql .= ", close_date = :close_date";
        }
        
        $sql .= " WHERE case_id = :case_id";
        
        $data['case_id'] = $caseId;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getStatusLabel($status) {
        $labels = [
            'open' => 'Відкрита',
            'in_progress' => 'В роботі',
            'closed' => 'Закрита',
            'cancelled' => 'Скасована'
        ];
        
        return $labels[$status] ?? $status;
    }
}
