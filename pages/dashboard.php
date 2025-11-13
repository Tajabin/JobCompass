<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
require_once "../db.php";
$email = $_SESSION['user'];

// fetch profile
$stmt = $conn->prepare("SELECT fullName, email, educationLevel, experienceLevel, preferredTrack, skills FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$profile['skills'] = json_decode($profile['skills'] ?? "[]");

// fetch jobs & resources for matching
$jobs_res = $conn->query("SELECT * FROM jobs");
$jobs = [];
while ($r = $jobs_res->fetch_assoc()) {
    $r['requiredSkills'] = json_decode($r['requiredSkills'] ?? "[]");
    $jobs[] = $r;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Dashboard | jobcompass</title>
<link rel="stylesheet" href="../styles.css">
</head>
<body>
<nav class="nav">
    <a href="dashboard.php">Dashboard</a> |
    <a href="jobs.php">Jobs</a> |
    <a href="resources.php">Resources</a> |
    <a href="profile.php">Profile</a> |
    <a href="../server.php?action=logout">Logout</a>
</nav>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($profile['fullName']) ?>!</h2>
    <p>Your preferred track: <?= htmlspecialchars($profile['preferredTrack']) ?></p>
</div>
</body>
</html>
