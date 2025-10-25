<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Registration</title>
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
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  width: 380px;
  text-align: center;
  transition: transform 0.3s ease;
  position: relative;
  overflow: hidden;
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
  box-shadow: 0 0 8px rgba(37, 117, 252, 0.3);
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
  display: block;
  font-size: 14px;
  color: #555;
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
  position: fixed;
  top: 20px;
  right: 20px;
  background-color: #ff4c4c;
  color: white;
  padding: 15px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  opacity: 0;
  transform: translateY(-20px);
  transition: all 0.5s ease;
  z-index: 9999;
}

.popup.show {
  opacity: 1;
  transform: translateY(0);
}

.popup.success {
  background-color: #28a745;
}

  </style>
</head>
<body>

  <div class="register-container">
    <h2>Student Registration</h2>
    <form id="studentForm">
      
      <input type="text" name="roll_number" class="input-field" placeholder="Enter Roll Number" required>
      <input type="text" name="name" class="input-field" placeholder="Enter Full Name" required>

  
      <select name="gender" class="input-field" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
  
      </select>

      <select name="class" class="input-field" required>
        <option value="">Select Class</option>
        <option value="10th">10th</option>
        <option value="11th">11th</option>
        <option value="12th">12th</option>
      </select>

      <input type="tel" name="phone" class="input-field" placeholder="Enter Phone Number" pattern="[0-9]{10}" required>

 
      <input type="date" id="dob" name="dob" class="input-field" required>

      <input type="password" id="password" name="password" class="input-field" placeholder="Enter Password" required>
      <input type="password" id="confirm_password" name="confirm_password" class="input-field" placeholder="Confirm Password" required>

      <button type="submit" class="btn">Register</button>

      <div class="login-link">
        Already have an account? <a href="login.php">Login here</a>
      </div>
    </form>
  </div>


  <div id="popup" class="popup"></div>

  <script>
 
    document.getElementById('dob').max = new Date().toISOString().split("T")[0];


    function showPopup(message, type='error') {
      const popup = document.getElementById('popup');
      popup.innerText = message;
      popup.className = `popup show ${type}`;
      setTimeout(() => {
        popup.className = 'popup'; 
      }, 3000);
    }

    document.getElementById('studentForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      try {
        const response = await fetch('../backend/student_register.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        showPopup(result.message, result.status === 'success' ? 'success' : 'error');

        if (result.status === 'success') {
          this.reset();
          setTimeout(() => {
            window.location.href = 'login.php';
          }, 1500);
        }
      } catch (error) {
        showPopup('Roll number already exists. Please use a different one');
      }
    });
  </script>

</body>
</html>
