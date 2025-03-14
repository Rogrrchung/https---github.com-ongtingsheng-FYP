<?php
require_once '../models/classModel.php';

$classModel = new ClassModel();

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $classModel->addClass($_POST['class_name'], $_POST['subject'], $_POST['subject_code'], $_POST['capacity'], $_POST['total_classes'], $_POST['first_day'], $_POST['last_day'], $_POST['start_time'], $_POST['end_time'], $_POST['status']);
    } elseif ($action === 'edit') {
        // Check if status is set; otherwise, fetch existing status
        if (!isset($_POST['status'])) {
            $existingClass = $classModel->getClassById($_POST['class_id']); // Fetch existing data
            $status = $existingClass['status']; // Use the original status
        } else {
            $status = $_POST['status'];
        }
        $classModel->updateClass($_POST['class_id'], $_POST['class_name'], $_POST['subject'], $_POST['subject_code'], $_POST['capacity'], $_POST['total_classes'], $_POST['first_day'], $_POST['last_day'], $_POST['start_time'], $_POST['end_time'], $status);
    } elseif ($action === 'delete') {
        $classModel->deleteClass($_POST['class_id']);
    }

    header("Location: classManage.php");
    exit();
}
