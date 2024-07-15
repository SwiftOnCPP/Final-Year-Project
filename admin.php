<?php
// Include the file for database connection
include "connection.php";

// Start the session
session_start();

// Function to show JavaScript alerts using SweetAlert2
function showAlert($message, $type = 'success') {
    echo "<script>
            Swal.fire({
                icon: '{$type}',
                title: '{$message}',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload(); // Reload the page after 1.5 seconds
            });
          </script>";
}

if (!isset($_SESSION['username']) || $_SESSION['job'] == 'Admin') {
    // Redirect to the login page or another page if not logged in as admin
    header("Location: chef.php");
    exit();
}

// Check if the logout button is clicked
if (isset($_POST['logout'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page after logout
    header("Location: login.php");
    exit();
}

// Check if remove button is clicked
if (isset($_GET['remove_id'])) {
    $id = $_GET['remove_id'];

    // SQL to delete admin control entry based on ID
    $sql = "DELETE FROM admin_panel WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect to refresh the page after removal
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error removing entry: " . $stmt->error;
    }
}

// Check if the approve or reject action is triggered via AJAX
if (isset($_POST['action'], $_POST['id'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];

    try {
        if ($action === 'approve') {
            // Update SQL query to approve
            $sql = "UPDATE admin_panel SET action='Approved' WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Entry approved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update entry']);
            }
        } elseif ($action === 'reject') {
            // SQL to delete admin control entry based on ID
            $sql = "DELETE FROM admin_panel WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Entry deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete entry']);
            }
        }
    } catch (Exception $e) {
        // Output JSON response for exception
        echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    }
    exit(); // Exit after AJAX processing
}

// Check if the complete button is clicked
if (isset($_POST['complete_order'])) {
    $orderId = $_POST['order_id'];
    // Update the order status to 'completed' in the database
    $completeOrderQuery = "UPDATE admin_panel SET status='Completed' WHERE id=?";
    $stmt = $conn->prepare($completeOrderQuery);
    $stmt->bind_param("i", $orderId);
    if ($stmt->execute()) {
        // Success message
        showAlert('Order completed successfully');
    } else {
        // Error message
        showAlert('Failed to complete order', 'error');
    }
}

// Check if the delete button is clicked
if (isset($_POST['delete_order'])) {
    $orderId = $_POST['order_id'];

    // Delete from admin_panel table
    $deleteOrderQuery = "DELETE FROM admin_panel WHERE id=?";
    $stmt = $conn->prepare($deleteOrderQuery);
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        // Success message
        showAlert('Order cancelled successfully');
        
        // Delete from order_details table
        $deleteOrderDetailsQuery = "DELETE FROM order_details WHERE order_id=?";
        $stmt_details = $conn->prepare($deleteOrderDetailsQuery);
        $stmt_details->bind_param("i", $orderId);
        $stmt_details->execute(); // Execute without checking the result (optional)

    } else {
        // Error message
        showAlert('Failed to cancel order', 'error');
    }
}


// Select orders from admin_panel table that are pending or completed
$orderQuery = "
   SELECT ap.id, ap.order_menu, ap.table_no, ap.total_price, ap.order_type, ap.action, ap.status, ap.operation
   FROM admin_panel ap
   WHERE ap.action = 'Pending' OR ap.status = 'Completed' OR ap.action = 'Approved'
   ORDER BY ap.id DESC
";



$orderResult = $conn->query($orderQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page - Admin Control</title>
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
/* Style for the body */
body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    flex-direction: column;
}

/* Style for the container */
.container {
    width: 90%;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    max-height: 10000%; /* Use a large percentage */
    padding: 20px;
    overflow-y: auto; /* Allow vertical scrolling */
}

/* Style for the header */
.header {
    background-color: #000000;
    color: #ff6600;
    text-align: center;
    padding: 20px;
}

.header h1 {
    margin: 0;
    font-size: 24px;
}

/* Style for tables */
table {
    width: 100%;
    border-collapse: collapse;
    background-color: #ffffff;
}

th, td {
    border: 1px solid #dddddd;
    padding: 10px;
    text-align: center;
}

th {
    background-color: #007bff;
    color: #ffffff;
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

button {
    padding: 5px 10px;
    margin-right: 5px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

/* Style for action buttons */
.action-buttons {
    text-align: center;
}

.action-buttons button,
.action-buttons input[type="submit"] {
    display: inline-block;
    padding: 10px 20px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
    color: white;
    text-align: center;
}


.action-buttons .approve-button {
    background-color: #28a745; /* Green for approval */
}

.action-buttons .reject-button {
    background-color: #dc3545; /* Red for rejection */
}

.action-buttons button:hover,
.action-buttons input[type="submit"]:hover {
    opacity: 0.9;
}

/* Additional styles for other buttons */

.complete-button {
    background-color: #4CAF50; /* Green for completion */
}

.delete-button {
    background-color: #f44336; /* Red for deletion */
}

.complete-button:hover,
.delete-button:hover {
    opacity: 0.9;
}

.button-container {
    text-align: center;
    margin-top: 20px;
    margin-bottom: 20px;
}

.button-container a,
.button-container form input[type="submit"] {
    display: inline-block;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
    border: none;
    cursor: pointer;
    color: #fff;
    font-size: 16px;
    margin: 0 10px; /* Add margin to separate the buttons */
}

.logout-link {
    display: block;
    font-size: 18px;
    color: white;
    background-color: #333;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    text-align: center;
    margin: 20px auto; /* Center the link */
    width: fit-content;
}

.logout-link:hover {
    background-color: grey;
}

/* Dropdown styles updated to match the dashboard */
.dropdown {
    position: relative;
    display: inline-block;
    margin-right: 10px;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 100; /* Ensure a high z-index */
    border-radius: 5px;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
    transition: background-color 0.3s;
}

.dropdown-content a:hover {
    background-color: #ddd;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.dropbtn {
    padding: 10px 20px;
    font-size: 16px;
    margin-right: 5px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    background-color: #007bff; /* Match dashboard button background */
    color: white;
}

.dropdown:hover .dropbtn {
    background-color: grey;
}

.complete-button,
.delete-button {
    padding: 8px 5px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
    color: white;
    text-align: center;
}

.complete-button {
    background-color: #4CAF50; /* Green */
}

.delete-button {
    background-color: #f44336; /* Red */
}

.complete-button:hover,
.delete-button:hover {
    opacity: 0.9;
}

/* Responsive design for mobile */
@media (max-width: 768px) {
    .container {
        width: 100%;
        margin: 10px;
    }

    table {
        width: 100%;
        max-width: 100%;
        display: block;
        overflow-x: auto;
    }

    th, td {
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #007bff;
        color: #ffffff;
        font-weight: bold;
        white-space: nowrap;
    }

    td {
        border: 1px solid #dddddd;
    }

    .complete-button,
    .delete-button {
        padding: 8px 5px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
        color: white;
        text-align: center;
    }

    .complete-button {
        background-color: #4CAF50; /* Green */
    }

    .delete-button {
        background-color: #f44336; /* Red */
    }

    .complete-button:hover,
    .delete-button:hover {
        opacity: 0.9;
    }
}
    </style>
</head>
<body>
<div class="container">
        <div class="header">
            <?php if (isset($_SESSION['username'])) : ?>
                <h1>Hello <?php echo $_SESSION['username']; ?>!</h1>
            <?php endif; ?>
            <h2>(ADMIN PAGE - ORDER)</h2>
        </div>

        <!-- Dropdown for Menu, Account, and Log Out -->
        <div class="dropdown">
            <button class="dropbtn">Menu <i class="fas fa-caret-down"></i></button>
            <div class="dropdown-content">
                <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="admin.php"><i class="fa-solid fa-user"></i> Admin</a>
                <a href="chef.php"><i class="fas fa-user-friends"></i> Chef</a>
                <a href="admin_account.php"><i class="fas fa-cogs"></i> Account</a>
                <a href="menu_management.php"><i class="fas fa-utensils"></i> Menu Management</a>
                <form method="POST">
                    <input type="submit" class="logout-link" name="logout" value="Log Out">
                </form>
            </div>
        </div>
        
        <table>
            <tr>
                <th>Order ID</th>
                <th>Order</th>
                <th>Table No.</th>
                <th>Total Price</th>
                <th>Type of Order</th>
                <th>Action</th>
                <th>Status</th>
                <th>Timestamp</th>
            </tr>
            <?php
            // Displaying orders from database
            if ($orderResult->num_rows > 0) {
                while($row = $orderResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["order_menu"] . "</td>";
                    echo "<td>" . $row["table_no"] . "</td>";
                    echo "<td>RM " . $row["total_price"] . "</td>";
                    echo "<td>" . $row["order_type"] . "</td>";
                    echo "<td class='action-buttons'>";
                    if ($row["action"] == "Pending") {
                        echo "<button class='approve-button' onclick=\"approveRejectEntry('approve', " . $row["id"] . ");\"><i class='fas fa-check'></i> Approve</button>";
                    } elseif ($row["action"] == "Approved") {
                        echo "<span class='approve-button approved'><i class='fas fa-check'></i> Approved</span>";
                    } elseif ($row["action"] == "Rejected") {
                        echo "<span class='reject-button'><i class='fas fa-times'></i> Rejected</span>";
                    }
                    echo "</td>";
                    echo "<td class='action-buttons'>";
                    if ($row['status'] !== 'Completed') {
                        echo "<form method='post' action='" . ($_SERVER["PHP_SELF"]) . "' style='display: inline;'>";
                        echo "<input type='hidden' name='order_id' value='" . $row['id'] . "'>";
                        echo "<input type='submit' name='complete_order' value='Complete Order' class='complete-button'>";
                        echo "</form>";
                        echo "<form method='post' action='" . ($_SERVER["PHP_SELF"]) . "' style='display: inline; margin-left: 5px;'>"; // Add margin-left for gap
                        echo "<input type='hidden' name='order_id' value='" . $row['id'] . "'>";
                        echo "<input type='submit' name='delete_order' value='Cancel Order' class='delete-button'>";
                        echo "</form>";
                    } else {
                        echo "<span class='order-completed'>Completed</span>";
                    }
                    echo "</td>";
                    echo "<td>" . $row["operation"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No orders found.</td></tr>";
            }
            ?>
        </table>
    </div>

    <script>
        // Function to approve or reject an entry via AJAX
        function approveRejectEntry(action, id) {
            $.ajax({
                url: 'admin.php',
                method: 'POST',
                dataType: 'json',
                data: { action: action, id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload(); // Reload the page after 1.5 seconds
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' - ' + error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong! Please try again later.'
                    });
                }
            });
        }
                    // Refresh the page every 60 seconds
                    setInterval(function(){
                window.location.reload();
            }, 60000);
    </script>
</body>
</html>