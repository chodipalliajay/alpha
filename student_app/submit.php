<?php
require "config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

$full_name = trim($_POST["full_name"] ?? "");
$email     = trim($_POST["email"] ?? "");
$phone     = trim($_POST["phone"] ?? "");
$dob       = $_POST["dob"] ?? "";
$gender    = $_POST["gender"] ?? "";
$course    = $_POST["course"] ?? "";
$percent   = $_POST["previous_percentage"] ?? "";
$address   = trim($_POST["address"] ?? "");

if (!$full_name || !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !preg_match("/^[6-9][0-9]{9}$/", $phone) || !$dob || !$gender ||
    !$course || $percent === "" || $percent < 0 || $percent > 100 || !$address) {
    die("Invalid input. Go back and check the form.");
}

// Duplicate email check
$check = $pdo->prepare("SELECT id FROM applications WHERE email = ?");
$check->execute([$email]);
if ($check->fetch()) {
    die("An application with this email already exists.");
}

// File upload
$docPath = null;
if (!empty($_FILES["document"]["name"])) {
    $allowed = ["pdf", "jpg", "jpeg", "png"];
    $ext = strtolower(pathinfo($_FILES["document"]["name"], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed) || $_FILES["document"]["size"] > 2 * 1024 * 1024) {
        die("Invalid file type or file larger than 2MB.");
    }
    $newName = uniqid("doc_") . "." . $ext;
    move_uploaded_file($_FILES["document"]["tmp_name"], "uploads/" . $newName);
    $docPath = "uploads/" . $newName;
}

// Save
$sql = "INSERT INTO applications
        (full_name, email, phone, dob, gender, course, previous_percentage, address, document_path)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$full_name, $email, $phone, $dob, $gender, $course, $percent, $address, $docPath]);

// Reference number and token
$id    = $pdo->lastInsertId();
$ref   = "APP" . date("Y") . str_pad($id, 5, "0", STR_PAD_LEFT);
$token = bin2hex(random_bytes(16));

$pdo->prepare("UPDATE applications SET reference_no = ?, token = ? WHERE id = ?")
    ->execute([$ref, $token, $id]);

header("Location: success.php?ref=$ref&token=$token");
exit;