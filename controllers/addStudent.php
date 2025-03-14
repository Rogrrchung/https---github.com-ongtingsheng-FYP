<?php
error_log(print_r($_POST, true)); // Debugging: Log the POST data

require_once '../models/studentClass.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['student_ids']) && isset($_POST['class_id'])) {
    $student_ids = $_POST['student_ids'];
    $class_id = $_POST['class_id'];

    // Debugging: Check if class_id is empty
    if (empty($class_id)) {
        error_log("Class ID is empty.");
        header("Location: ../views/classManage.php?error=empty_class_id");
        exit();
    }

    // Debugging: Check if student_ids is empty
    if (empty($student_ids)) {
        error_log("Student IDs are empty.");
        header("Location: ../views/classManage.php?error=empty_student_ids");
        exit();
    }

    // Check class capacity
    $studentClass = new StudentClass(); 
    $classCapacity = $studentClass->getClassCapacity($class_id);

    if (!$classCapacity) {
        error_log("Class not found.");
        header("Location: ../views/classManage.php?error=class_not_found");
        exit();
    }

    $capacity = $classCapacity['capacity'];
    $current_students = $classCapacity['current_students']; 
    $new_students_count = count($student_ids);

    // Debugging: Log capacity and current students
    error_log("Class Capacity: $capacity, Current Students: $current_students, New Students: $new_students_count");

    // Check if adding new students exceeds capacity
    if (($current_students + $new_students_count) > $capacity) {
        error_log("Class capacity exceeded.");
        header("Location: ../views/classManage.php?class_id=$class_id&error=capacity_exceeded");
        exit();
    }

    // Add students to the class
    $studentClass = new StudentClass();
    try {
        $studentClass->addStudentsToClass($student_ids, $class_id);
        header("Location: ../views/classManage.php?class_id=$class_id&success=1");
        exit();
    } catch (Exception $e) {
        error_log("Error adding students to class: " . $e->getMessage());
        header("Location: ../views/classManage.php?error=database_error");
        exit();
    }
} else {
    // Debugging: Log the error
    error_log("Invalid request: " . print_r($_POST, true));
    header("Location: ../views/classManage.php?error=invalid_request");
    exit();
}
?>