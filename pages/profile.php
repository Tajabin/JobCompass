<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
require_once "../db.php";
$email = $_SESSION['user'];

// fetch existing
$stmt = $conn->prepare("SELECT fullName, email, skills, about, cv_text FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$skills = json_decode($profile['skills'] ?? "[]");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Profile | jobcompass</title>
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
    <h2>Edit Profile</h2>
    <form method="POST" action="../server.php">
        <input type="hidden" name="update_profile" value="1">
        <label>Full name: <b><?= htmlspecialchars($profile['fullName']) ?></b></label><br><br>

        <label>Skills (add multiple):</label><br>
        <div id="skills-list">
            <?php foreach ($skills as $s): ?>
                <div class="skill-item">
                    <input type="text" name="skills[]" value="<?= htmlspecialchars($s) ?>">
                    <button type="button" onclick="this.parentNode.remove()">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" onclick="addSkill()">Add skill</button>

        <br><br>
        <label>About / Experience notes</label><br>
        <textarea name="about" rows="4" cols="60"><?= htmlspecialchars($profile['about']) ?></textarea><br><br>

        <label>Paste CV / Notes (text)</label><br>
        <textarea name="cv_text" rows="8" cols="80"><?= htmlspecialchars($profile['cv_text']) ?></textarea><br><br>

        <button type="submit">Save profile</button>
    </form>
</div>

<script>
function addSkill(){
    const div = document.createElement('div');
    div.className = 'skill-item';
    div.innerHTML = '<input type="text" name="skills[]" placeholder="e.g. JavaScript"><button type="button" onclick="this.parentNode.remove()">Remove</button>';
    document.getElementById('skills-list').appendChild(div);
}
</script>
</body>
</html>