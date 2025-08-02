<?php
session_start();
require_once "./config/db.php";
require_once __DIR__ . '/includes/User.php';


$user = new User();
$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);


    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
       
            if ($user->register($name, $email, $password)) {
                $stmt = $user->conn->prepare("SELECT user_id, username, role FROM users WHERE username = ? LIMIT 1");
                $stmt->bind_param("s", $name);
                $stmt->execute();
                $stmt->bind_result($user_id, $db_username, $role);
                $stmt->fetch();
                $stmt->close();

                $_SESSION["user_id"] = $user_id;
                $_SESSION["username"] = $db_username;
                $_SESSION["role"] = $role;

                header("Location: user/user_dashboard.php");
                exit();
            } else {
                $error = "Registration failed. Try a different username.";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - Disaster Management System</title>
  <link rel="stylesheet" href="register.css" />
</head>
<body>

  <div class="register_card">
    <div class="card_header">
      <h2>Create Account</h2>
      <p>Register to report and manage disasters</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div style="color:red; padding:10px; text-align:center;">
        <?php foreach ($errors as $error): ?>
          <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

 
    <form method="post" action="">
      <div class="form">
        <label class="form_label" for="username">Username</label>
        <input type="text" id="username" name="username" class="text_box" required>
      </div>

      <div class="form">
        <label class="form_label" for="email">Email Address</label>
        <input type="text" id="email" name="email" class="text_box" required>
      </div>

      <div class="form">
        <label class="form_label" for="password">Password</label>
        <input type="password" id="password" name="password" class="text_box" required>
      </div>

      <div class="form">
        <label class="form_label" for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="text_box" required>
      </div>

      <button type="submit" class="btnsubmit">Register</button>

      <p style="text-align:center; margin-top:1rem;">
        Already have an account? <a href="./auth/login.php" style="color:#197d2">Login here</a>
      </p>
    </form>
  </div>

</body>
</html>
