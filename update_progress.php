<?php
// Include the file for database connection
include "connection.php";

// Check if order_id and progress are set in POST data
if (isset($_POST['order_id'], $_POST['progress'])) {
    $orderId = $_POST['order_id'];
    $nextState = $_POST['progress'];

    // Update the progress in the order_details table
    $updateProgressQuery = "UPDATE order_details SET progress='$nextState' WHERE order_id='$orderId'";
    if ($conn->query($updateProgressQuery) === TRUE) {
        echo "Progress updated successfully";
    } else {
        echo "Error updating progress: " . $conn->error;
    }
} else {
    echo "Invalid data received";
}
?>
