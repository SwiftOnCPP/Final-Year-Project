<?php
// Include the file for database connection
include "connection.php";

// Start the session
session_start();
// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['job'] == 'Chef') {
    // Redirect to the login page or another page if not logged in as chef
    header("Location: chef.php");
    exit();
}
// Function to show JavaScript alerts using SweetAlert2
function showAlert($message, $type) {
    echo "<script>
            Swal.fire({
                icon: '{$type}',
                title: '{$message}',
                showConfirmButton: false,
                timer: 1500
            });
         </script>";
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
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

// Get the current month and year
$currentMonth = date('m');
$currentYear = date('Y');

// SQL query to get total orders and total sales for the current month with status 'Completed'
$sql = "SELECT COUNT(id) as total_orders, SUM(total_price) as total_sales
        FROM admin_panel
        WHERE status = 'Completed'
        AND MONTH(operation) = ?
        AND YEAR(operation) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $currentMonth, $currentYear);
$stmt->execute();
$stmt->bind_result($totalOrders, $totalSales);
$stmt->fetch();
$stmt->close();

// SQL query to get the most ordered menu items for the current month with status 'Completed'
$sqlMenuItems = "SELECT order_menu
                 FROM admin_panel
                 WHERE status = 'Completed'
                 AND MONTH(operation) = ?
                 AND YEAR(operation) = ?";

$stmtMenuItems = $conn->prepare($sqlMenuItems);
$stmtMenuItems->bind_param("ii", $currentMonth, $currentYear);
$stmtMenuItems->execute();
$resultMenuItems = $stmtMenuItems->get_result();

$menuItemsCount = [];

// Process the order_menu column to count each menu item
while ($row = $resultMenuItems->fetch_assoc()) {
    $orderMenu = $row['order_menu'];
    $items = explode(', ', $orderMenu);
    foreach ($items as $item) {
        $itemName = trim(explode(' x ', $item)[0]);
        if (!isset($menuItemsCount[$itemName])) {
            $menuItemsCount[$itemName] = 0;
        }
        $menuItemsCount[$itemName]++;
    }
}

$stmtMenuItems->close();

// Sort menu items by count in descending order
arsort($menuItemsCount);

// SQL query to get daily sales for the current month
$sqlDailySales = "SELECT DAY(operation) as day, SUM(total_price) as sales
                  FROM admin_panel
                  WHERE status = 'Completed'
                  AND MONTH(operation) = ?
                  AND YEAR(operation) = ?
                  GROUP BY DAY(operation)";

$stmtDailySales = $conn->prepare($sqlDailySales);
$stmtDailySales->bind_param("ii", $currentMonth, $currentYear);
$stmtDailySales->execute();
$resultDailySales = $stmtDailySales->get_result();

$dailySalesData = [];
while ($row = $resultDailySales->fetch_assoc()) {
    $dailySalesData[$row['day']] = $row['sales'];
}

$stmtDailySales->close();

// Generate the labels and data for the chart
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
$chartLabels = [];
$chartData = [];

for ($i = 1; $i <= $daysInMonth; $i++) {
    $chartLabels[] = $i;
    $chartData[] = isset($dailySalesData[$i]) ? $dailySalesData[$i] : 0;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Style for the body */
/* Style for the body */
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 20px;
    display: flex;
    justify-content: center;
    min-height: 100vh;
    color: #333;
}

/* Style for the container */
.container {
    width: 100%;
    max-width: 1200px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin: 20px 0;
    padding: 20px;
}

/* Style for the header */
.header {
    background-color: black;
    color: #fff;
    text-align: center;
    padding: 20px;
    border-radius: 8px 8px 0 0;
}

.header h1 {
    margin: 0;
    font-size: 28px;
    color: orange;
}

.header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: normal;
}

/* Dropdown styles */
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
    background-color: #007bff;
    color: white;
}

.dropdown:hover .dropbtn {
    background-color: grey;
}

/* Style for the analytics section */
.analytics {
    padding: 20px;
}

.analytics h3 {
    margin-bottom: 20px;
    color: #007bff;
}

/* Style for tables */
table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    margin-bottom: 20px;
}

th, td {
    border: 1px solid #e1e1e1;
    padding: 12px;
    text-align: center;
    font-size: 14px;
}

th {
    background-color: #007bff;
    color: #fff;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

/* Style for input fields and select dropdowns */
input[type="text"], input[type="password"], select {
    width: 100%;
    padding: 8px;
    margin: 4px 0;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

input[type="text"]:focus, select:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    outline: none;
}

/* Style for buttons */
button,
.action-buttons a,
.button-container a,
.button-container form input[type="submit"] {
    display: inline-block;
    margin: 0 5px;
    padding: 10px 20px;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.action-buttons a {
    padding: 8px 16px;
    margin: 0;
}

button:hover,
.action-buttons a:hover,
.button-container a:hover,
.button-container form input[type="submit"]:hover {
    background-color: #0056b3;
}

.approve-button {
    background-color: #28a745;
}

.reject-button {
    background-color: #dc3545;
}

.approve-button:hover {
    background-color: #218838;
}

.reject-button:hover {
    background-color: #c82333;
}

/* Style for the canvas element */
canvas {
    max-width: 100%;
    height: auto;
}

/* Style for cards */
.cards-container {
    display: flex;
    gap: 20px; /* Adjust the gap between cards as needed */
}

.card {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: flex-start;
    margin-top: 15px;
    border-radius: 8px;
    padding: 20px;
    background-color: white;
    margin-left: 35px;
    width: 300px;
    height: 100px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.card img {
    width: 90px;
    height: 60px;
    margin-right: 15px;
}

/* Style for logout link */
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

/* Responsive design for mobile */
@media (max-width: 768px) {
    body {
        padding: 10px;
    }

    .container {
        padding: 10px;
        overflow-x: auto;
    }

    .header h1 {
        font-size: 22px;
    }

    .header h2 {
        font-size: 18px;
    }

    table {
        display: block;
        overflow-x: auto;
    }

    th, td {
        padding: 8px;
    }

    .dropdown-content a,
    .dropdown-content form input[type="submit"] {
        padding: 10px 14px;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 5px;
    }
}



    </style>
</head>
<body>

    <div class="container">
    

    <!-- Dropdown for navigation -->
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

    <div class="cards-container">
        <div class="card">
            <img src="images/order.png" alt="Image">
            <h3>Total Orders (Current Month): <?php echo $totalOrders; ?></h3>
        </div>
        <div class="card">
            <img src="images/money.png" alt="Image">
            <h3>Total Sales (Current Month): RM <?php echo number_format($totalSales, 2); ?></h3>
        </div>
        <div class="card">
            
            <h3>Coming Soon !</h3>
        </div>
    </div>

    <h3>Most Ordered Menu Items (Current Month):</h3>
    <table>
        <tr>
            <th>Menu Item</th>
            <th>Count</th>
        </tr>
        <?php foreach ($menuItemsCount as $itemName => $count) : ?>
        <tr>
            <td><?php echo htmlspecialchars($itemName); ?></td>
            <td><?php echo $count; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Sales Analytics (Day by Day for Current Month):</h3>
    <canvas id="salesChart"></canvas>
</div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'Daily Sales (RM)',
                    data: <?php echo json_encode($chartData); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
