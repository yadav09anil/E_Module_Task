<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
 body {
  font-family: "Poppins", sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
  position: relative;
  overflow: hidden;
  background: rgba(0, 0, 0, 0.7); 
}

body::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: url("../backend/resources/bg1.jpg") no-repeat center center;
  background-size: 100% 100%;
  opacity: 0.5;
  z-index: -1;
}

.login-container {
  background: white;
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  width: 350px;
  text-align: center;
  transition: transform 0.3s ease;
}

.login-container:hover {
  transform: translateY(-5px);
}

h2 {
  margin-bottom: 25px;
  color: #333;
}

.input-field {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  outline: none;
  transition: 0.3s;
  position: relative;
}

.input-field:focus {
  border-color: #2575fc;
  box-shadow: 0 0 8px rgba(37, 117, 252, 0.3);
}

.btn {
  background: #2575fc;
  color: white;
  border: none;
  padding: 12px 20px;
  width: 100%;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

.btn::before {
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

.btn:hover::before {
  left: 125%;
}

.btn:hover {
  background: #1e63d9;
  transform: translateY(-2px);
}


.link-container {
  margin-top: 10px;
  font-size: 14px;
}

.link-container a {
  color: #2575fc;
  text-decoration: none;
  font-weight: 600;
}

.link-container a:hover {
  text-decoration: underline;
}

    .popup {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #ff4c4c;
      color: white;
      padding: 15px 20px;
      border-radius: 8px;
      opacity: 0;
      transform: translateY(-20px);
      transition: all 0.5s ease;
      z-index: 9999;
    }
    .popup.show { opacity: 1; transform: translateY(0); }
    .popup.success { background-color: #28a745; }


.input-field::after {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(120deg, rgba(255,255,255,0.3), transparent);
  transition: 0.5s;
}

.input-field:focus::after {
  left: 100%;
}
  </style>
</head>
<body>

<div class="login-container">
  <h2>Login</h2>
  <form id="loginForm">
    <select name="role" id="roleSelect" class="input-field" required>
      <option value="">Select Role</option>
      <option value="student">Student</option>
      <option value="teacher">Teacher</option>
    </select>

    <div id="studentFields" style="display:none;">
      <input type="text" name="roll_number" class="input-field" placeholder="Enter Roll Number">
      <input type="password" name="password_student" class="input-field" placeholder="Enter Password">
    </div>

    <div id="teacherFields" style="display:none;">
      <input type="text" name="teacher_id" class="input-field" placeholder="Enter Teacher ID">
      <input type="password" name="password_teacher" class="input-field" placeholder="Enter Password">
    </div>

    <button type="submit" class="btn">Login</button>
    <div class="link-container">
      Don’t have an account? <a href="student_register.php">Register here</a><br>
      <a href="forgot_password.php">Forgot Password?</a>
    </div>
  </form>
</div>

<div id="popup" class="popup"></div>

<script>
const roleSelect = document.getElementById('roleSelect');
const studentFields = document.getElementById('studentFields');
const teacherFields = document.getElementById('teacherFields');
const loginForm = document.getElementById('loginForm');
const popup = document.getElementById('popup');

function showPopup(message, type='error') {
  popup.innerText = message;
  popup.className = `popup show ${type}`;
  setTimeout(() => popup.className = 'popup', 3000);
}

// Show/hide fields based on role
roleSelect.addEventListener('change', function() {
  if(this.value === 'student') {
    studentFields.style.display = 'block';
    teacherFields.style.display = 'none';
    studentFields.querySelector('input[name="roll_number"]').required = true;
    studentFields.querySelector('input[name="password_student"]').required = true;
  } else if(this.value === 'teacher') {
    studentFields.style.display = 'none';
    teacherFields.style.display = 'block';
    teacherFields.querySelector('input[name="teacher_id"]').required = true;
    teacherFields.querySelector('input[name="password_teacher"]').required = true;
  } else {
    studentFields.style.display = 'none';
    teacherFields.style.display = 'none';
  }
});

// Submit login form
loginForm.addEventListener('submit', function(e) {
  e.preventDefault();

  const role = roleSelect.value;
  let formData = new URLSearchParams();

  if(role === 'student') {
    formData.append('role', role);
    formData.append('roll_number', studentFields.querySelector('input[name="roll_number"]').value);
    formData.append('password', studentFields.querySelector('input[name="password_student"]').value);
  } else if(role === 'teacher') {
    formData.append('role', role);
    formData.append('teacher_id', teacherFields.querySelector('input[name="teacher_id"]').value);
    formData.append('password', teacherFields.querySelector('input[name="password_teacher"]').value);
  } else {
    showPopup("Please select a role");
    return;
  }

  fetch('../backend/login.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'success'){
      showPopup(data.message, 'success');
      setTimeout(() => {
        if(role === 'student') window.location.href = 'student_dashboard.php';
        else window.location.href = 'teacher_dashboard.php';
      }, 1000);
    } else {
      showPopup(data.message);
    }
  })
  .catch(err => showPopup('Server error'));
});
</script>

</body>
</html>
