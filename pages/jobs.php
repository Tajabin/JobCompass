<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
require_once "../db.php";

$track = $_GET['track'] ?? '';
$location = $_GET['location'] ?? '';
$type = $_GET['type'] ?? '';

$sql = "SELECT * FROM jobs WHERE 1=1";
if ($track) $sql .= " AND track = '". $conn->real_escape_string($track) ."'";
if ($location) $sql .= " AND location LIKE '%". $conn->real_escape_string($location) ."%'";
if ($type) $sql .= " AND type = '". $conn->real_escape_string($type) ."'";

$res = $conn->query($sql);
$jobs = [];
while ($r = $res->fetch_assoc()) {
    $r['requiredSkills'] = json_decode($r['requiredSkills'] ?? "[]");
    $jobs[] = $r;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Jobs | jobcompass</title>
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
    <h2>Available Jobs</h2>
    <?php foreach ($jobs as $job): ?>
        <div class="card">
            <h3><?= htmlspecialchars($job['title']) ?></h3>
            <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
            <p><strong>Required Skills:</strong> <?= implode(', ', $job['requiredSkills']) ?></p>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
