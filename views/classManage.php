<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management</title>
    <link rel="stylesheet" href="../public/css/classManage.css">
    <link rel="stylesheet" href="../public/css/error.css">
    <link rel="stylesheet" href="../public/css/table.css">
    <link rel="stylesheet" href="../public/css/addStudentClass.css">
    <link rel="stylesheet" href="../public/css/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../public/js/classManage.js"></script>
    <script src="../public/js/error.js"></script>
    <script src="../public/js/addStudent.js"></script>
</head>

<?php
require_once '../models/classModel.php';
require_once '../controllers/classController.php';
require_once '../models/student.php';
$student = new Student();
$students = $student->getAllStudents();

// Get unique programmes for filtering
$programmes = array_unique(array_column($students, 'programme'));

$classModel = new ClassModel();
$classes = $classModel->getAllClasses();

if (isset($_GET['error'])) {
    $error = $_GET['error'];
    $message = '';
    $icon = '';

    switch ($error) {
        case 'capacity_exceeded':
            $message = "Class capacity exceeded. Cannot add more students.";
            $icon = 'fas fa-exclamation-triangle'; // Font Awesome icon
            break;
        case 'empty_class_id':
            $message = "Class ID is missing.";
            $icon = 'fas fa-times-circle';
            break;
        case 'empty_student_ids':
            $message = "No students selected.";
            $icon = 'fas fa-user-slash';
            break;
        case 'class_not_found':
            $message = "Class not found.";
            $icon = 'fas fa-search-minus';
            break;
        case 'database_error':
            $message = "Database error occurred.";
            $icon = 'fas fa-database';
            break;
        default:
            $message = "An error occurred.";
            $icon = 'fas fa-exclamation-circle';
    }

    echo "
    <div class='error-message'>
        <div class='error-icon'>
            <i class='$icon'></i>
        </div>
        <div class='error-content'>
            <h3>Oops! Something went wrong.</h3>
            <p>$message</p>
        </div>
        <div class='error-close'>
            <i class='fas fa-times'></i>
        </div>
    </div>
    ";
}

if (isset($_GET['success'])) {
    echo "
    <div class='success-message'>
        <div class='success-icon'>
            <i class='fas fa-check-circle'></i>
        </div>
        <div class='success-content'>
            <h3>Success!</h3>
            <p>Students added successfully.</p>
        </div>
        <div class='success-close'>
            <i class='fas fa-times'></i>
        </div>
    </div>
    ";
}
?>

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
        <h1 class="header">Class <span class="highlight">Management</span></h1>
        <div class="actions">
            <button class="btn add" id="openAddClass">+ Add New Class</button>

            <!-- <button class="btn import">Import from CSV</button> -->
        </div>

        <!-- Class Table -->
        <form method="POST" action="">
            <table class="class-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Class Name</th>
                        <th>Subject</th>
                        <th>Class Code</th>
                        <th>Capacity</th>
                        <th>First Day</th>
                        <th>Second Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Students Assigned</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $index => $class): ?>
                            <tr data-class-id="<?= $class['class_id']; ?>"> <!-- Store class_id here -->
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($class['class_name']) ?></td>
                                <td><?= htmlspecialchars($class['subject']) ?></td>
                                <td><?= htmlspecialchars($class['subject_code']) ?></td>
                                <td><?= htmlspecialchars($class['capacity']) ?></td>
                                <td><?= htmlspecialchars($class['first_day']) ?></td>
                                <td><?= htmlspecialchars($class['last_day']) ?></td>
                                <td><?= htmlspecialchars($class['start_time']) ?></td>
                                <td><?= htmlspecialchars($class['end_time']) ?></td>

                                <td><?= $class['students_assigned'] . '/' . $class['capacity'] ?></td>
                                <td><span class="tableStatus <?= $class['status'] == 'Active' ? 'active' : 'inactive' ?>">
                                        <?= htmlspecialchars($class['status']) ?>
                                    </span></td>
                                <td>
                                    <button type="button" class="action-btn add-student-btn"
                                        data-class-id="<?= $class['class_id']; ?>">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                    <a href="attendanceRecord.php?class_id=<?= $class['class_id']; ?>"
                                        class="action-btn view-btn">
                                        <i class="fas fa-calendar-check"></i>
                                    </a>
                                    <button class="edit-btn" data-id="<?= $class['class_id']; ?>"
                                        data-name="<?= $class['class_name']; ?>" data-subject="<?= $class['subject']; ?>"
                                        data-code="<?= $class['subject_code']; ?>" data-capacity="<?= $class['capacity']; ?>"
                                        data-firstday="<?= $class['first_day']; ?>" data-lastday="<?= $class['last_day']; ?>"
                                        data-start="<?= $class['start_time']; ?>" data-end="<?= $class['end_time']; ?>"
                                        data-status="<?= $class['status']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="class_id" value="<?= $class['class_id']; ?>">
                                        <button type="submit" name="action" value="delete" class="action-btn delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this class?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">No classes found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>

        <!-- Add Class Modal -->
        <div id="addClassModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeAddClass">&times;</span>
                <!-- Add Class Form -->
                <h2>Add New Class</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <input type="hidden" name="action" value="add">
                    </div>
                    <div class="form-group">
                        <input type="text" name="class_name" placeholder="Class Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="subject" placeholder="Subject" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="subject_code" placeholder="Subject Code" required>
                    </div>
                    <div class="form-group">
                        <input type="number" name="capacity" placeholder="Capacity" required>
                    </div>
                    <div class="form-group">
                        <input type="number" name="total_classes" placeholder="Total Classes" required>
                    </div>

                    <!-- Schedule Section -->
                    <div class="form-group">
                        <label for="scheduleDays">First Day:</label>
                        <select name="first_day" id="scheduleDays">
                            <option value="Mon">Monday</option>
                            <option value="Tue">Tuesday</option>
                            <option value="Wed">Wednesday</option>
                            <option value="Thu">Thursday</option>
                            <option value="Fri">Friday</option>
                            <option value="Sat">Saturday</option>
                            <option value="Sun">Sunday</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scheduleDays">Last Day:</label>
                        <select name="last_day" id="scheduleDays">
                            <option value="Mon">Monday</option>
                            <option value="Tue">Tuesday</option>
                            <option value="Wed">Wednesday</option>
                            <option value="Thu">Thursday</option>
                            <option value="Fri">Friday</option>
                            <option value="Sat">Saturday</option>
                            <option value="Sun">Sunday</option>
                        </select>
                    </div>

                    <input type="time" name="start_time" placeholder="Start Time" required>
                    <input type="time" name="end_time" placeholder="End Time" required>

                    <select name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <button type="submit">Add Class</button>
                </form>

            </div>
        </div>
        <script>
            // Add Class Modal Handling
            document.getElementById("openAddClass").addEventListener("click", function () {
                document.getElementById("addClassModal").style.display = "flex";
            });
            document.getElementById("closeAddClass").addEventListener("click", function () {
                document.getElementById("addClassModal").style.display = "none";
            });
            window.onclick = function (event) {
                let modal = document.getElementById("addClassModal");
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            };
        </script>
    </div>

    <!-- Edit Class Modal -->
    <div id="editClassModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeEditClass">&times;</span>
            <h2>Edit Class</h2>
            <form id="editClassForm" method="POST" action="">
                <!-- Hidden input for class ID -->
                <input type="hidden" name="class_id" id="editClassId">

                <div class="form-group">
                    <label>Class Name</label>
                    <input type="text" name="class_name" id="editClassName" readonly>
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" id="editClassSubject" readonly>
                </div>
                <div class="form-group">
                    <label>Subject Code</label>
                    <input type="text" name="subject_code" id="editClassCode" readonly>
                </div>
                <div class="form-group">
                    <label>Capacity</label>
                    <input type="number" name="capacity" id="editClassCapacity">
                </div>
                <div class="form-group">
                    <input type="number" name="total_classes" placeholder="Total Classes" required>
                </div>

                <!-- Schedule Section -->
                <div class="form-group">
                    <label for="editFirstDay">First Day:</label>
                    <select name="first_day" id="editFirstDay">
                        <option value="Mon">Monday</option>
                        <option value="Tue">Tuesday</option>
                        <option value="Wed">Wednesday</option>
                        <option value="Thu">Thursday</option>
                        <option value="Fri">Friday</option>
                        <option value="Sat">Saturday</option>
                        <option value="Sun">Sunday</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editLastDay">Last Day:</label>
                    <select name="last_day" id="editLastDay">
                        <option value="Mon">Monday</option>
                        <option value="Tue">Tuesday</option>
                        <option value="Wed">Wednesday</option>
                        <option value="Thu">Thursday</option>
                        <option value="Fri">Friday</option>
                        <option value="Sat">Saturday</option>
                        <option value="Sun">Sunday</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="start_time" id="editStartTime" required>
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="end_time" id="editEndTime" required>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="editClassStatus">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>


                <input type="hidden" name="action" value="edit">
                <button type="submit" class="btn-submit">Save Changes</button>
            </form>
        </div>
    </div>
    <script>
        // Edit Class Modal Handling
        document.querySelectorAll(".edit-btn").forEach(button => {
            button.addEventListener("click", function () {
                // Get modal
                let modal = document.getElementById("editClassModal");

                // Set form fields with existing class data
                document.getElementById("editClassId").value = this.getAttribute("data-id");
                document.getElementById("editClassName").value = this.getAttribute("data-name");
                document.getElementById("editClassSubject").value = this.getAttribute("data-subject");
                document.getElementById("editClassCode").value = this.getAttribute("data-code");
                document.getElementById("editClassCapacity").value = this.getAttribute("data-capacity");
                document.getElementById("editFirstDay").value = this.getAttribute("data-firstday");
                document.getElementById("editLastDay").value = this.getAttribute("data-lastday");
                document.getElementById("editStartTime").value = this.getAttribute("data-start");
                document.getElementById("editEndTime").value = this.getAttribute("data-end");
                document.getElementById("editClassStatus").value = this.getAttribute("data-status");
                event.preventDefault();
                // Show modal
                modal.style.display = "flex";
            });
        });

        // Close modal when clicking the close button
        document.getElementById("closeEditClass").addEventListener("click", function () {
            document.getElementById("editClassModal").style.display = "none";
        });

        // Close modal when clicking outside of it
        window.onclick = function (event) {
            let modal = document.getElementById("editClassModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    </script>

    <!-- Add Student Modal -->
    <div id="addStudentModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeAddStudent">&times;</span>
            <h2>Add Students to Class</h2>

            <!-- Programme Filter -->
            <div class="form-group">
                <label for="programmeFilter">Filter by Programme:</label>
                <select id="programmeFilter" onchange="filterStudents()">
                    <option value="all">All Programmes</option>
                    <?php foreach ($programmes as $programme): ?>
                        <option value="<?= htmlspecialchars($programme) ?>"><?= htmlspecialchars($programme) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Student List Form -->
            <form action="../controllers/addStudent.php" method="POST">
                <input type="hidden" name="class_id" id="formClassId"> <!-- Dynamically Set -->
                <!-- Student List Table -->
                <table class="student-table">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Programme</th>
                        </tr>
                    </thead>
                    <tbody id="studentList">
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $student): ?>
                                <tr data-programme="<?= htmlspecialchars($student['programme']) ?>">
                                    <td>
                                        <input type="checkbox" name="student_ids[]"
                                            value="<?= htmlspecialchars($student['student_id']) ?>">
                                    </td>
                                    <td><?= htmlspecialchars($student['student_id']) ?></td>
                                    <td><?= htmlspecialchars($student['username']) ?></td>
                                    <td><?= htmlspecialchars($student['programme']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No students available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Add Students Button -->
                <button type="submit" class="btn-submit" id="confirmAddStudents">Add Selected Students</button>
            </form>
        </div>
    </div>

    <script>
        // Get modal elements
        const modal = document.getElementById("addClassModal");
        const closeModal = document.getElementById("closeAddClass");
        const openModal = document.getElementById("openAddClass");

        // Open modal
        openModal.addEventListener("click", () => {
            modal.style.display = "flex";
        });

        // Close modal
        closeModal.addEventListener("click", () => {
            modal.style.display = "none";
        });
    </script>

</body>

</html>