<?php
require_once 'db_con.php';

if (!isset($_GET['payment_transaction_id'])) {
    die("Error: No payment transaction ID provided");
}

$payment_transaction_id = $_GET['payment_transaction_id'];

// Get transaction details including acknowledgement receipt number
$query = "SELECT acknowledgement_receipt_number FROM payment_transactions WHERE payment_transaction_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $payment_transaction_id);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

if (!$transaction) {
    die("Error: Transaction not found");
}

$receipt_number = $transaction['acknowledgement_receipt_number'];
$pdf_filename = "FiT-LOGSYNC Acknowledgement Receipt ({$receipt_number}).pdf";
$receipt_path = "../acknowledgement-receipt/" . $pdf_filename;


// Check if the receipt exists
if (file_exists($receipt_path)) {
    // Serve the existing PDF file
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $pdf_filename . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    readfile($receipt_path);
} else {
    die("Error: Receipt file not found. Please contact the administrator.");
} 