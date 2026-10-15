<?php
session_start();
require "../config/db.php";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$_POST["username"]]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($_POST["password"], $admin["password"])) {
        $_SESSION["admin"] = $admin["username"];
        header("Location: dashboard.php");
        exit;
    }
    $error = "Invalid username or password";
}
?>
<!DOCTYPE html>
<html>
<head><title>Admin Login</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<div class="container">
  <h1>Admin Login</h1>
  <form method="POST">
    <label>Username</label><input type="text" name="username" required>
    <label>Password</label><input type="password" name="password" required>
    <p class="error"><?= htmlspecialchars($error) ?></p>
    <button type="submit">Login</button>
  </form>
</div>
</body>
</html>