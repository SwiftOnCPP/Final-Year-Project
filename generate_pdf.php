<?php
session_start();
include "connection.php"; // Ensure correct path and content of your connection script

// Ensure order_id is provided and valid
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$orderID = $_GET['order_id'];

// Fetch order details from the database
$sql = "SELECT * FROM admin_panel WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $orderMenu = $row['order_menu'];
    $tableNo = $row['table_no'];
    $totalPrice = $row['total_price'];
    $orderType = $row['order_type'];

    // Generate PDF and force download
    generatePDF($orderID, $orderMenu, $tableNo, $totalPrice, $orderType);
} else {
    // If order not found, handle it accordingly
    header("Location: index.php");
    exit();
}

$stmt->close();

// Function to generate PDF and force download
function generatePDF($orderID, $orderMenu, $tableNo, $totalPrice, $orderType) {
    require_once('TCPDF-main/tcpdf.php'); // Include TCPDF library

    // Create new PDF document with custom page size (80mm x 297mm for POS receipt)
    $pdf = new TCPDF('P', 'mm', array(80, 297), true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('WESTERN.HOUSE');
    $pdf->SetTitle('Order Receipt');
    $pdf->SetSubject('Order Receipt');
    $pdf->SetKeywords('Order, Receipt, Restaurant');

    // Add a page
    $pdf->AddPage();

    // Set logo and header
    $logo = 'WesternHouse.JPG';  // Path to your logo image
    $pdf->Image($logo, 30, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false); // Smaller logo

    // Line break
    $pdf->Ln(20);

    // Title and date
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'WESTERN HOUSE', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 10);

    // Get current date and time
    $currentDate = date('Y-m-d');
    $currentTime = date('h:i:s A'); // 12-hour format with AM/PM

    // Order receipt date and time
    $pdf->Cell(0, 10, 'Order Receipt', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Date: ' . $currentDate, 0, 1, 'C');
    $pdf->Cell(0, 10, 'Time: ' . $currentTime, 0, 1, 'C');

    // Line separator
    $pdf->Cell(0, 10, '----------------------------------------', 0, 1, 'C');

    // Line break
    $pdf->Ln(2);

    // Order details
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 10, "Order ID: #$orderID", 0, 1, 'C');
    $pdf->Cell(0, 10, "Table Number: $tableNo", 0, 1, 'C');
    $pdf->Cell(0, 10, "Order Type: $orderType", 0, 1, 'C');

    // Line break
    $pdf->Ln(5);

    // Order items
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Order Items', 0, 1, 'C'); // Center-aligned order items header
    $pdf->SetFont('helvetica', '', 10);
    // Display order menu items
    $pdf->MultiCell(0, 10, $orderMenu, 0, 'C'); // Center-aligned order menu items

    // Line break
    $pdf->Ln(5);

    // Total price
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Total Price: RM ' . number_format($totalPrice, 2), 0, 1, 'C'); // Center-aligned total price

    // Line break
    $pdf->Ln(5);

    // Footer message
    $pdf->SetFont('helvetica', 'I', 10);
    $pdf->Cell(0, 10, 'Thank you for dining with us!', 0, 1, 'C');
    $pdf->Cell(0, 10, '----------------------------------------', 0, 1, 'C');

    // Additional footer information
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(0, 10, "WESTERN.HOUSE\nNO 9G, TAMAN AKASIA, Taman Seri Akasia, 09000 Kulim, Kedah, Malaysia\nPhone: (016) 606-6090\nWebsite: www.wafiyhensem.com", 0, 'C');

    // Output PDF to the browser with forced download
    $pdfFileName = "WWESTERN.HOUSE_OrderReceipt_#{$orderID}.pdf";
    $pdf->Output($pdfFileName, 'D');

    // Clean up
    $pdf->close();
}
?>
