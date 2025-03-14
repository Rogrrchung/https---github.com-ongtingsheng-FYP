<?php
session_start();
require_once '../config/classDatabase.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$pin_code = rand(100000, 999999);
$expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Delete old PIN if exists
    $stmt = $conn->prepare("DELETE FROM attendance_pin WHERE student_id = ?");
    $stmt->execute([$student_id]);

    // Insert new PIN
    $stmt = $conn->prepare("INSERT INTO attendance_pin (student_id, pin_code, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$student_id, $pin_code, $expires_at]);

    $_SESSION['generated_pin'] = $pin_code; // Store PIN in session for displaying

    header("Location: mfa.php"); // Redirect back to the form
    exit;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
