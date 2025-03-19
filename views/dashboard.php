<?php
require_once "../config/classDatabase.php";

try {
    $conn = Database::getInstance()->getConnection();

    // Prepare the query to get all attendance records for all scheduled classes
    $stmt = $conn->prepare("
        WITH RECURSIVE DateSeries AS (
    SELECT 
        c.class_id, 
        c.start_date AS class_date, 
        c.end_date, 
        WEEKDAY(c.start_date) AS weekday_num,
        CASE 
            WHEN c.first_day = 'Mon' THEN 0
            WHEN c.first_day = 'Tue' THEN 1
            WHEN c.first_day = 'Wed' THEN 2
            WHEN c.first_day = 'Thu' THEN 3
            WHEN c.first_day = 'Fri' THEN 4
            WHEN c.first_day = 'Sat' THEN 5
            WHEN c.first_day = 'Sun' THEN 6
        END AS first_day_num,
        CASE 
            WHEN c.last_day = 'Mon' THEN 0
            WHEN c.last_day = 'Tue' THEN 1
            WHEN c.last_day = 'Wed' THEN 2
            WHEN c.last_day = 'Thu' THEN 3
            WHEN c.last_day = 'Fri' THEN 4
            WHEN c.last_day = 'Sat' THEN 5
            WHEN c.last_day = 'Sun' THEN 6
        END AS last_day_num
    FROM class c
    UNION ALL
    -- Continue generating dates until reaching the end_date
    SELECT 
        ds.class_id, 
        DATE_ADD(ds.class_date, INTERVAL 1 DAY), 
        ds.end_date,
        WEEKDAY(DATE_ADD(ds.class_date, INTERVAL 1 DAY)),
        ds.first_day_num,
        ds.last_day_num
    FROM DateSeries ds
    WHERE ds.class_date < ds.end_date
)
SELECT 
    s.student_id,
    s.username,
    c.subject,
    c.first_day,
    c.last_day,
    COUNT(DISTINCT CASE WHEN sc.status = 'Present' THEN sc.date_taken END) AS present_days,
    COUNT(DISTINCT CASE WHEN WEEKDAY(ds.class_date) IN (ds.first_day_num, ds.last_day_num) THEN ds.class_date END) AS total_scheduled_days,
    ROUND(
        (COUNT(DISTINCT CASE WHEN sc.status = 'Present' THEN sc.date_taken END) / 
        NULLIF(COUNT(DISTINCT CASE WHEN WEEKDAY(ds.class_date) IN (ds.first_day_num, ds.last_day_num) THEN ds.class_date END), 0)) * 100, 
        2
    ) AS attendance_percentage
FROM students s
JOIN student_classes sc ON s.student_id = sc.student_id
JOIN class c ON sc.class_id = c.class_id
JOIN DateSeries ds ON ds.class_id = c.class_id 
WHERE WEEKDAY(ds.class_date) IN (ds.first_day_num, ds.last_day_num)
GROUP BY s.student_id, c.subject, c.first_day, c.last_day
ORDER BY s.student_id, c.subject;

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
                    <th>First Day</th>
                    <th>Last Day</th>
                    <th>Present Days</th>
                    <th>Total Scheduled Days</th>
                    <th>Attendance (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($students as $row) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['student_id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= htmlspecialchars($row['first_day']) ?></td>
                        <td><?= htmlspecialchars($row['last_day']) ?></td>
                        <td><?= htmlspecialchars($row['present_days']) ?></td>
                        <td><?= htmlspecialchars($row['total_scheduled_days']) ?></td>
                        <td
                            class="attendance-percentage <?= $row['attendance_percentage'] < 80 ? 'low-attendance' : 'high-attendance' ?>">
                            <?= $row['attendance_percentage'] !== null ? htmlspecialchars($row['attendance_percentage']) . '%' : 'N/A' ?>
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