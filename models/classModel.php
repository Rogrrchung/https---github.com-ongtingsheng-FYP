<?php
require_once '../config/classDatabase.php';

class ClassModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Fetch all classes 
    public function getAllClasses()
    {
        $sql = "SELECT c.class_id, c.class_name, c.subject, c.subject_code, c.capacity, c.total_classes, c.first_day, c.last_day, c.start_time, c.end_time,
                       (SELECT COUNT(*) FROM student_classes sc WHERE sc.class_id = c.class_id) AS students_assigned, 
                       CASE WHEN c.status = 1 THEN 'Active' ELSE 'Inactive' END AS status
                       FROM class c";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    

    // Get a single class by ID
    public function getClassById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM class WHERE class_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Add a new class
    public function addClass($class_name, $subject, $subject_code, $capacity, $total_classes, $first_day, $last_day, $start_time, $end_time, $status)
    {
        $stmt = $this->pdo->prepare("INSERT INTO class (class_name, subject, subject_code, capacity, total_classes, first_day, last_day, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$class_name, $subject, $subject_code, $capacity, $total_classes, $first_day, $last_day, $start_time, $end_time, $status]);
    }

    // Update a class
    public function updateClass($class_id, $class_name, $subject, $subject_code, $capacity, $total_classes, $first_day, $last_day, $start_time, $end_time, $status = null)
    {
        // Fetch the existing status if no new status is provided
        if ($status === null) {
            $stmt = $this->pdo->prepare("SELECT status FROM class WHERE class_id = ?");
            $stmt->execute([$class_id]);
            $existingClass = $stmt->fetch(PDO::FETCH_ASSOC);
            $status = $existingClass['status']; // Keep the original status
        }

        // Update the class with the correct status
        $stmt = $this->pdo->prepare("UPDATE class 
                                 SET class_name = ?, subject = ?, subject_code = ?, capacity = ?, total_classes = ?,
                                     first_day = ?, last_day = ?, start_time = ?, end_time = ?, status = ? 
                                 WHERE class_id = ?");
        return $stmt->execute([$class_name, $subject, $subject_code, $capacity, $total_classes, $first_day, $last_day, $start_time, $end_time, $status, $class_id]);
    }


    // Delete a class
    public function deleteClass($class_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM class WHERE class_id = ?");
        return $stmt->execute([$class_id]);
    }
}
