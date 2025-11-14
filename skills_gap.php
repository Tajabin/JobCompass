<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

require_once "../db.php";

$email = $_SESSION['user'];

// Fetch user skills from DB
$stmt = $conn->prepare("SELECT skills FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$userRow = $stmt->get_result()->fetch_assoc();

$userSkills = json_decode($userRow['skills'], true);
if (!$userSkills) $userSkills = [];  
$userSkillsLower = array_map('strtolower', $userSkills);

// Fetch all jobs
$jobs = $conn->query("SELECT * FROM jobs");

// Fetch resources
$resources = $conn->query("SELECT * FROM resources");

$resourceMap = [];

// Build resource map: skill → [resource list]
while ($r = $resources->fetch_assoc()) {
    $skills = json_decode($r['relatedSkills'], true);

    if (!$skills) {
        $skills = array_map('trim', explode(',', $r['relatedSkills']));
    }

    foreach ($skills as $skill) {
        $resourceMap[strtolower($skill)][] = $r;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Skill Gap Analysis</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .job-box {
            background:#fff; padding:15px; margin:15px 0;
            border-radius:6px; box-shadow:0 3px 10px rgba(0,0,0,0.1);
        }
        .resource {
            background:#f4f4f4; padding:10px; margin:8px 0;
            border-left:4px solid #007bff;
        }
    </style>
</head>
<body>

<nav class="nav">
    <a href="dashboard.php">Dashboard</a> |
    <a href="jobs.php">Jobs</a> |
    <a href="resources.php">Resources</a> |
    <a href="profile.php">Profile</a> |
    <a href="skill_gap.php"><b>Skill Gap Analysis</b></a> |
    <a href="../server.php?action=logout">Logout</a>
</nav>

<div class="container">
    <h2>Skill Gap Analysis & Learning Suggestions</h2>

    <?php while ($job = $jobs->fetch_assoc()): ?>

        <?php
        // FIXED — handle job skills JSON
        $rawSkills = $job['requiredSkills'];
        $decoded = json_decode($rawSkills, true);

        if ($decoded) {
            $jobSkills = array_map('trim', $decoded);
        } else {
            $jobSkills = array_map('trim', explode(',', $rawSkills));
        }

        $jobSkillsLower = array_map('strtolower', $jobSkills);

        // Match calculation
        $matched = array_intersect($userSkillsLower, $jobSkillsLower);
        $missing = array_diff($jobSkillsLower, $userSkillsLower);

        $matchPercent = round((count($matched) / max(count($jobSkillsLower), 1)) * 100);
        ?>

        <div class="job-box">
            <h3><?= htmlspecialchars($job['title']) ?> — <?= $matchPercent ?>% match</h3>

            <?php if (count($missing) > 0): ?>
                <p><strong>⚠ Missing:</strong> <?= implode(", ", array_map('ucfirst', $missing)); ?></p>
            <?php else: ?>
                <p style="color:green;"><strong>✔ You match all required skills!</strong></p>
            <?php endif; ?>

            <h4>Missing Skills (Skill Gap):</h4>
            <p><?= implode(", ", array_map('ucfirst', $missing)); ?></p>

            <h4>Recommended Learning Resources:</h4>

            <?php foreach ($missing as $skill): 
                $s = strtolower($skill);
            ?>

                <?php if (isset($resourceMap[$s])): ?>

                    <?php foreach ($resourceMap[$s] as $res): ?>
                        <div class="resource">
                            <strong><?= htmlspecialchars($res['title']); ?></strong><br>
                            Platform: <?= htmlspecialchars($res['platform']); ?><br>
                            Cost: <?= htmlspecialchars($res['cost']); ?><br>
                            <a href="<?= htmlspecialchars($res['url']); ?>" target="_blank">Open Resource</a>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <div class="resource">No resources found for <b><?= ucfirst($skill) ?></b> (add in resources table)</div>
                <?php endif; ?>

            <?php endforeach; ?>

        </div>

    <?php endwhile; ?>

</div>

</body>
</html>
