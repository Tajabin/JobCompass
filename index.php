<?php
session_start();
require_once "db.php";

$user_logged_in = isset($_SESSION['user']);
$email = $user_logged_in ? $_SESSION['user'] : null;

if ($user_logged_in) {
    // Fetch user name for greeting
    $stmt = $conn->prepare("SELECT fullName FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $fullName = $user['fullName'] ?? 'User';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $user_logged_in ? "Dashboard | JobCompass" : "Welcome | JobCompass" ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .nav {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }
        .nav a:hover {
            text-decoration: underline;
        }
        .container {
            text-align: center;
            padding: 60px;
        }
        .card {
            display: inline-block;
            background: white;
            padding: 40px 60px;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 600px;
        }
        h1 {
            margin-top: 0;
        }
        a.button {
            display: inline-block;
            margin: 10px;
            padding: 10px 25px;
            background: #007bff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
        }
        a.button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<?php if ($user_logged_in): ?>
    <!-- Logged-in Navbar -->
    <nav class="nav">
        <a href="pages/dashboard.php">Dashboard</a> |
        <a href="pages/jobs.php">Jobs</a> |
        <a href="pages/resources.php">Resources</a> |
        <a href="pages/profile.php">Profile</a> |
        <a href="server.php?action=logout">Logout</a>
    </nav>

    <div class="container">
        <div class="card">
            <h1>Welcome back, <?= htmlspecialchars($fullName) ?>!</h1>
            <p>Use the navigation above to explore job opportunities, resources, or update your profile.</p>
            <a href="pages/dashboard.php" class="button">Go to Dashboard</a>
        </div>
    </div>

<?php else: ?>
    <!-- Guest View -->
    <div class="container">
        <div class="card">
            <h1>Welcome to JobCompass</h1>
            <p>Your personal guide to career opportunities and skill growth.</p>
            <a href="pages/login.php" class="button">Login</a>
            <a href="pages/register.php" class="button">Register</a>
        </div>
    </div>
<?php endif; ?>

</body>
</html>
