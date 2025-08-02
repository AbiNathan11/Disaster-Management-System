<?php
session_start();
include_once "../config/db.php";

// Handle login form submission
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $db_username, $db_password, $role);
        $stmt->fetch();

        // ✅ Check password using password_verify
        if (password_verify($password, $db_password)) {
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $db_username;
            $_SESSION["role"] = $role;

            if ($role === "admin") {
                header("Location: ../admin/admin_dashboard.php");
            } else {
                header("Location: ../user/user_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Disaster Management System</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <span class="icon">🛡️</span>
        <h2>Disaster Management</h2>
        <p>Admin/User Login</p>
    </div>
    <?php if (!empty($error)): ?>
        <div class="alert" id="alertBox">
            <span>❌ <?php echo $error; ?></span>
            <button type="button" class="close-btn" onclick="document.getElementById('alertBox').style.display='none'">×</button>
        </div>
    <?php endif; ?>
    <form method="post" action="" autocomplete="off">
        <div class="form-group">
            <label class="form-label" for="username">👤 Username or Email</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username or Email" required autofocus>
        </div>
        <div class="form-group">
            <label class="form-label" for="password">🔒 Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <button type="button" class="pw-toggle-btn" onclick="togglePassword()" tabindex="-1" aria-label="Show/Hide Password"><span id="toggleIcon">👁️</span></button>
        </div>
        <div class="remember-row">
            <div>
                <input type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <a href="#" class="forgot-link">Forgot Password?</a>
        </div>
        <button class="btn" type="submit">Login</button>
    </form>
</div>
<script>
function togglePassword() {
    const password = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    if (password_verify($password, $db_password)) {
        password.type = 'text';
        icon.textContent = '🙈';
    } else {
        password.type = 'password';
        icon.textContent = '👁️';
    }
}
</script>
</body>
</html>