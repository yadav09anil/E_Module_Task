<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
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

.forgot-container {
  background: white;
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  width: 380px;
  text-align: center;
  transition: transform 0.3s ease;
}

.forgot-container:hover {
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
  box-shadow: 0 0 8px rgba(37,117,252,0.3);
}

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
  margin-top: 15px;
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
  display: none;
  background: #4caf50;
  color: white;
  padding: 12px;
  margin-bottom: 15px;
  border-radius: 5px;
  animation: fadeIn 0.5s ease;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

  </style>
</head>
<body>

  <div class="forgot-container">
    <h2>Forgot Password</h2>

    <div class="popup" id="popupMsg"></div>

    <form id="forgotForm">
      <!-- Role Selection -->
      <select name="role" id="roleSelect" class="input-field" required>
        <option value="">Select Role</option>
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
      </select>

      <!-- Role-Based Input Fields -->
      <div id="roleFields" style="display:none;">
        <div id="studentField" style="display:none;">
          <input type="text" name="student_roll" class="input-field" placeholder="Enter Roll Number">
        </div>

        <div id="teacherField" style="display:none;">
          <input type="text" name="teacher_id" class="input-field" placeholder="Enter Teacher ID">
        </div>

        <!-- New Password -->
        <input type="password" name="new_password" class="input-field" placeholder="Enter New Password" required>
        <input type="password" name="confirm_password" class="input-field" placeholder="Confirm Password" required>
      </div>

      <button type="submit" class="btn">Reset Password</button>

      <div class="link-container">
        Back to <a href="login.php">Login</a>
      </div>
    </form>
  </div>

  <script>
    const roleSelect = document.getElementById('roleSelect');
    const studentField = document.getElementById('studentField');
    const teacherField = document.getElementById('teacherField');
    const roleFields = document.getElementById('roleFields');
    const forgotForm = document.getElementById('forgotForm');
    const popupMsg = document.getElementById('popupMsg');

    // Show fields when role selected
    roleSelect.addEventListener('change', function() {
      if (this.value === 'student') {
        roleFields.style.display = 'block';
        studentField.style.display = 'block';
        teacherField.style.display = 'none';
      } else if (this.value === 'teacher') {
        roleFields.style.display = 'block';
        studentField.style.display = 'none';
        teacherField.style.display = 'block';
      } else {
        roleFields.style.display = 'none';
      }
    });

    forgotForm.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(forgotForm);

      fetch('../backend/forgot_password.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        popupMsg.style.display = 'block';
        popupMsg.textContent = data.message;
        popupMsg.style.background = data.status === 'success' ? '#4caf50' : '#f44336';

        if (data.status === 'success') {
          setTimeout(() => {
            window.location.href = "login.php";
          }, 2000);
        } else {
          setTimeout(() => { popupMsg.style.display = 'none'; }, 4000);
        }
      })
      .catch(err => {
        popupMsg.style.display = 'block';
        popupMsg.textContent = "Something went wrong!";
        popupMsg.style.background = '#f44336';
        setTimeout(() => { popupMsg.style.display = 'none'; }, 4000);
      });
    });
  </script>

</body>
</html>
