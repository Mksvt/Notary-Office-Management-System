<?php
/**
 * Модель для роботи з послугами
 */

class Service extends BaseModel {
    protected $table = 'services';

    public function validate($data) {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Назва послуги є обов\'язковою';
        }

        if (!isset($data['base_price']) || $data['base_price'] < 0) {
            $errors['base_price'] = 'Базова ціна повинна бути >= 0';
        }

        return $errors;
    }

    public function getActiveServices() {
        $sql = "SELECT * FROM services WHERE is_active = 1 ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function deactivate($id) {
        $sql = "UPDATE services SET is_active = 0 WHERE service_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function activate($id) {
        $sql = "UPDATE services SET is_active = 1 WHERE service_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
