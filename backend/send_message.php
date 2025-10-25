<?php
session_start();
include 'config.php'; // Database connection

// Check if teacher is logged in
if(!isset($_SESSION['teacher_id'])){
    echo json_encode(['status'=>'error','message'=>'Unauthorized']);
    exit;
}

$teacher_id = $_SESSION['teacher_id'];
$class = $_POST['class'] ?? '';
$gender = $_POST['gender'] ?? '';
$roll_number = $_POST['roll_number'] ?? '';
$message = $_POST['message'] ?? '';

if(empty($message)){
    echo json_encode(['status'=>'error','message'=>'Message cannot be empty']);
    exit;
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO teacher_messages (teacher_id, class, gender, roll_number, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $teacher_id, $class, $gender, $roll_number, $message);

if($stmt->execute()){
    echo json_encode(['status'=>'success','message'=>'Message sent successfully']);
} else {
    echo json_encode(['status'=>'error','message'=>'Database error: '.$stmt->error]);
}

$stmt->close();
$conn->close();
?>
