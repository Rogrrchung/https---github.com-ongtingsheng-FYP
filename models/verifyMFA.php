<?php
session_start();
require_once '../config/classDatabase.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['student_id'])) {
        $_SESSION['message'] = ['text' => "You must log in first.", 'type' => "error"];
        header("Location: ../views/mfa.php");
        exit();
    }

    $student_id = $_SESSION['student_id'];
    $pin = trim($_POST['pin']);
    $class_id = trim($_POST['class_id']);

    if (empty($pin) || empty($class_id)) {
        $_SESSION['message'] = ['text' => "Invalid request. Make sure all fields are filled.", 'type' => "error"];
        header("Location: ../views/mfa.php");
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();

        // Fetch class details
        $stmt = $db->prepare("SELECT pin_code, start_time, end_time, first_day, last_day FROM class WHERE class_id = :class_id LIMIT 1");
        $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $stmt->execute();
        $class = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$class) {
            $_SESSION['message'] = ['text' => "Class not found.", 'type' => "error"];
            header("Location: ../views/mfa.php");
            exit();
        }

        // Validate PIN
        if ($class['pin_code'] !== $pin) {
            $_SESSION['message'] = ['text' => "Invalid PIN. Please check and try again.", 'type' => "warning"];
            header("Location: ../views/mfa.php");
            exit();
        }

        // Ensure timezone is set correctly
        date_default_timezone_set('Asia/Kuala_Lumpur');

        // Get current time and day
        $current_time = date("H:i:s");
        $current_day = strtolower(date("D")); // Example: "fri"

        // Convert stored first_day and last_day to lowercase 3-letter format
        $first_day = strtolower(substr($class['first_day'], 0, 3));
        $last_day = strtolower(substr($class['last_day'], 0, 3));

        // Check if today is within the class schedule
        $allowed_days = [$first_day, $last_day];
        if (!in_array($current_day, $allowed_days)) {
            $_SESSION['message'] = ['text' => "Attendance is not allowed today. Your class is scheduled for <b>" . ucfirst($first_day) . " & " . ucfirst($last_day) . "</b>.", 'type' => "error"];
            header("Location: ../views/mfa.php");
            exit();
        }

        // Check if current time is within class time
        if ($current_time < $class['start_time'] || $current_time > $class['end_time']) {
            $_SESSION['message'] = ['text' => "Attendance can only be taken between <b>" . $class['start_time'] . "</b> and <b>" . $class['end_time'] . "</b>.", 'type' => "warning"];
            header("Location: ../views/mfa.php");
            exit();
        }

        // Check attendance record
        $stmt = $db->prepare("SELECT status FROM student_classes WHERE student_id = :student_id AND class_id = :class_id");
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $stmt->execute();
        $attendanceStatus = $stmt->fetchColumn();

        if ($attendanceStatus === 'present') {
            $_SESSION['message'] = ['text' => "✅ Attendance already marked for today. No further action needed.", 'type' => "success"];
        } elseif ($attendanceStatus === 'absent') {
            $stmt = $db->prepare("UPDATE student_classes SET status = 'present' WHERE student_id = :student_id AND class_id = :class_id");
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['message'] = ['text' => "🎉 Attendance successfully updated from <b>absent</b> to <b>present</b>.", 'type' => "success"];
        } else {
            $_SESSION['message'] = ['text' => "⚠️ No attendance record found. Please contact your lecturer.", 'type' => "error"];
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = ['text' => "Database error: " . $e->getMessage(), 'type' => "error"];
    }

    header("Location: ../views/mfa.php");
    exit();
}
?>
