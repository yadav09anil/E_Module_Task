<?php
include 'config.php'; // connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $teacher_id = trim($_POST['teacher_id']);
    $teacher_name = trim($_POST['teacher_name']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check password match
    if ($password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match!"]);
        exit;
    }

    // Check if teacher_id already exists
    $check = $conn->prepare("SELECT * FROM teachers_info WHERE teacher_id = ?");
    $check->bind_param("s", $teacher_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Teacher ID already exists!"]);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new teacher
    $stmt = $conn->prepare("INSERT INTO teachers_info (teacher_id, teacher_name, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $teacher_id, $teacher_name, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error while saving data."]);
    }

    $stmt->close();
    $check->close();
    $conn->close();
}
?>
