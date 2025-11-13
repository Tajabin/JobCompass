<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
require_once "../db.php";
$res = $conn->query("SELECT * FROM resources ORDER BY id DESC");
$resources = [];
while ($r = $res->fetch_assoc()) {
    $r['relatedSkills'] = json_decode($r['relatedSkills'] ?? "[]");
    $resources[] = $r;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Resources | jobcompass</title>
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
    <h2>Learning Resources</h2>
    <?php foreach ($resources as $r): ?>
        <div class="card">
            <h3><a href="<?= htmlspecialchars($r['url']) ?>" target="_blank"><?= htmlspecialchars($r['title']) ?></a></h3>
            <p>Platform: <?= htmlspecialchars($r['platform']) ?> | Cost: <?= htmlspecialchars($r['cost']) ?></p>
            <p>Related skills: <?= implode(', ', $r['relatedSkills']) ?></p>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>