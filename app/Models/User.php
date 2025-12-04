<?php
/**
 * Модель для роботи з користувачами (для авторизації)
 */

class User extends BaseModel {
    protected $table = 'users';

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function createUser($username, $password, $role, $relatedId = null) {
        $data = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'related_id' => $relatedId,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->create($data);
    }
}
