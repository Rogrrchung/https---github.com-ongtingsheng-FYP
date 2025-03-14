<?php
require_once "../config/classDatabase.php";

try {
    $conn = Database::getInstance()->getConnection();

    // Prepare the query
    $stmt = $conn->prepare("
        SELECT 
            s.student_id,
            s.username,
            c.subject,
            COUNT(CASE WHEN sc.status = 'Present' THEN 1 END) AS present_days,
            c.total_classes,
            ROUND((COUNT(CASE WHEN sc.status = 'Present' THEN 1 END) / c.total_classes) * 100, 2) AS attendance_percentage
        FROM students s
        JOIN student_classes sc ON s.student_id = sc.student_id
        JOIN class c ON sc.class_id = c.class_id
        GROUP BY s.student_id, c.subject, c.total_classes
        ORDER BY s.student_id, c.subject
    ");

    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../public/css/dashboard.css">
    <link rel="stylesheet" href="../public/css/nav.css">
    <title>Student Attendance Report</title>

</head>

<body>
    <nav class="navbar">
        <div class="nav-logo">
            <h2>EasyClass</h2>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classManage.php" class="active"><i class="fas fa-chalkboard-teacher"></i> Classes</a></li>
            <li><a href="#"><i class="fas fa-user"></i> User</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2>📊 Student Attendance Report</h2>

        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Present Days</th>
                    <th>Total Classes</th>
                    <th>Attendance (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $current_student = null;
                foreach ($students as $row) {
                    if ($current_student !== $row['student_id']) {
                        $current_student = $row['student_id'];
                        echo "<tr class='student-row'>";
                        echo "<td>{$row['student_id']}</td>";
                        echo "<td>{$row['username']}</td>";
                        echo "<td colspan='4'></td>";
                        echo "</tr>";
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= htmlspecialchars($row['present_days']) ?></td>
                        <td><?= htmlspecialchars($row['total_classes']) ?></td>
                        <td
                            class="attendance-percentage <?= $row['attendance_percentage'] < 80 ? 'low-attendance' : 'high-attendance' ?>">
                            <?= htmlspecialchars($row['attendance_percentage']) ?>%
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="container-footer">
            <p>⚡ Note: Attendance below 80% is marked in <span style="color:red;">red</span>.</p>
        </div>
    </div>

</body>

</html>