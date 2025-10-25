<?php
session_start();
include('../backend/config.php');

if(!isset($_SESSION['teacher_id'])){
    header("Location: login.php");
    exit;
}

$teacher_id = $_SESSION['teacher_id'];

$stmt = $conn->prepare("SELECT teacher_id, teacher_name FROM teachers_info WHERE teacher_id = ?");
$stmt->bind_param("s", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows === 0){
    session_destroy();
    header("Location: login.php");
    exit;
}
$teacher = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Teacher Dashboard | Student Teacher Portal</title>
<style>
  body {
  margin: 0;
  font-family: "Poppins", sans-serif;
  display: flex;
  height: 100vh;
  background: #f0f2f5;
  overflow: hidden;
}

.sidebar {
  width: 220px;
  background: #1a1a1a;
  color: #ffffff;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding: 20px 0;
  height: 100vh;
  position: fixed;
  box-shadow: 5px 0 20px rgba(0,0,0,0.3);
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
  background: #e74c3c;
  text-align: center;
  margin: 10px 20px; 
  padding: 10px 0;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  position: relative;
  overflow: hidden;
  transition: all 0.4s ease;
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
  background: #c0392b;
  transform: scale(1.05);
}


.main-content {
  margin-left: 220px;
  padding: 20px;
  width: calc(100% - 220px);
  overflow-y: auto;
  animation: fadeIn 0.8s ease;
}

.content-section {
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  display: none;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.content-section.active {
  display: block;
}

.content-section:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

h2, h3 {
  margin-top: 0;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

th {
  background: #2575fc;
  color: white;
}

.message-form select, .message-form input, .message-form textarea {
  width: 100%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 8px;
  outline: none;
  transition: 0.3s;
}

.message-form select:focus, .message-form input:focus, .message-form textarea:focus {
  border-color: #2575fc;
  box-shadow: 0 0 8px rgba(37,117,252,0.3);
}

.message-form button {
  background: #2575fc;
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: 0.3s;
  position: relative;
  overflow: hidden;
}

.message-form button::before {
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

.message-form button:hover::before {
  left: 125%;
}

.message-form button:hover {
  background: #1e63d9;
  transform: translateY(-2px);
}

.teacher-info {
  margin-bottom: 20px;
  background: white;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.teacher-info:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.15);
}

.student-search {
  margin-bottom: 10px;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.student-search input, .student-search select {
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
  flex: 1;
  transition: 0.3s;
}

.student-search input:focus, .student-search select:focus {
  border-color: #2575fc;
  box-shadow: 0 0 6px rgba(37,117,252,0.3);
}


#popupMessage {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #16c55c;
  color: white;
  padding: 15px 25px;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  display: none;
  font-weight: 500;
  z-index: 1000;
  animation: fadeIn 0.5s ease;
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
    <div class="nav-item active" id="sendMessageBtn">Send Message</div>
    <div class="nav-item" id="studentListBtn">Student List</div>
    <div class="logout" onclick="window.location.href='../backend/logout.php'">Logout</div>
</div>

<div class="main-content">

    <div class="teacher-info">
      <h2>Teacher Info</h2>
      <p><strong>Name:</strong> <?php echo htmlspecialchars($teacher['teacher_name']); ?></p>
      <p><strong>ID:</strong> <?php echo htmlspecialchars($teacher['teacher_id']); ?></p>
    </div>

    <div id="sendMessageSection" class="content-section active message-form">
      <h3>Send Message to Students</h3>
      <form id="teacherMessageForm">
        <label>Class</label>
        <select name="class">
          <option value="">Select Class</option>
          <option value="All">All</option>
          <option value="10th">10th</option>
          <option value="11th">11th</option>
          <option value="12th">12th</option>
        </select>

        <label>Gender</label>
        <select name="gender">
          <option value="">Select Gender</option>
          <option value="All">All</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>

        <label>Roll Number (Optional)</label>
        <input type="text" name="roll_number" placeholder="Enter Roll Number">

        <label>Message</label>
        <textarea name="message" rows="4" placeholder="Type your message here..." required></textarea>

        <button type="submit">Send Message</button>
      </form>
    </div>


<div id="studentListSection" class="content-section">
  <h3>All Students</h3>
  <div class="student-search">
    <input type="text" id="searchRoll" placeholder="Search by Roll Number">
    <input type="text" id="searchName" placeholder="Search by Name">
    <select id="searchClass">
      <option value="">Select Class</option>
      <option value="10th">10th</option>
      <option value="11th">11th</option>
      <option value="12th">12th</option>
    </select>
    <select id="searchGender">
      <option value="">Select Gender</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select>
  </div>
  <table id="studentTable">
    <thead>
      <tr>
        <th>Roll Number</th>
        <th>Name</th>
        <th>Class</th>
        <th>Gender</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $student_query = "SELECT roll_number, name, class, gender FROM students_info ORDER BY roll_number ASC";
      $result = $conn->query($student_query);

      if($result && $result->num_rows > 0){
          while($row = $result->fetch_assoc()){
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row['roll_number']) . "</td>";
              echo "<td>" . htmlspecialchars($row['name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['class']) . "</td>";
              echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='4' style='text-align:center;'>No students found</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>


</div>

<div id="popupMessage">Message sent successfully!</div>

<script>
const sendMessageBtn = document.getElementById('sendMessageBtn');
const studentListBtn = document.getElementById('studentListBtn');
const sendMessageSection = document.getElementById('sendMessageSection');
const studentListSection = document.getElementById('studentListSection');

sendMessageBtn.addEventListener('click', function() {
  sendMessageSection.classList.add('active');
  studentListSection.classList.remove('active');
  sendMessageBtn.classList.add('active');
  studentListBtn.classList.remove('active');
});

studentListBtn.addEventListener('click', function() {
  studentListSection.classList.add('active');
  sendMessageSection.classList.remove('active');
  studentListBtn.classList.add('active');
  sendMessageBtn.classList.remove('active');
});


const messageForm = document.getElementById('teacherMessageForm');
const popupMessage = document.getElementById('popupMessage');

messageForm.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(messageForm);

    fetch('../backend/send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        popupMessage.innerText = data.message;
        popupMessage.style.background = data.status === 'success' ? '#16c55c' : '#e74c3c';
        popupMessage.style.display = 'block';
        setTimeout(()=>{ popupMessage.style.display='none'; popupMessage.style.background='#16c55c'; }, 3000);
        if(data.status === 'success') messageForm.reset();
    })
    .catch(err => console.log(err));
});


const searchRoll = document.getElementById('searchRoll');
const searchName = document.getElementById('searchName');
const searchClass = document.getElementById('searchClass');
const searchGender = document.getElementById('searchGender');
const studentTable = document.getElementById('studentTable').getElementsByTagName('tbody')[0];

function filterStudents(){
    const rollVal = searchRoll.value.toLowerCase();
    const nameVal = searchName.value.toLowerCase();
    const classVal = searchClass.value;
    const genderVal = searchGender.value;

    Array.from(studentTable.rows).forEach(row => {
        const roll = row.cells[0].innerText.toLowerCase();
        const name = row.cells[1].innerText.toLowerCase();
        const cls = row.cells[2].innerText;
        const gender = row.cells[3].innerText;

        row.style.display = ((roll.includes(rollVal) || rollVal==='') &&
                             (name.includes(nameVal) || nameVal==='') &&
                             (cls===classVal || classVal==='') &&
                             (gender===genderVal || genderVal==='')) ? '' : 'none';
    });
}

searchRoll.addEventListener('input', filterStudents);
searchName.addEventListener('input', filterStudents);
searchClass.addEventListener('change', filterStudents);
searchGender.addEventListener('change', filterStudents);
</script>

</body>
</html>
