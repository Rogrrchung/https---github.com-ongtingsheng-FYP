<?php
session_start();
require_once '../models/studentClass.php'; // Include your StudentClass model

$class_id = $_GET['class_id']; // Get class_id from the URL

$studentClass = new StudentClass();

// Handle marking attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $student_id = $_POST['student_id'];
    $status = $_POST['status'];
    $studentClass->markAttendance($student_id, $status, $class_id); // Mark attendance
    header("Location: attendanceRecord.php?class_id=$class_id"); // Refresh the page
    exit();
}

// Handle PIN generation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_pin'])) {
    $generatedPin = $studentClass->generatePinCode($class_id);
}


// Fetch attendance records
$attendanceRecords = $studentClass->getAttendanceRecords($class_id);

// Fetch all students in the class
$students = $studentClass->getStudentsByClass($class_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link rel="stylesheet" href="../public/css/attendanceRecord.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Font Awesome -->
</head>
<style>
    .export-btn {
        background-color: #28a745;
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 5px;
        display: inline-block;
        margin-bottom: 15px;
    }

    .export-btn:hover {
        background-color: #218838;
    }
</style>

<body>
    <div class="container">
        <h1 class="header">Attendance <span class="highlight">Records</span></h1>

        <!-- Back Button -->
        <a href="classManage.php" class="btn back-btn">
            <i class="fas fa-arrow-left"></i> Back to Classes
        </a>
        <!-- Export to Excel Button -->
        <a href="exportAttendance.php?class_id=<?= $class_id; ?>" class="btn export-btn">
            <i class="fas fa-file-excel"></i> Export to Excel
        </a>
        <!-- Generate PIN Code Button -->
        <form method="POST" action="">
            <button type="submit" name="generate_pin" class="btn generate-btn">
                <i class="fas fa-key"></i> Generate PIN
            </button>
        </form>

        <!-- Display the Generated PIN -->
        <?php if (isset($generatedPin)): ?>
            <p class="pin-display">Generated PIN: <strong><?= $generatedPin; ?></strong></p>
        <?php else: ?>
            <p class="pin-display">Current PIN: <strong><?= $studentClass->getPinCode($class_id); ?></strong></p>
        <?php endif; ?>

        <!-- Attendance Records Table -->
        <table class="schedule-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <!-- <th>Date</th> -->
                    <th>Status</th>
                    <!-- <th>Time Marked</th> -->
                    <th>Action</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendanceRecords as $record): ?>
                    <tr>
                        <td><?= $record['student_id']; ?></td>
                        <td><?= htmlspecialchars($record['student_name']); ?></td>
                        <td>
                            <span
                                class="status <?= strcasecmp($record['status'], 'Present') === 0 ? 'active' : 'inactive'; ?>">
                                <?= $record['status']; ?>
                            </span>
                        </td>
                        <td>
                            <!-- Form for marking Present -->
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="student_id" value="<?= $record['student_id']; ?>">
                                <input type="hidden" name="status" value="Present">
                                <button type="submit" name="mark_attendance" class="action-btn present-btn">
                                    <i class="fas fa-check"></i>Present
                                </button>
                            </form>

                            <!-- Form for marking Absent -->
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="student_id" value="<?= $record['student_id']; ?>">
                                <input type="hidden" name="status" value="Absent">
                                <button type="submit" name="mark_attendance" class="action-btn absent-btn">
                                    <i class="fas fa-times"></i>Absent
                                </button>
                            </form>
                        </td>
                        <td>
                            <!-- Form for removing the student -->
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="student_id" value="<?= $record['student_id']; ?>">
                                <input type="hidden" name="remove_student" value="1">
                                <button type="submit" name="remove_student_btn" class="action-btn remove-btn">
                                    <i class="fas fa-trash"></i>Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>