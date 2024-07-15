<?php
// Include the file for database connection
include "connection.php";

// Start the session
session_start(); 

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Query to get the staff_name of the logged-in user
    $sql = "SELECT staff_name FROM staff_details WHERE username='$username'";
    $result = $conn->query($sql);

    // Check if the query returned any rows
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['name'] = $row['staff_name']; // Store the staff_name in the session variable
    }
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

// Check if the complete button is clicked
if (isset($_POST['complete_order'])) {
    $orderId = $_POST['order_id'];
    // Update the order status to 'completed'
    $completeOrderQuery = "UPDATE order_details SET order_status='Completed' WHERE order_id='$orderId'";
    $conn->query($completeOrderQuery);
}

// Check if the delete button is clicked
if (isset($_POST['delete_order'])) {
    $orderId = $_POST['order_id'];
    // Delete the order from the database
    $deleteOrderQuery = "DELETE FROM order_details WHERE order_id='$orderId'";
    $conn->query($deleteOrderQuery);
}

// Query to fetch all orders that are not completed, ordered by order_id descending
$orderQuery = "
    SELECT od.order_id, od.order_item, od.total_price, od.table_number, od.order_status, od.progress
    FROM order_details od
    JOIN admin_panel ap ON od.order_id = ap.id
    WHERE od.order_status != 'Completed'
    AND ap.action = 'Approved'
    ORDER BY od.order_id DESC;
";

$orderResult = $conn->query($orderQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <title>Chef Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

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

        h1 {
            text-align: center;
            color: #333333;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
        }

        button {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .complete-button {
            background-color: #4CAF50;
            color: white;
        }

        .delete-button {
            background-color: #f44336;
            color: white;
        }

        /* Set background image and size for the entire body */
        body {
            background-image: url('adminpage.jpg'); 
            background-size: cover;
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
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
        }

        .dropdown-content a {
            color: black;
            padding: 20px 16px;
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
            color: white;
            background-color: #007bff; /* Match dashboard button background */
        }

        .dropdown:hover .dropbtn {
            background-color: grey;
        }

        @media screen and (max-width: 768px) {
        .container {
            width: calc(100% - 20px); /* Adjust width to match the table and buttons */
            padding: 10px;
            margin: 0 auto; /* Center the container horizontally */
            overflow-x: auto; /* Enable horizontal scrolling if necessary */
        }

        table {
            width: 100%;
            margin-top: 10px; /* Add space between table and container */
        }

        table th, table td {
            font-size: 14px;
            padding: 6px;
        }

        .button-container {
            margin-top: 20px;
            text-align: center;
            white-space: nowrap; /* Prevent buttons from wrapping */
        }

        .button-container a,
        .button-container form input[type="submit"] {
            font-size: 14px;
            padding: 8px 16px;
            margin: 5px;
        }
    }


    </style>
    <script>
        $(document).ready(function() {
            // Refresh the page every 60 seconds
            setInterval(function(){
                window.location.reload();
            }, 60000);

            // Adjust the table layout for smaller screens
            function adjustTable() {
                if ($(window).width() < 768) {
                    $("table").addClass("responsive");
                } else {
                    $("table").removeClass("responsive");
                }
            }

            adjustTable();

            $(window).resize(function() {
                adjustTable();
            });
        });
    </script>
</head>
<body>
    <!-- Check if the user is logged in and display their name -->
    <?php if (isset($_SESSION['name'])) : ?>
        <h1><center>Welcome, <?php echo $_SESSION['name']; ?>!</center></h1>
    <?php endif; ?>

    <div class="container">
        <h1>Order Details</h1>
        
                <!-- Dropdown for Chef, Account, and Log Out -->
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
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Details</th>
                    <th>Total Price</th>
                    <th>Table No.</th>
                    <th>Status</th>
                    <th>Progress</th> <!-- New column for progress -->
                    <th colspan="2">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($orderResult->num_rows > 0): ?>
                    <?php while ($orderRow = $orderResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $orderRow['order_id']; ?></td>
                            <td><?php echo $orderRow['order_item']; ?></td>
                            <td><?php echo "RM " . $orderRow['total_price']; ?></td>
                            <td><?php echo $orderRow['table_number']; ?></td>
                            <td><?php echo $orderRow['order_status']; ?></td>
                            <td>
                            <button class="progress-button"
                                data-order-id="<?php echo $orderRow['order_id']; ?>"
                                data-current-state="<?php echo $orderRow['progress']; ?>">
                                <?php echo $orderRow['progress']; ?>
                            </button>
                            </td>
                            <td>
                                <!-- Form for deleting the order -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $orderRow['order_id']; ?>">
                                    <button type="submit" name="delete_order" class="delete-button">Cancel Order</button>
                                </form>
                            </td>    
                            <td>
                                <!-- Form for completing the order -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $orderRow['order_id']; ?>">
                                    <button type="submit" name="complete_order" class="complete-button">Complete Order</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No pending orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<script>
$(document).ready(function() {
    // Set initial button text and style based on current state
    $('.progress-button').each(function() {
        var currentState = $(this).data('current-state');
        switch (currentState) {
            case 'Pending':
                $(this).css('background-color', 'grey');
                break;
            case 'Preparing':
                $(this).css('background-color', 'yellow');
                break;
            case 'Delivered':
                $(this).css('background-color', '#0DC813');
                break;
            default:
                $(this).css('background-color', 'grey');
                break;
        }
    });

    // Handle click events on progress buttons
    $('.progress-button').click(function() {
        var orderId = $(this).data('order-id');
        var currentState = $(this).data('current-state');

        // Determine next state based on current state
        var nextState = '';
        switch (currentState) {
            case 'Pending':
                nextState = 'Preparing';
                break;
            case 'Preparing':
                nextState = 'Delivered';
                break;
            case 'Delivered':
                nextState = 'Delivered'; // Stay on 'Delivered' if already delivered
                break;
            default:
                nextState = 'Pending';
                break;
        }

        // Update button text and style based on nextState
        switch (nextState) {
            case 'Pending':
                $(this).css('background-color', 'grey').text('Pending');
                break;
            case 'Preparing':
                $(this).css('background-color', 'yellow').text('Preparing');
                break;
            case 'Delivered':
                $(this).css('background-color', '#0DC813').text('Delivered');
                break;
            default:
                $(this).css('background-color', 'grey').text('Pending');
                break;
        }

        // Update database with new progress state via AJAX
        $.ajax({
            url: 'update_progress.php',
            method: 'POST',
            data: { order_id: orderId, progress: nextState },
            success: function(response) {
                // Optional: Handle success response if needed
                console.log('Progress updated successfully');
            },
            error: function(xhr, status, error) {
                // Optional: Handle error if AJAX call fails
                console.error('Error updating progress:', error);
            }
        });
    });
});

</script>