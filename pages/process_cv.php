<?php
session_start();

// Check login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Correct path to dictionary
$skills = include "../api/skills_dictionary.php";

// Get CV text
$cvText = strtolower($_POST['cv_text'] ?? '');

$foundSkills = [];

// Find skills
foreach ($skills as $skill => $synonyms) {
    foreach ($synonyms as $synonym) {
        if (strpos($cvText, strtolower($synonym)) !== false) {
            $foundSkills[] = ucfirst($skill);
        }
    }
}

$foundSkills = array_unique($foundSkills);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Extracted Skills</title>
</head>
<body>

<h2>Extracted Skills from Your CV</h2>

<?php if (!empty($foundSkills)): ?>
    <p>
        <?php foreach ($foundSkills as $skill): ?>
            <span style="padding:6px;border:1px solid #444;margin:4px;display:inline-block;">
                <?= $skill ?>
            </span>
        <?php endforeach ?>
    </p>
<?php else: ?>
    <p>No skills detected. Try pasting a more detailed CV text.</p>
<?php endif; ?>

<br><br>

<!-- ⭐ Button to return to dashboard -->
<a href="../index.php" 
   style="padding:10px 18px; background:#007BFF; color:white; border-radius:5px; 
          text-decoration:none; font-size:16px;">
    Return to Dashboard
</a>

</body>
</html>
