<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];

    if (empty($student_id)) {
        die("Please enter a student ID.");
    }

    // Simulate student login (In real use, verify from database)
    $_SESSION['student_id'] = $student_id;

    echo "Logged in as Student ID: " . $_SESSION['student_id'];
    echo "<br><a href='mfa.php'>Go to Attendance Page</a>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Student Login</title>
</head>
<body>
    <h2>Test Student Login</h2>
    <form method="POST">
        <label>Student ID:</label>
        <input type="text" name="student_id" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
