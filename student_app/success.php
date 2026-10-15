<?php
require "config/db.php";

$ref   = $_GET["ref"] ?? "";
$token = $_GET["token"] ?? "";

$stmt = $pdo->prepare("SELECT full_name FROM applications WHERE reference_no = ? AND token = ?");
$stmt->execute([$ref, $token]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) { die("Invalid request."); }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Application Submitted</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container" style="text-align:center">
    <h1>Application Submitted ✅</h1>
    <p>Thank you, <strong><?= htmlspecialchars($app["full_name"]) ?></strong>!</p>
    <p>Your reference number: <strong><?= htmlspecialchars($ref) ?></strong></p>
    <p>Please download and keep your application copy.</p>

    <a href="download_pdf.php?ref=<?= urlencode($ref) ?>&token=<?= urlencode($token) ?>">
      <button type="button">⬇ Download PDF</button>
    </a>
    <p><a href="index.html">Submit another application</a></p>
  </div>
</body>
</html>