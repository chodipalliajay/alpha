<?php
require "config/db.php";
require_once(__DIR__ . '/lib/fpdf/fpdf.php');


$ref   = $_GET["ref"] ?? "";
$token = $_GET["token"] ?? "";

$stmt = $pdo->prepare("SELECT * FROM applications WHERE reference_no = ? AND token = ?");
$stmt->execute([$ref, $token]);
$a = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$a) { die("Invalid or expired link."); }

function t($text) {
    return iconv("UTF-8", "windows-1252//TRANSLIT//IGNORE", (string)$text);
}

class PDF extends FPDF {
    function Header() {
        $this->SetFillColor(52, 152, 219);
        $this->Rect(0, 0, 210, 28, "F");
        $this->SetTextColor(255, 255, 255);
        $this->SetFont("Arial", "B", 18);
        $this->SetY(9);
        $this->Cell(0, 10, "STUDENT APPLICATION FORM", 0, 1, "C");
        $this->Ln(14);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont("Arial", "I", 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, "This is a system generated copy | Page " . $this->PageNo(), 0, 0, "C");
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTextColor(0, 0, 0);

$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(95, 8, "Reference No: " . $a["reference_no"], 0, 0);
$pdf->SetFont("Arial", "", 11);
$pdf->Cell(0, 8, "Date: " . date("d M Y, h:i A", strtotime($a["applied_at"])), 0, 1, "R");
$pdf->Ln(4);

$fields = [
    "Full Name"           => $a["full_name"],
    "Email"               => $a["email"],
    "Phone"               => $a["phone"],
    "Date of Birth"       => date("d M Y", strtotime($a["dob"])),
    "Gender"              => $a["gender"],
    "Course Applied"      => $a["course"],
    "Previous Percentage" => $a["previous_percentage"] . " %",
    "Application Status"  => $a["status"],
];

foreach ($fields as $label => $value) {
    $pdf->SetFont("Arial", "B", 11);
    $pdf->SetFillColor(236, 240, 245);
    $pdf->Cell(60, 10, t($label), 1, 0, "L", true);
    $pdf->SetFont("Arial", "", 11);
    $pdf->Cell(0, 10, t($value), 1, 1);
}

$pdf->SetFont("Arial", "B", 11);
$pdf->Cell(60, 10, "Address", 1, 0, "L", true);
$pdf->SetFont("Arial", "", 11);
$pdf->MultiCell(0, 10, t($a["address"]), 1);

$pdf->SetFont("Arial", "B", 11);
$pdf->Cell(60, 10, "Document Uploaded", 1, 0, "L", true);
$pdf->SetFont("Arial", "", 11);
$pdf->Cell(0, 10, $a["document_path"] ? "Yes" : "No", 1, 1);

$pdf->Ln(10);
$pdf->SetFont("Arial", "I", 10);
$pdf->MultiCell(0, 6, "I hereby declare that the information provided above is true to the best of my knowledge.");

$pdf->Output("D", "Application_" . $a["reference_no"] . ".pdf");