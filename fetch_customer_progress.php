<?php
include "connection.php";

// Fetch customer progress data
$progress_sql = "SELECT * FROM order_details WHERE order_status != 'Completed' ORDER BY order_id DESC";
$progress_result = $conn->query($progress_sql);

$progress_data = [];

if ($progress_result->num_rows > 0) {
    while ($progress_row = $progress_result->fetch_assoc()) {
        $progress_data[] = $progress_row;
    }
}

header('Content-Type: application/json');
echo json_encode($progress_data);
?>
