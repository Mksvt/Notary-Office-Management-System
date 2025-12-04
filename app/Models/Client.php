<?php
/**
 * Модель для роботи з клієнтами
 */

class Client extends BaseModel {
    protected $table = 'clients';

    public function validate($data, $isUpdate = false, $clientId = null) {
        $errors = [];

        // Обов'язкові поля
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Прізвище є обов\'язковим';
        } elseif (mb_strlen($data['last_name']) > 50) {
            $errors['last_name'] = 'Прізвище не може бути довшим за 50 символів';
        }

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'Ім\'я є обов\'язковим';
        } elseif (mb_strlen($data['first_name']) > 50) {
            $errors['first_name'] = 'Ім\'я не може бути довшим за 50 символів';
        }

        // Перевірка ІПН
        if (!empty($data['tax_id'])) {
            if (!preg_match('/^\d{10}$/', $data['tax_id'])) {
                $errors['tax_id'] = 'ІПН повинен містити 10 цифр';
            } else {
                // Перевірка унікальності
                $existing = $this->findByTaxId($data['tax_id']);
                if ($existing && (!$isUpdate || $existing['client_id'] != $clientId)) {
                    $errors['tax_id'] = 'Клієнт з таким ІПН вже існує';
                }
            }
        }

        // Перевірка паспорта
        if (!empty($data['passport_series']) && !empty($data['passport_number'])) {
            $existing = $this->findByPassport($data['passport_series'], $data['passport_number']);
            if ($existing && (!$isUpdate || $existing['client_id'] != $clientId)) {
                $errors['passport'] = 'Клієнт з таким паспортом вже існує';
            }
        }

        // Перевірка email
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Некоректний формат email';
            }
        }

        // Перевірка дати народження
        if (!empty($data['birth_date'])) {
            $date = DateTime::createFromFormat('Y-m-d', $data['birth_date']);
            if (!$date || $date->format('Y-m-d') !== $data['birth_date']) {
                $errors['birth_date'] = 'Некоректна дата народження';
            } elseif ($date > new DateTime()) {
                $errors['birth_date'] = 'Дата народження не може бути в майбутньому';
            }
        }

        return $errors;
    }

    public function findByTaxId($taxId) {
        $sql = "SELECT * FROM clients WHERE tax_id = :tax_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tax_id' => $taxId]);
        return $stmt->fetch();
    }

    public function findByPassport($series, $number) {
        $sql = "SELECT * FROM clients WHERE passport_series = :series AND passport_number = :number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['series' => $series, 'number' => $number]);
        return $stmt->fetch();
    }

    public function search($query) {
        $searchTerm = "%{$query}%";
        $sql = "SELECT * FROM clients WHERE 
                last_name LIKE :query1 OR 
                first_name LIKE :query2 OR 
                middle_name LIKE :query3 OR 
                tax_id LIKE :query4 OR 
                phone LIKE :query5 
                ORDER BY last_name, first_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'query1' => $searchTerm,
            'query2' => $searchTerm,
            'query3' => $searchTerm,
            'query4' => $searchTerm,
            'query5' => $searchTerm
        ]);
        return $stmt->fetchAll();
    }

    public function getCasesCount($clientId) {
        $sql = "SELECT COUNT(*) as total FROM notarial_cases WHERE client_id = :client_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['client_id' => $clientId]);
        $result = $stmt->fetch();
        return $result['total'];
    }
}
