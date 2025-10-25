<?php
header('Content-Type: application/json');
include 'config.php';

// Get POST data safely
$roll_number = trim($_POST['roll_number'] ?? '');
$name        = trim($_POST['name'] ?? '');
$gender      = $_POST['gender'] ?? '';
$class       = $_POST['class'] ?? '';
$phone       = trim($_POST['phone'] ?? '');
$dob         = $_POST['dob'] ?? '';
$password    = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validation
if(empty($roll_number) || empty($name) || empty($gender) || empty($class) || empty($phone) || empty($dob) || empty($password) || empty($confirm_password)) {
    echo json_encode(['status'=>'error','message'=>'All fields are required']);
    exit;
}

if($password !== $confirm_password){
    echo json_encode(['status'=>'error','message'=>'Passwords do not match']);
    exit;
}

if(strtotime($dob) > time()){
    echo json_encode(['status'=>'error','message'=>'Date of birth cannot be in the future']);
    exit;
}

if(!preg_match('/^[0-9]{10}$/', $phone)){
    echo json_encode(['status'=>'error','message'=>'Invalid phone number']);
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert data
$stmt = $conn->prepare("INSERT INTO students_info (roll_number, name, gender, class, phone, dob, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $roll_number, $name, $gender, $class, $phone, $dob, $hashed_password);

if($stmt->execute()){
    echo json_encode(['status'=>'success','message'=>'Registration successful!']);
} else {
    echo json_encode(['status'=>'error','message'=>'Roll number already exists or failed to register.']);
}

$stmt->close();
$conn->close();
?>
