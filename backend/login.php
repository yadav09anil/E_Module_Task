<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
session_start();
include 'config.php'; // Database connection

$role = $_POST['role'] ?? '';
$password = $_POST['password'] ?? '';

if($role === 'student'){
    $identifier = $_POST['roll_number'] ?? '';
    $table = 'students_info';
    $col = 'roll_number';
} elseif($role === 'teacher'){
    $identifier = $_POST['teacher_id'] ?? '';
    $table = 'teachers_info';
    $col = 'teacher_id';
} else {
    echo json_encode(['status'=>'error','message'=>'Invalid role']);
    exit;
}

if(empty($identifier) || empty($password)){
    echo json_encode(['status'=>'error','message'=>'All fields are required']);
    exit;
}

// Prepare statement
$stmt = $conn->prepare("SELECT * FROM $table WHERE $col = ?");
if(!$stmt){
    echo json_encode(['status'=>'error','message'=>'Database error: '.$conn->error]);
    exit;
}

$stmt->bind_param("s", $identifier);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    echo json_encode(['status'=>'error','message'=>'User not found']);
    exit;
}

$user = $result->fetch_assoc();

if(password_verify($password, $user['password'])){
    $_SESSION['role'] = $role;

    if($role === 'student'){
        $_SESSION['roll_number'] = $identifier;
    } else {
        $_SESSION['teacher_id'] = $identifier;
    }

    echo json_encode(['status'=>'success','message'=>'Login successful']);
} else {
    echo json_encode(['status'=>'error','message'=>'Incorrect password']);
}

$stmt->close();
$conn->close();
?>
