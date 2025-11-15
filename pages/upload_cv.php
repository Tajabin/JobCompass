<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Your CV</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 450px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 18px;
            background: #0066ff;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #004ecc;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Upload Your CV for Skill Extraction</h2>

    <form action="process_cv.php" method="POST">
        <label for="cv_text">Paste CV Text or Upload a File:</label>
        <textarea name="cv_text" id="cv_text" rows="12" placeholder="Paste your CV text here..." required></textarea><br><br>

        <button type="submit">Extract Skills</button>
    </form>
</div>

</body>
</html>
