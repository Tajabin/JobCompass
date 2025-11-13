<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login | jobcompass.com</title>
<link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="form-container">
    <h2>Login — JobCompass</h2>
    <form method="POST" action="../server.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
</html>
