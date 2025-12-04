<?php
/**
 * Модель для роботи з офісами
 */

class Office extends BaseModel {
    protected $table = 'offices';

    public function validate($data) {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Назва офісу є обов\'язковою';
        }

        if (empty($data['address'])) {
            $errors['address'] = 'Адреса є обов\'язковою';
        }

        if (empty($data['city'])) {
            $errors['city'] = 'Місто є обов\'язковим';
        }

        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Некоректний формат email';
            }
        }

        return $errors;
    }

    public function getNotariesCount($officeId) {
        $sql = "SELECT COUNT(*) as total FROM notaries WHERE office_id = :office_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['office_id' => $officeId]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getCasesCount($officeId) {
        $sql = "SELECT COUNT(*) as total FROM notarial_cases nc 
                INNER JOIN notaries n ON nc.notary_id = n.notary_id 
                WHERE n.office_id = :office_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['office_id' => $officeId]);
        $result = $stmt->fetch();
        return $result['total'];
    }
}
