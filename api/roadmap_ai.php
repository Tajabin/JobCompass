<?php
session_start();
require "../db.php";

if (!isset($_SESSION['user'])) {
    die("Not logged in");
}

$email = $_SESSION['user'];

// Fetch stored roadmap JSON
$stmt = $conn->prepare("SELECT roadmap FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$roadmap = json_decode($user['roadmap'], true);

// Build text content
$content = "Career Roadmap ({$roadmap['months']} Months)\n";
$content .= "Target Role: {$roadmap['target']}\n";
$content .= "Daily Learning: {$roadmap['learning_time']} hours/day\n\n";

foreach ($roadmap['phases'] as $p) {
    $content .= $p['phase'] . "\n";
    foreach ($p['topics'] as $topic) {
        $content .= " - $topic\n";
    }
    $content .= "\n";
}

$content .= "Project Suggestion: {$roadmap['projects']}\n";
$content .= "Start Applying: {$roadmap['apply_start']}\n";

// Download as TXT
header("Content-Type: text/plain");
header("Content-Disposition: attachment; filename=career_roadmap.txt");

echo $content;
exit;
?>
