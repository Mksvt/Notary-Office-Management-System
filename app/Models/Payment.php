<?php
/**
 * Модель для роботи з платежами
 */

class Payment extends BaseModel {
    protected $table = 'payments';

    public function validate($data) {
        $errors = [];

        if (empty($data['case_id'])) {
            $errors['case_id'] = 'ID справи є обов\'язковим';
        }

        if (empty($data['amount']) || $data['amount'] <= 0) {
            $errors['amount'] = 'Сума повинна бути більше 0';
        }

        if (empty($data['payment_date'])) {
            $errors['payment_date'] = 'Дата платежу є обов\'язковою';
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $data['payment_date']);
            if (!$date || $date > new DateTime()) {
                $errors['payment_date'] = 'Дата платежу не може бути в майбутньому';
            }
        }

        if (empty($data['method'])) {
            $errors['method'] = 'Метод платежу є обов\'язковим';
        }

        return $errors;
    }

    public function getAllWithDetails() {
        $sql = "SELECT 
                    p.*,
                    nc.case_number,
                    CONCAT(c.last_name, ' ', c.first_name) as client_name,
                    s.name as service_name
                FROM payments p
                INNER JOIN notarial_cases nc ON p.case_id = nc.case_id
                INNER JOIN clients c ON nc.client_id = c.client_id
                INNER JOIN services s ON nc.service_id = s.service_id
                ORDER BY p.payment_date DESC, p.payment_id DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getByCaseId($caseId) {
        $sql = "SELECT * FROM payments WHERE case_id = :case_id ORDER BY payment_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['case_id' => $caseId]);
        return $stmt->fetchAll();
    }

    public function getByIdWithDetails($paymentId) {
        $sql = "SELECT 
                    p.*,
                    nc.case_number,
                    CONCAT(c.last_name, ' ', c.first_name, ' ', COALESCE(c.middle_name, '')) as client_name,
                    s.name as service_name,
                    o.name as office_name,
                    o.address as office_address
                FROM payments p
                INNER JOIN notarial_cases nc ON p.case_id = nc.case_id
                INNER JOIN clients c ON nc.client_id = c.client_id
                INNER JOIN services s ON nc.service_id = s.service_id
                INNER JOIN notaries n ON nc.notary_id = n.notary_id
                INNER JOIN offices o ON n.office_id = o.office_id
                WHERE p.payment_id = :payment_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['payment_id' => $paymentId]);
        return $stmt->fetch();
    }

    public function getPaymentsByNotary($notaryId) {
        $sql = "SELECT 
                    p.*,
                    nc.case_number,
                    CONCAT(c.last_name, ' ', c.first_name) as client_name,
                    s.name as service_name
                FROM payments p
                INNER JOIN notarial_cases nc ON p.case_id = nc.case_id
                INNER JOIN clients c ON nc.client_id = c.client_id
                INNER JOIN services s ON nc.service_id = s.service_id
                WHERE nc.notary_id = :notary_id
                ORDER BY p.payment_date DESC, p.payment_id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['notary_id' => $notaryId]);
        return $stmt->fetchAll();
    }

    public function getStatusLabel($status) {
        $labels = [
            'pending' => 'Очікується',
            'paid' => 'Оплачено',
            'cancelled' => 'Скасовано',
            'refunded' => 'Повернено'
        ];
        
        return $labels[$status] ?? $status;
    }

    public function getMethodLabel($method) {
        $labels = [
            'cash' => 'Готівка',
            'card' => 'Картка',
            'bank_transfer' => 'Банківський переказ',
            'other' => 'Інше'
        ];
        
        return $labels[$method] ?? $method;
    }

    public function getTotalPaid($startDate = null, $endDate = null) {
        $sql = "SELECT SUM(amount) as total FROM payments WHERE status = 'paid'";
        $params = [];
        
        if ($startDate) {
            $sql .= " AND payment_date >= :start_date";
            $params['start_date'] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND payment_date <= :end_date";
            $params['end_date'] = $endDate;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }
}
