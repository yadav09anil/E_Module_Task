<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Registration | Student Teacher Portal</title>
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

.register-container {
  background: white;
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  width: 380px;
  text-align: center;
  position: relative;
  overflow: hidden;
  transition: transform 0.3s ease;
}

.register-container:hover {
  transform: translateY(-5px);
}

.register-container::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 50%;
  height: 100%;
  background: linear-gradient(120deg, rgba(255,255,255,0.3), transparent);
  transform: skewX(-20deg);
  transition: 0.7s;
}

.register-container:hover::before {
  left: 125%;
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

.login-link {
  margin-top: 15px;
  font-size: 14px;
}

.login-link a {
  color: #2575fc;
  text-decoration: none;
  font-weight: 600;
}

.login-link a:hover {
  text-decoration: underline;
}

.popup {
  display: none;
  background: #f44336;
  color: white;
  padding: 12px;
  margin-bottom: 15px;
  border-radius: 5px;
  animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

  </style>
</head>
<body>

  <div class="register-container">
    <h2>Teacher Registration</h2>

    <div class="popup" id="popupMsg"></div>

    <form id="teacherForm">
      <input type="text" name="teacher_id" class="input-field" placeholder="Enter Teacher ID" required>
      <input type="text" name="teacher_name" class="input-field" placeholder="Enter Full Name" required>
      <input type="password" name="password" class="input-field" placeholder="Enter Password" required>
      <input type="password" name="confirm_password" class="input-field" placeholder="Confirm Password" required>
      
      <button type="submit" class="btn">Register</button>

      <div class="login-link">
        Already have an account? <a href="login.php">Login here</a>
      </div>
    </form>
  </div>

  <script>
    const teacherForm = document.getElementById('teacherForm');
    const popupMsg = document.getElementById('popupMsg');

    teacherForm.addEventListener('submit', async function(e){
      e.preventDefault();

      const formData = new FormData(teacherForm);

      const response = await fetch("../backend/teacher_register.php", {
        method: "POST",
        body: formData
      });

      const data = await response.json();

      popupMsg.style.display = 'block';
      popupMsg.textContent = data.message;

      if (data.status === "success") {
        popupMsg.style.background = '#4CAF50';
        setTimeout(() => {
          window.location.href = "login.php";
        }, 2000);
      } else {
        popupMsg.style.background = '#f44336';
        setTimeout(() => { popupMsg.style.display = 'none'; }, 4000);
      }
    });
  </script>

</body>
</html>
