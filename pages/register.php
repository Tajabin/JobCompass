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
<title>Register | jobcompass.com</title>
<link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="form-container">
    <h2>Register — JobCompass</h2>
    <form method="POST" action="../server.php">
        <input type="text" name="fullName" placeholder="Full name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password (min 6 chars)" required>

        <select name="education" required>
            <option value="">Education level / Department</option>
            <option value="BSc CSE">BSc CSE</option>
            <option value="BBA">BBA</option>
        </select>

        <select name="experience" required>
            <option value="">Experience Level</option>
            <option value="Fresher">Fresher</option>
            <option value="Mid">Mid</option>
        </select>

        <select name="track" required>
            <option value="">Preferred Track</option>
            <option value="Web Development">Web Development</option>
            <option value="Data Science">Data Science</option>
        </select>

        <button type="submit" name="register">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>
