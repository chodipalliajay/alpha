<?php
require "auth_check.php";
require "../config/db.php";

if (isset($_GET["id"], $_GET["status"]) && in_array($_GET["status"], ["Approved", "Rejected", "Pending"])) {
    $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?")->execute([$_GET["status"], $_GET["id"]]);
    header("Location: dashboard.php");
    exit;
}

$search = $_GET["q"] ?? "";
$stmt = $pdo->prepare("SELECT * FROM applications WHERE full_name LIKE ? OR course LIKE ? OR reference_no LIKE ? ORDER BY applied_at DESC");
$stmt->execute(["%$search%", "%$search%", "%$search%"]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<h1>Applications (<?= count($rows) ?>)</h1>
<p><a href="logout.php">Logout</a></p>
<form method="GET">
  <input type="text" name="q" placeholder="Search name, course or reference no." value="<?= htmlspecialchars($search) ?>">
</form><br>

<table>
<tr>
  <th>Ref No</th><th>Name</th><th>Email</th><th>Phone</th><th>Course</th>
  <th>%</th><th>Status</th><th>Document</th><th>PDF</th><th>Action</th>
</tr>
<?php foreach ($rows as $r): ?>
<tr>
  <td><?= htmlspecialchars($r["reference_no"] ?? "-") ?></td>
  <td><?= htmlspecialchars($r["full_name"]) ?></td>
  <td><?= htmlspecialchars($r["email"]) ?></td>
  <td><?= htmlspecialchars($r["phone"]) ?></td>
  <td><?= htmlspecialchars($r["course"]) ?></td>
  <td><?= $r["previous_percentage"] ?></td>
  <td><?= $r["status"] ?></td>
  <td><?php if ($r["document_path"]): ?><a href="../<?= htmlspecialchars($r["document_path"]) ?>" target="_blank">View</a><?php endif; ?></td>
  <td><?php if ($r["token"]): ?><a href="../download_pdf.php?ref=<?= urlencode($r["reference_no"]) ?>&token=<?= urlencode($r["token"]) ?>">PDF</a><?php endif; ?></td>
  <td>
    <a href="?id=<?= $r["id"] ?>&status=Approved">Approve</a> |
    <a href="?id=<?= $r["id"] ?>&status=Rejected">Reject</a>
  </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>