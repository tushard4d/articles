<?php
include 'confi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Email and password required!";
    } else {
        $sql = "SELECT name, email, password FROM admins WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($name, $user_email, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $user_email;
                header("Location:dist/index.html");
                exit;
            } else {
                $message = "Invalid password!";
            }
        } else {
            $message = "Email not registered!";
        }
        $stmt->close();
    };
    if (empty($email) || empty($password)) {
        $message = "Email and password required!";
    } else {
        $sql = "SELECT name, email, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($name, $user_email, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $user_email;

                header("Location:userdash.php");
                exit;
            } else {
                $message = "Invalid password!";
            }
        } else {
            $message = "Email not registered!";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background: linear-gradient(to bottom right, #e0f7fa, #ffffff);
        margin: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container {
        background: #ffffff;
        padding: 35px 30px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        width: 100%;
        max-width: 420px;
        text-align: center;
    }

    h2 {
        margin-bottom: 25px;
        color: #333;
        font-size: 28px;
    }

    input {
        width: 100%;
        padding: 12px;
        margin-bottom: 18px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
        box-sizing: border-box;
    }

    input:focus {
        border-color: #4CAF50;
        outline: none;
    }

    button {
        width: 100%;
        padding: 14px;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 17px;
        font-weight: bold;
        cursor: pointer;
    }

    button:hover {
        background: #45a049;
    }

    .error-msg {
        background: #ffebee;
        color: #c62828;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .register-link {
        margin-top: 20px;
        font-size: 15px;
    }

    .register-link a {
        color: #1976d2;
        text-decoration: none;
        font-weight: 600;
    }

    .register-link a:hover {
        text-decoration: underline;
    }
</style>
</head>

<body>

    <div class="container">

    <h2>Login</h2>

    <?php if (isset($message)): ?>
        <div class="error-msg"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter email" required>
        <input type="password" name="password" placeholder="Enter password" required>
        <button type="submit">Login</button>
    </form>

    <div class="register-link">
        <a href="register.php">Create an account</a>
    </div>

</div>


</body>
</html>
