<?php
// server.php - main backend entry for jobcompass.com
session_start();
require_once "db.php";

// Helper: current user email
function current_user_email() {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

/*
REGISTER
Expected POST:
fullName, email, password, education, experience, track
*/
if (isset($_POST['register'])) {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $education = $_POST['education'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $track = $_POST['track'] ?? '';
    $password_raw = $_POST['password'] ?? '';

    if (!$fullName || !$email || !$password_raw) {
        die("Required fields missing");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (fullName, email, password, educationLevel, experienceLevel, preferredTrack) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fullName, $email, $password, $education, $experience, $track);
    if ($stmt->execute()) {
        $_SESSION['user'] = $email;
        header("Location: pages/dashboard.php");
        exit();
    } else {
        die("Error: " . $stmt->error);
    }
}

/*
LOGIN
Expected POST: email, password
*/
if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $email;
            header("Location: pages/dashboard.php");
            exit();
        }
    }
    die("Invalid login credentials");
}

/*
LOGOUT
*/
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: pages/login.php");
    exit();
}
?>
