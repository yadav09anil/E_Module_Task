<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

// Check if student is logged in
if(!isset($_SESSION['roll_number'])){
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$rollNumber = $_SESSION['roll_number'];

// Collect POST data
$name = $_POST['name'] ?? '';
$class = $_POST['class'] ?? '';
$gender = $_POST['gender'] ?? '';
$phone = $_POST['phone'] ?? '';
$dob = $_POST['dob'] ?? '';

// Check for empty fields
if(empty($name) || empty($class) || empty($gender) || empty($phone) || empty($dob)){
    echo json_encode(['status'=>'error','message'=>'All fields are required']);
    exit;
}


// Handle profile picture upload
$uploadsDir = 'uploads/';
if(!is_dir($uploadsDir)){
    mkdir($uploadsDir, 0755, true);
}

// Default path to database
$profilePicDB = null;

if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0){
    $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

    // Only allow jpg
    if($ext !== 'jpg'){
        echo json_encode(['status'=>'error','message'=>'Only JPG images are allowed']);
        exit;
    }

    $profilePicPath = $uploadsDir . $rollNumber . '.jpg';

    // Remove old profile pic if exists
    if(file_exists($profilePicPath)){
        unlink($profilePicPath);
    }

    if(!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profilePicPath)){
        echo json_encode(['status'=>'error','message'=>'Failed to upload profile picture']);
        exit;
    }

    $profilePicDB = $profilePicPath; // Save relative path for DB
}

// Prepare update query
if($profilePicDB){
    $stmt = $conn->prepare("UPDATE students_info SET name=?, class=?, gender=?, phone=?, dob=?, profile_pic=? WHERE roll_number=?");
    $stmt->bind_param("sssssss", $name, $class, $gender, $phone, $dob, $profilePicDB, $rollNumber);
}else{
    $stmt = $conn->prepare("UPDATE students_info SET name=?, class=?, gender=?, phone=?, dob=? WHERE roll_number=?");
    $stmt->bind_param("ssssss", $name, $class, $gender, $phone, $dob, $rollNumber);
}

// Execute update
if($stmt->execute()){
    echo json_encode(['status'=>'success','message'=>'Profile updated successfully']);
}else{
    echo json_encode(['status'=>'error','message'=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>
