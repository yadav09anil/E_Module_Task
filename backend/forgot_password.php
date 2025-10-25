<?php
include('config.php'); //database connection file
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $role = $_POST['role'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match!']);
        exit;
    }

    // Hash the new password securely
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // For Student
    if ($role === 'student' && !empty($_POST['student_roll'])) {
        $roll = $_POST['student_roll'];

        $stmt = $conn->prepare("UPDATE students_info SET password = ? WHERE roll_number = ?");
        $stmt->bind_param("ss", $hashed_password, $roll);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Student password updated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Roll Number or no changes made.']);
        }

        $stmt->close();
    }

    // For Teacher
    elseif ($role === 'teacher' && !empty($_POST['teacher_id'])) {
        $teacher_id = $_POST['teacher_id'];

        $stmt = $conn->prepare("UPDATE teachers_info SET password = ? WHERE teacher_id = ?");
        $stmt->bind_param("ss", $hashed_password, $teacher_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Teacher password updated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Teacher ID or no changes made.']);
        }

        $stmt->close();
    }

    else {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields!']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method!']);
}

$conn->close();
?>
