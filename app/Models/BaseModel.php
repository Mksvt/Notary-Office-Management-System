<?php
/**
 * Базовий клас для всіх моделей
 */

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        
        // Auto-detect primary key if not set
        if (!$this->primaryKey) {
            $this->primaryKey = $this->getPrimaryKeyName();
        }
    }

    /**
     * Get primary key name based on table name
     */
    protected function getPrimaryKeyName() {
        // Handle special table names
        $specialCases = [
            'notarial_cases' => 'case_id',
            'users' => 'user_id',
            'documents' => 'document_id',
            'payments' => 'payment_id',
            'client_applications' => 'application_id'
        ];
        
        if (isset($specialCases[$this->table])) {
            return $specialCases[$this->table];
        }
        
        // Remove trailing 's' if present and add '_id'
        // clients -> client_id, notaries -> notary_id, offices -> office_id
        $singular = rtrim($this->table, 's');
        
        // Handle special cases for -ies ending (notaries -> notary)
        if (substr($this->table, -3) === 'ies') {
            $singular = substr($this->table, 0, -3) . 'y';
        }
        
        return $singular . '_id';
    }

    /**
     * Отримати всі записи
     */
    public function findAll($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Знайти запис за ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Створити новий запис
     */
    public function create($data) {
        $fields = array_keys($data);
        $values = ':' . implode(', :', $fields);
        $fields = implode(', ', $fields);
        
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$values})";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Оновити запис
     */
    public function update($id, $data) {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
        }
        $fields = implode(', ', $fields);
        
        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Видалити запис
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Пошук за критеріями
     */
    public function findWhere($conditions, $orderBy = null, $limit = null) {
        $where = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $where[] = "{$field} = :{$field}";
            $params[$field] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Підрахунок записів
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if (!empty($conditions)) {
            $where = [];
            $params = [];
            
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            
            $sql .= " WHERE " . implode(' AND ', $where);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
        } else {
            $stmt = $this->db->query($sql);
        }
        
        $result = $stmt->fetch();
        return $result['total'];
    }
}
