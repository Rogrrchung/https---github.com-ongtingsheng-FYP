<?php
require_once '../config/classDatabase.php';

class Student {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAllStudents() {
        $stmt = $this->pdo->prepare("SELECT student_id, username, programme FROM students");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
