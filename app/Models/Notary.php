<?php
/**
 * Модель для роботи з нотаріусами
 */

class Notary extends BaseModel {
    protected $table = 'notaries';

    public function validate($data, $isUpdate = false, $notaryId = null) {
        $errors = [];

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Прізвище є обов\'язковим';
        }

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'Ім\'я є обов\'язковим';
        }

        if (empty($data['license_number'])) {
            $errors['license_number'] = 'Номер ліцензії є обов\'язковим';
        } else {
            $existing = $this->findByLicense($data['license_number']);
            if ($existing && (!$isUpdate || $existing['notary_id'] != $notaryId)) {
                $errors['license_number'] = 'Нотаріус з таким номером ліцензії вже існує';
            }
        }

        if (empty($data['office_id'])) {
            $errors['office_id'] = 'Офіс є обов\'язковим';
        }

        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Некоректний формат email';
            }
        }

        return $errors;
    }

    public function findByLicense($licenseNumber) {
        $sql = "SELECT * FROM notaries WHERE license_number = :license";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['license' => $licenseNumber]);
        return $stmt->fetch();
    }

    public function getAllWithOffices() {
        $sql = "SELECT n.*, o.name as office_name 
                FROM notaries n 
                LEFT JOIN offices o ON n.office_id = o.office_id 
                ORDER BY n.last_name, n.first_name";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getActiveNotaries() {
        $sql = "SELECT n.*, o.name as office_name 
                FROM notaries n 
                LEFT JOIN offices o ON n.office_id = o.office_id 
                WHERE n.is_active = 1 
                ORDER BY n.last_name, n.first_name";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function deactivate($id) {
        $sql = "UPDATE notaries SET is_active = 0 WHERE notary_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function activate($id) {
        $sql = "UPDATE notaries SET is_active = 1 WHERE notary_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
