<?php
require_once '../models/studentClass.php';
require_once '../config/classDatabase.php';
require_once '../models/ClassModel.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$classModel = new ClassModel();

// Get class ID from request
$class_id = $_GET['class_id'] ?? null;

$classDetails = [];
$class_name = "Unknown";
$subject = "Unknown";
$attendanceRecords = [];

if ($class_id) {
    // Fetch class details
    $classDetails = $classModel->getClassById($class_id);
    $class_name = $classDetails['class_name'] ?? 'Unknown';
    $subject = $classDetails['subject'] ?? 'Unknown';

    // Fetch attendance records for this class
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT s.student_id, s.username, sc.status
                           FROM student_classes sc
                           JOIN students s ON sc.student_id = s.student_id
                           WHERE sc.class_id = ?");
    $stmt->execute([$class_id]);
    $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Header Styles
$boldStyle = [
    'font' => ['bold' => true, 'size' => 14],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]
];

// Set Class Details
$sheet->setCellValue('A1', "Class ID: " . $class_id);
$sheet->setCellValue('A2', "Class Name: " . $class_name);
$sheet->setCellValue('A3', "Subject: " . $subject);

// Apply Styles to Class Details
$sheet->getStyle('A1:A3')->applyFromArray($boldStyle);

// Set Table Headers (Skipping Date)
$headers = ['Student ID', 'Student Name', 'Status'];
$sheet->fromArray($headers, null, 'A5');

// Apply Bold Header Style
$sheet->getStyle('A5:C5')->applyFromArray($boldStyle);

// Fill Attendance Data
$row = 6;
foreach ($attendanceRecords as $record) {
    $sheet->setCellValue('A' . $row, $record['student_id']);
    $sheet->setCellValue('B' . $row, $record['username']);
    $sheet->setCellValue('C' . $row, $record['status']);
    $row++;
}

// Auto-size Columns for Better Readability
foreach (range('A', 'C') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set Excel Headers for Download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Attendance_' . $class_name . '.xlsx"');
header('Cache-Control: max-age=0');

// Save & Output Excel File
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>