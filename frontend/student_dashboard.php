<?php
session_start();
include('../backend/config.php'); // database connection


if (!isset($_SESSION['roll_number'])) {
    header("Location: login.php");
    exit;
}

$rollNumber = $_SESSION['roll_number'];

// Fetch student details
$stmt = $conn->prepare("SELECT * FROM students_info WHERE roll_number = ?");
$stmt->bind_param("s", $rollNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No student found with roll number: " . htmlspecialchars($rollNumber));
}

$student = $result->fetch_assoc();
$stmt->close();

$msg_stmt = $conn->prepare("
  SELECT tm.message, tm.created_at, ti.teacher_name
    FROM teacher_messages tm
    JOIN teachers_info ti ON tm.teacher_id = ti.teacher_id
    WHERE 
        (
            tm.roll_number = ?  -- direct message to student
            OR (
                (tm.class = ? OR tm.class = 'All')  -- class-specific or for all
                AND (tm.gender = ? OR tm.gender = 'All')  -- gender-specific or for all
            )
        )
    ORDER BY tm.created_at DESC
");
$msg_stmt->bind_param("sss", $student['roll_number'], $student['class'], $student['gender']);
$msg_stmt->execute();
$messages = $msg_stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard | Student Teacher Portal</title>

<style>
body {
  font-family: "Poppins", sans-serif;
  margin: 0;
  background: linear-gradient(135deg, #e3e9f0, #f0f2f5);
  display: flex;
  height: 100vh;
  overflow: hidden;
}

.sidebar {
  width: 220px;
  background: linear-gradient(180deg, #1a1a1a, #2c2c2c);
  color: #ffffff;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding: 20px 0;
  height: 100vh;
  position: fixed;
  box-shadow: 5px 0 20px rgba(0, 0, 0, 0.3);
  animation: slideIn 0.7s ease;
}
.sidebar-header {
  text-align: center;
  margin-bottom: 30px; 
}

.school-name {
  font-size: 24px; 
  font-weight: bold;
}

.school-subtitle {
  font-size: 14px; 
  letter-spacing: 2px;
}
.sidebar .nav-item {
  padding: 15px 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  font-size: 16px;
  color: #f0f0f0;
  position: relative;
  overflow: hidden;
}

.sidebar .nav-item::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(120deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: 0.5s;
}

.sidebar .nav-item:hover::before {
  left: 100%;
}

.sidebar .nav-item:hover,
.sidebar .nav-item.active {
  background: #444;
  color: #ffffff;
  transform: translateX(5px);
}

.sidebar .logout {
  margin-top: auto;
  background: linear-gradient(90deg, #ff5e57, #ff7b72);
  text-align: center;
  margin: 20px;
  padding: 10px 0;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.4s ease;
  color: #ffffff;
  position: relative;
  overflow: hidden;
}

.sidebar .logout::before {
  content: "";
  position: absolute;
  top: 0;
  left: -75%;
  width: 50%;
  height: 100%;
  background: linear-gradient(120deg, rgba(255,255,255,0.5), transparent);
  transform: skewX(-20deg);
  transition: 0.5s;
}

.sidebar .logout:hover::before {
  left: 125%;
}

.sidebar .logout:hover {
  background: linear-gradient(90deg, #e0433d, #ff5e57);
  transform: scale(1.05);
}

.main-content {
  margin-left: 220px;
  padding: 20px;
  width: calc(100% - 220px);
  overflow-y: auto;
  animation: fadeIn 0.8s ease;
}

.section {
  background: #ffffff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  margin-bottom: 20px;
  transition: 0.3s ease;
}

.section:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

.section h2 {
  margin-top: 0;
  color: #222;
}


.messages .message {
  border-bottom: 1px solid #eee;
  padding: 10px 0;
}

.messages .message:last-child {
  border-bottom: none;
}

.messages .from {
  font-weight: bold;
  color: #2575fc;
}


.profile-section form {
  display: flex;
  flex-direction: column;
  align-items: center;
  animation: fadeIn 1s ease;
}

.profile-section input, 
.profile-section select {
  width: 100%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 8px;
  outline: none;
  transition: 0.3s;
}

.profile-section input:focus,
.profile-section select:focus {
  border-color: #2575fc;
  box-shadow: 0 0 8px rgba(37,117,252,0.3);
}

.profile-section button {
  background: linear-gradient(90deg, #2575fc, #1e63d9);
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: 0.3s;
  margin-top: 10px;
  position: relative;
  overflow: hidden;
}

.profile-section button::before {
  content: "";
  position: absolute;
  top: 0;
  left: -75%;
  width: 50%;
  height: 100%;
  background: linear-gradient(120deg, rgba(255,255,255,0.5), transparent);
  transform: skewX(-20deg);
  transition: 0.5s;
}

.profile-section button:hover::before {
  left: 125%;
}

.profile-section button:hover {
  transform: translateY(-3px);
}


.profile-pic-container {
  position: relative;
  margin-bottom: 15px;
  animation: fadeIn 0.8s ease;
}

.profile-pic-container img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #2575fc;
  transition: 0.4s ease;
}

.profile-pic-container img:hover {
  transform: scale(1.05);
  box-shadow: 0 0 15px rgba(37,117,252,0.4);
}

.profile-pic-container label {
  position: absolute;
  bottom: 0;
  right: 0;
  background: linear-gradient(90deg, #2575fc, #1e63d9);
  width: 35px;
  height: 35px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  color: white;
  font-size: 20px;
  border: 2px solid white;
  transition: 0.3s;
}

.profile-pic-container label:hover {
  transform: scale(1.1);
  background: linear-gradient(90deg, #1e63d9, #2575fc);
}

.profile-pic-container input[type="file"] {
  display: none;
}

/* Animations */
@keyframes slideIn {
  from { transform: translateX(-100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

</style>
</head>
<body>

<div class="sidebar">

 <div class="sidebar-header">
    <div class="school-name">ABC</div>
    <div class="school-subtitle">SCHOOL</div>
  </div>
  <div class="nav-item active" id="notificationBtn">Notifications</div>
  <div class="nav-item" id="profileBtn">Profile</div>
  <div class="logout" onclick="window.location.href='../backend/logout.php'">Logout</div>
</div>

<div class="main-content">

  <div class="section messages" id="notificationSection">
    <h2>Messages from Teachers</h2>
    <?php if($messages->num_rows > 0): ?>
      <?php while($msg = $messages->fetch_assoc()): ?>
        <div class="message">
          <p class="from"><?= htmlspecialchars($msg['teacher_name']) ?>:</p>
          <p><?= htmlspecialchars($msg['message']) ?></p>
          <small><?= htmlspecialchars($msg['created_at']) ?></small>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No messages found.</p>
    <?php endif; ?>
  </div>

  <div class="section profile-section" id="profileSection" style="display:none;">
    <h2>Your Profile</h2>
    <form id="updateProfileForm" enctype="multipart/form-data">
      <?php
      $uploadsDir = '../backend/uploads/';
      $rollPicPath = $uploadsDir . $student['roll_number'] . '.jpg';
      $defaultPicPath = $uploadsDir . 'default.jpg';
      $uploadsURL = '../backend/uploads/';
      $profilePicSrc = file_exists($rollPicPath) ? $uploadsURL . $student['roll_number'] . '.jpg' : $uploadsURL . 'default.jpg';
      ?>
      <div class="profile-pic-container">
        <img id="profilePic" src="<?= htmlspecialchars($profilePicSrc) ?>" alt="Profile Picture">
        <label for="profilePicInput">+</label>
        <input type="file" id="profilePicInput" name="profile_pic" accept=".jpg">
      </div>

      <input type="text" name="name" placeholder="Full Name" value="<?= htmlspecialchars($student['name']) ?>" required>
      <input type="text" name="class" placeholder="Class" value="<?= htmlspecialchars($student['class']) ?>" required>
      <input type="text" name="gender" placeholder="Gender" value="<?= htmlspecialchars($student['gender']) ?>" required>
      <input type="tel" name="phone" placeholder="Phone Number" value="<?= htmlspecialchars($student['phone']) ?>" pattern="\d{10}" maxlength="10" required>
      <input type="date" name="dob" placeholder="Date of Birth" value="<?= htmlspecialchars($student['dob']) ?>" max="<?= date('Y-m-d') ?>" required>

      <button type="submit">Update</button>
    </form>

    <p id="updateMessage" style="color:green;margin-top:10px;"></p>
  </div>

</div>

<script>
const profileBtn = document.getElementById('profileBtn');
const profileSection = document.getElementById('profileSection');
const notificationSection = document.getElementById('notificationSection');
const notificationBtn = document.getElementById('notificationBtn');
const profilePicInput = document.getElementById('profilePicInput');
const profilePic = document.getElementById('profilePic');
const updateForm = document.getElementById('updateProfileForm');
const updateMessage = document.getElementById('updateMessage');

profileBtn.addEventListener('click', () => {
  profileSection.style.display = 'block';
  notificationSection.style.display = 'none';
  profileBtn.classList.add('active');
  notificationBtn.classList.remove('active');
});

notificationBtn.addEventListener('click', () => {
  profileSection.style.display = 'none';
  notificationSection.style.display = 'block';
  notificationBtn.classList.add('active');
  profileBtn.classList.remove('active');
});


profilePicInput.addEventListener('change', (e) => {
  const file = e.target.files[0];
  if(file){
    const reader = new FileReader();
    reader.onload = function(event){ 
      profilePic.src = event.target.result; 
    }
    reader.readAsDataURL(file);
  }
});


updateForm.addEventListener('submit', function(e){
  e.preventDefault();

  const formData = new FormData(updateForm);

  fetch('../backend/student_dashboard.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'success'){
      updateMessage.style.color = 'green';
      updateMessage.textContent = 'Profile updated successfully!';
      setTimeout(()=> location.reload(), 1500);
    } else {
      updateMessage.style.color = 'red';
      updateMessage.textContent = data.message;
    }
  })
  .catch(err => {
    updateMessage.style.color = 'red';
    updateMessage.textContent = '';
  });
});
</script>

</body>
</html>
