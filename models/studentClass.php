<?php
require_once '../config/classDatabase.php';

class StudentClass
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Add students to class, setting status as 'absent'
    public function addStudentsToClass($student_ids, $class_id)
    {
        if (empty($student_ids) || empty($class_id)) {
            throw new Exception("Student IDs or Class ID is empty.");
        }

        $stmt = $this->pdo->prepare("INSERT INTO student_classes (student_id, class_id, status) VALUES (:student_id, :class_id, 'absent')");

        foreach ($student_ids as $student_id) {
            try {
                $stmt->execute([
                    ':student_id' => $student_id,
                    ':class_id' => $class_id
                ]);
                error_log("Student $student_id added to class $class_id.");
            } catch (PDOException $e) {
                // Log the error or handle it
                error_log("Error adding student $student_id to class $class_id: " . $e->getMessage());
            }
        }
    }


    public function getClassCapacity($class_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT capacity, (SELECT COUNT(*) FROM student_classes WHERE class_id = ?) AS current_students
            FROM class
            WHERE class_id = ?
        ");
        $stmt->execute([$class_id, $class_id]);
        return $stmt->fetch();
    }

    public function getAttendanceRecords($class_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT sc.student_id, s.username AS student_name, sc.status
            FROM student_classes sc
            JOIN students s ON sc.student_id = s.student_id
            WHERE sc.class_id = :class_id
        ");
        $stmt->execute([':class_id' => $class_id]);
        return $stmt->fetchAll();
    }

    public function markAttendance($student_id, $status, $class_id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE student_classes
            SET status = :status
            WHERE student_id = :student_id AND class_id = :class_id 
        ");
        $stmt->execute([
            ':student_id' => $student_id,
            ':class_id' => $class_id,
            ':status' => $status,
        ]);
    }

    // Get students already in the class
    public function getStudentsByClass($class_id)
    {
        $stmt = $this->pdo->prepare("SELECT student_id FROM student_classes WHERE class_id = ?");
        $stmt->execute([$class_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Generate a 6-digit random PIN and store it in the class table
    public function generatePinCode($class_id)
    {
        try {
            // Check if the class already has a PIN
            $stmt = $this->pdo->prepare("SELECT pin_code FROM class WHERE class_id = :class_id");
            $stmt->execute([':class_id' => $class_id]);
            $existingPin = $stmt->fetchColumn();

            if ($existingPin) {
                return $existingPin; // Return existing PIN if already generated
            }

            // Generate a random 6-digit PIN
            $pinCode = mt_rand(100000, 999999);

            // Save the PIN to the class table
            $stmt = $this->pdo->prepare("UPDATE class SET pin_code = :pin_code WHERE class_id = :class_id");
            $stmt->execute([':pin_code' => $pinCode, ':class_id' => $class_id]);

            return $pinCode;
        } catch (PDOException $e) {
            error_log("Error generating PIN: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve the generated PIN for a class
    public function getPinCode($class_id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT pin_code FROM class WHERE class_id = :class_id");
            $stmt->execute([':class_id' => $class_id]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error retrieving PIN: " . $e->getMessage());
            return false;
        }
    }
}
?>