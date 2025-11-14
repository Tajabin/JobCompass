<?php
// ai_helper.php

// Normalize skills to lowercase
function normalizeSkills($skills) {
    if (!is_array($skills)) return [];
    return array_map(fn($s) => strtolower(trim($s)), $skills);
}

// Calculate match %
function calculateMatchScore($userSkills, $jobSkills) {
    $user = normalizeSkills($userSkills);
    $job  = normalizeSkills($jobSkills);

    if (empty($job)) return 0;

    $matchCount = count(array_intersect($user, $job));
    return round(($matchCount / count($job)) * 100);
}

// Get missing skills (skill gap)
function getSkillGap($userSkills, $jobSkills) {
    $user = normalizeSkills($userSkills);
    $job  = normalizeSkills($jobSkills);

    return array_diff($job, $user);
}

// AI-style explanation for UI
function explainMatch($userSkills, $jobSkills) {
    $user = normalizeSkills($userSkills);
    $job  = normalizeSkills($jobSkills);

    $matched = array_intersect($user, $job);
    $missing = array_diff($job, $user);

    $text = "";

    if (!empty($matched)) {
        $text .= "✔ Matched skills: " . implode(", ", array_map('ucfirst', $matched)) . ". ";
    }

    if (!empty($missing)) {
        $text .= "⚠ Missing skills: " . implode(", ", array_map('ucfirst', $missing)) . ". ";
    }

    if (empty($matched)) {
        $text .= "⚠ No matching skills found. Start with beginner-friendly resources.";
    }

    return $text;
}
?>
