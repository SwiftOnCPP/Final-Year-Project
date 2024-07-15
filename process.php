<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table_number = htmlspecialchars($_POST['table_number']);
    // Redirect to the display page with the table number as a query parameter
    header("Location: Checkout.php?table_number=" . urlencode($table_number));
    exit();
}
?>
