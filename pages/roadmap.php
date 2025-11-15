<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

require "../db.php";
require "../api/roadmap_ai.php";

$email = $_SESSION['user'];
$stmt = $conn->prepare("SELECT fullName, skills FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$userSkills = explode(",", $user['skills']);

$roadmap = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target = $_POST['target_role'];
    $months = $_POST['timeframe'];
    $hours  = $_POST['hours'];

    $roadmap = generateRoadmap($userSkills, $target, $months, $hours);

    // Save roadmap for user
    $save = $conn->prepare("UPDATE users SET roadmap=? WHERE email=?");
    $save->bind_param("ss", json_encode($roadmap), $email);
    $save->execute();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Career Roadmap</title>
<style>
body {font-family: Arial; padding:20px;}
.card {background:#fff; padding:20px; border-radius:8px; margin:20px 0; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
h2 {color:#333;}
.phase {background:#f3f5ff; padding:10px; border-radius:6px; margin-bottom:10px;}
</style>
</head>
<body>

<h2>AI Career Roadmap Generator</h2>

<form method="POST">
    <label>Target Role:</label><br>
    <select name="target_role" required>
        <option value="frontend developer">Frontend Developer</option>
        <option value="backend developer">Backend Developer</option>
        <option value="data analyst">Data Analyst</option>
        <option value="ui/ux designer">UI/UX Designer</option>
    </select>
    <br><br>

    <label>Timeframe:</label><br>
    <select name="timeframe">
        <option value="3">3 Months</option>
        <option value="6">6 Months</option>
    </select>
    <br><br>

    <label>Daily Learning Hours:</label><br>
    <input type="number" name="hours" value="2">
    <br><br>

    <button type="submit">Generate Roadmap</button>
</form>

<?php if ($roadmap): ?>
<div class="card">
    <h3><?php echo $roadmap['target']; ?> Roadmap (<?php echo $roadmap['months']; ?> Months)</h3>
    <p><b>Recommended Learning Time:</b> <?php echo $roadmap['learning_time']; ?> hour/day</p>

    <?php foreach ($roadmap['phases'] as $p): ?>
        <div class="phase">
            <h4><?php echo $p['phase']; ?></h4>
            <ul>
            <?php foreach ($p['topics'] as $t): ?>
                <li><?php echo $t; ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>

    <p><b>Project Suggestion:</b> <?php echo $roadmap['projects']; ?></p>
    <p><b>Start applying:</b> <?php echo $roadmap['apply_start']; ?></p>

    <form method="POST" action="roadmap_download.php">
        <button type="submit">Download PDF</button>
    </form>
</div>
<?php endif; ?>

</body>
</html>
