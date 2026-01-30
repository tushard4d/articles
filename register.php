<?php
include 'confi.php';  

$message = '';
$message_type = 'error';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role'] ?? '');

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $message = "All fields are required!";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address!";
    } 
    elseif (strlen($password) < 6 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[.@]/', $password)
    ) {
        $message = "Password must be at least 6 characters long and include one uppercase letter, one lowercase letter, and one special character ( . or @ ).";
    } 
    else {

        $table = ($role === 'admin') ? 'admins' : 'users';

        $check = $conn->prepare("SELECT email FROM $table WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = ucfirst($role) . " already exists!";
        } 
        else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare(
                "INSERT INTO $table (name, email, password) VALUES (?, ?, ?)"
            );
            $insert->bind_param("sss", $name, $email, $hashed_password);

            if ($insert->execute()) {
                $message = ucfirst($role) . " registered successfully!";
                $message_type = "success";
            } else {
                $message = "Something went wrong!";
            }

            $insert->close();
        }
        $check->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      background: linear-gradient(to bottom right, #e0f7fa, #ffffff);
      margin: 0;
      padding: 20px;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .container {
      background: white;
      padding: 35px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.12);
      width: 100%;
      max-width: 420px;
    }
    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 25px;
      font-size: 28px;
    }
    label {
      display: block;
      margin-bottom: 6px;
      color: #444;
      font-weight: 600;
      font-size: 15px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"]
     {
      width: 100%;
      padding: 12px;
      margin-bottom: 18px;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
      font-size: 16px;
      transition: border-color 0.3s;
    }
    input:focus {
      border-color: #4CAF50;
      outline: none;
    }
    .btn {
      width: 100%;
      padding: 14px;
      background: #4CAF50;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 17px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }
    .btn:hover {
      background: #45a049;
    }
    .message {
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 6px;
      text-align: center;
      font-weight: 500;
    }
    .error { background: #ffebee; color: #c62828; }
    .success { background: #e8f5e9; color: #2e7d32; }
    .login-link {
      text-align: center;
      margin-top: 20px;
      font-size: 15px;
    }
    .login-link a {
      color: #1976d2;
      text-decoration: none;
      font-weight: 600;
    }
    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Register</h2>

  <?php if (!empty($message)): ?>
    <div class="message <?= $message_type ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <form method="POST">

  <label>Full Name</label>
  <input type="text" name="name" required>

  <label>Email</label>
  <input type="email" name="email" required>

  <label>Password</label>
  <input type="password" name="password" required>

  <label>Register As</label>
  <select name="role" required>
    <option value="">Select Role</option>
    <option value="admin">Admin</option>
    <option value="user">User</option>
  </select>

  <button type="submit" class="btn">Register</button>
</form>

  <div class="login-link">
    Already have an account? <a href="login.php">Login</a>
  </div>
</div>

</body>
</html>
