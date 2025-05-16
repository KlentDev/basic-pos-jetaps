<?php

// Include necessary files and start the session
require("fpdf182/fpdf.php");
include_once "connectdb.php";
session_start();

$id = $_GET['id'];

// Fetch invoice details
$select = $pdo->prepare("SELECT * FROM tbl_invoice WHERE invoice_id = $id");
$select->execute();
$row = $select->fetch(PDO::FETCH_OBJ);

// Fetch the role and username from the session
$role = isset($_SESSION["role"]) ? $_SESSION["role"] : "DefaultRole"; // Replace "DefaultRole" with the default role if needed
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "DefaultUsername"; // Replace "DefaultUsername" with the default username if needed

$pdf = new FPDF('P', 'mm', array(100, 145));
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(83, 8, 'JETAPS POINT OF SALES ', 1, 1, 'C');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(83, 5, 'Iligan', 0, 1, 'C');
$pdf->Cell(83, 5, 'Contact : 09066854707', 0, 1, 'C');
$pdf->Cell(83, 5, 'E-mail Address : jetapsemail@gmail.com', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(83, 5, 'Username: ' . $username . ' - Role: ' . $role, 0, 1, 'C');




$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(30, 4, 'Bill To: ', 0, 0, '');
$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(10, 4, $row->customer_name, 0, 1, '');

$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(20, 4, 'OR NUMBER: ', 0, 0, '');
$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(40, 4, $row->invoice_id, 0, 1, '');

$pdf->SetFont('Arial', 'BI', 8);
// Format the date
$dateFormat = date('Y-m-d', strtotime($row->order_date));
$pdf->Cell(8, 4, 'Date : ', 0, 0, '');
$pdf->Cell(20, 4, $dateFormat, 0, 0, '');

// Format the time
$timeFormat = date('H:i:s', strtotime($row->order_time));
$pdf->Cell(10, 4, 'Time : ', 0, 0, '');
$pdf->Cell(20, 4, $timeFormat, 0, 1, '');


$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->SetFillColor(208, 208, 208);
$pdf->Cell(50, 5, 'PRODUCT', 1, 0, 'C');
$pdf->Cell(11, 5, 'QTY', 1, 0, 'C');
$pdf->Cell(13, 5, 'PRICE', 1, 0, 'C');
$pdf->Cell(15, 5, 'SUB TOTAL', 1, 1, 'C');

$select = $pdo->prepare("SELECT * FROM tbl_invoice_details WHERE invoice_id = $id");
$select->execute();

while ($item = $select->fetch(PDO::FETCH_OBJ)) {
    $pdf->SetX(7);
    $pdf->SetFont('Helvetica', 'B', 8);
    $pdf->Cell(50, 5, $item->product_name, 1, 0, 'L');
    $pdf->Cell(11, 5, $item->qty, 1, 0, 'C');
    $pdf->Cell(13, 5, number_format($item->price, 2), 1, 0, 'C');
    $pdf->Cell(15, 5, number_format($item->price * $item->qty, 2), 1, 1, 'C');
}

$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');
$pdf->Cell(25, 5, 'SUBTOTAL', 1, 0, 'C');
$pdf->Cell(20, 5, number_format($row->subtotal, 2), 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');
$pdf->Cell(25, 5, 'DISCOUNT', 1, 0, 'C');
$pdf->Cell(20, 5, number_format($row->discount, 2), 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 10);
$pdf->Cell(20, 5, '', 0, 0, 'L');
$pdf->Cell(25, 5, 'GRANDTOTAL', 1, 0, 'C');
$pdf->Cell(20, 5, number_format($row->total, 2), 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');
$pdf->Cell(25, 5, 'PAYMENT TYPE', 1, 0, 'C');
$pdf->Cell(20, 5, $row->payment_type, 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');
$pdf->Cell(25, 5, 'WARRANTY', 1, 0, 'C');
$pdf->Cell(20, 5, $row->warranty, 1, 1, 'C');

$pdf->Cell(20, 5, '', 0, 1, '');


$pdf->SetX(3);
$pdf->SetFont('Courier', 'B', 12);
$pdf->Cell(90, 5, 'Thank you for choosing', 0, 1, 'C');

$pdf->SetX(3);
$pdf->SetFont('Courier', 'B', 12);
$pdf->Cell(90, 5, 'JETAPS | POS', 0, 1, 'C');

$pdf->SetX(3);
$pdf->SetFont('Courier', 'B', 12);
$pdf->Cell(90, 5, "Please Come Again", 0, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('Courier', 'BI', 8);
$pdf->SetX(7);
$pdf->Cell(90, 7, '----------------------------------------------------', 0, 1, '');

$pdf->Output();
?>
