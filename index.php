<?php
session_start();

include "connection.php";

// Initialize session variables for new order on GET request
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $_SESSION['selected_items'] = [];
    $_SESSION['selected_quantities'] = [];
    $_SESSION['items'] = []; // Initialize items session variable
}

// Fetch menu items and store them in the session
$sql = "SELECT category, name, description, price, image FROM menu_items";
$result = $conn->query($sql);

// Initialize an array to hold menu items by category
$menu_items = [];

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $menu_items[$row["category"]][] = $row;
        // Store item prices in the session
        $_SESSION['items'][$row["name"]] = $row["price"];
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    foreach ($_POST['items'] as $key => $quantity) {
        if (is_numeric($quantity) && $quantity > 0) {
            // Store selected item and quantity
            $_SESSION['selected_items'][] = $key;
            $_SESSION['selected_quantities'][$key] = $quantity;
        }
    }

    // Redirect to order_type.php after processing form
    header("Location: order_type.php");
    exit();
}

// Fetch customer progress data for approved orders
$progress_sql = "
    SELECT od.order_id, od.table_number, od.progress
    FROM order_details od
    JOIN admin_panel ap ON od.order_id = ap.id
    WHERE (od.order_status != 'Completed' AND ap.action = 'Approved')
    ORDER BY od.order_id DESC";

$progress_result = $conn->query($progress_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>WESTERN HOUSE Food Ordering System</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Unna:ital,wght@0,400;0,700;1,400;1,700&display=swap');

    body {
        padding: 0;
        margin: 0;
        width: 100%;
        height: 100%;
        font-family: sans-serif;
    }

    header {
        font-family: 'Unna', serif;
        position: fixed;
        background-color: black;
        color: orange;
        text-align: center;
        padding: 5px 0;
        width: 100%;
        top: 0;
        border-radius: 0 0 15px 15px;
        z-index: 1000; /* Ensure it's above other content */
    }

    header h2 {
        margin: 0;
        margin-left: 45px;
        cursor: pointer;
    }

    .home-button {
        padding: 5px 16px;
        background-color: black;
        margin-left: 45px;
        color: white;
        text-decoration: none;
    }

    .home-button:hover {
        background-color: grey;
    }
    h3{
        text-decoration: underline;
        margin-left: 10px;
    }

    h4 {
        font-family: sans-serif;
        margin-left: 10px;
    }

    section {
        margin-top: 50px;
        margin-bottom: 160px;
    }

    img {
        margin: 0 10px;
        border-radius: 10px;
        width: 120px;
        float: left;
    }

    .adjust {
        margin-left: 140px;
    }

    .drinks {
        margin-left: 10px;
    }

    .decrease, .increase {
        width: 28px;
        height: 28px;
        font-size: 20px;
        font-weight: bold;
        border-radius: 20px;
        border-color: black;
        border: 2px solid;
    }

    table {
        align-items: center;
        background-color: white;
        z-index: 999; /* Ensure it's below fixed header */
    }

    .table-design {
        border-collapse: separate;
        border-spacing: 0;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        z-index: 1000; /* Ensure it's above other content */
    }

    table tr th a {
        font-family: sans-serif;
        display: block;
        text-decoration: none;   
        text-align: center;
        color: black;
        font-size: 20px;
        font-weight: bold;
        padding: 3px 10px;
        border-radius: 5px;
    }

    table tr th a:hover {
        background-color: gray;
    }

    input[type="number"] {
        border-radius: 5px;
        border: 2px solid black;
    }

    input[type="submit"], input[type="reset"] {
        position: fixed;
        font-size: 20px;
        cursor: pointer;
        color: white;
        padding: 7px;
        transition-duration: 0.4s;
        border: none;
        z-index: 1000; /* Ensure it's above other content */
        border-radius: 10px;
    }

    input[type="submit"] {
        background-color: #4CAF50;
        bottom: 100px;
        left: 80%;
        transform: translateX(-80%);
    }

    input[type="reset"] {
        background-color: red;
        bottom: 100px;
        left: 20%;
        transform: translateX(-20%);
    }

    input[type="submit"]:hover {
        background-color: white;
        color: #4CAF50;
        border: 2px solid #4CAF50;
    }

    input[type="reset"]:hover {
        background-color: white;
        color: red;
        border: 2px solid red;
    }

    input[type="number"] {
        width: 24px;
    }

    .progress-table {
        margin-top: 20px;
        width: 100%;
        border-collapse: collapse;
        overflow-y: auto;
    }

    .progress-table th, .progress-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    .progress-table th {
        background-color: #f2f2f2;
    }

    .progress-cell {
        position: relative;
    }

    .progress-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
    }

    /* Define keyframes for different animations */
    @keyframes pulse-pending {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        100% {
            transform: scale(1.1);
            opacity: 0.8;
        }
    }

    @keyframes pulse-preparing {
        0% {
            transform: translateY(0);
            opacity: 1;
        }
        50% {
            transform: translateY(-5px);
            opacity: 0.8;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Apply animations to progress status elements based on status */
    .progress-pending {
        background-color: #3C3D3C;
        color: white;
        animation: pulse-pending 1s infinite alternate;
    }

    .progress-preparing {
        background-color: yellow;
        color: black;
        animation: pulse-preparing 1s infinite ease-in-out;
    }

    .progress-delivered {
        background-color: #0DC813;
        color: white;
    }

    .scroll-down-button {
        position: fixed;
        bottom: 380px;
        right: -70px;
        background-color: orange;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        z-index: 1000; /* Ensure it's above other content */
        transform: rotate(90deg); /* Apply initial rotation */
    }

    .scroll-down-button:hover {
        background-color: #FCD2AD;
    }
        #topcontrol {
        position: fixed;
        bottom: 100px;
        right: 3px;
        background-color: #3D4040;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 13px;
        font-size: 16px;
        cursor: pointer;
        display: none; /* Hide the button initially */
        z-index: 1000; /* Ensure it's above other content */
    }

    #topcontrol:hover {
        background-color: #3FAAFF;
    }
        footer {
        background-color: #333;
        padding: 20px 0;
        color: #fff;
    }

    .card {
        background: none;
        border: none;
    }

    .footer-link {
        color: #fff;
        text-decoration: none;
        margin: 0 10px;
    }

    .footer-link:hover {
        color: #ccc;
    }

    .footer-separator {
        margin: 0 10px;
        color: #fff;
    }

    .card-header {
        border: 0;
        background: none;
    }

    .card-header h6 {
        margin: 0;
        font-size: 1rem;
    }

    .card-header strong {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }

    .card-header i {
        margin-right: 5px;
    }

    .card-header .footer-link {
        display: inline-flex;
        align-items: center;
    }

    @media (max-width: 768px) {
        .card-header strong {
            flex-direction: column;
            gap: 5px;
        }
    }



    </style>
    <script>
        // JavaScript to handle clicking on the h2 element with id "top"
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('top').addEventListener('click', function() {
                window.location.href = 'index.php'; // Navigate to index.php
            });
        });
    </script>
</head>
<body>
    <header>
        <h2 id="top">WESTERN HOUSE 
            <a href="login.php" class="home-button"><i class="fas fa-home"></i></a>
        </h2>
    </header>


    <section>
        <!-- Your form for selecting menu items -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php
            // Define the desired category order
            $category_order = array("Popular Menu", "Nasi Goreng", "Burger", "Side Dish", "Spaghetti", "Drinks");

            // Sort menu items by predefined category order
            foreach ($category_order as $category) {
                if (isset($menu_items[$category])) {
                    echo '<h4 id="' . htmlspecialchars($category) . '"><u>' . htmlspecialchars($category) . '</u></h4>';
                    echo '<div class="category-section">';
                    foreach ($menu_items[$category] as $item) {
                        echo '<div class="menu-item">';
                        if ($item["image"]) {
                            echo '<img src="' . htmlspecialchars($item["image"]) . '" alt="' . htmlspecialchars($item["name"]) . '">';
                        } else {
                            echo '<img src="placeholder.jpg" alt="No Image">';
                        }
                        echo '<div class="adjust">';
                        echo '<p><b>' . htmlspecialchars($item["name"]) . '</b></p>';
                        echo '<p>' . htmlspecialchars($item["description"]) . '</p>';
                        echo '<p>RM' . htmlspecialchars($item["price"]) . '</p>';
                        echo '<p>Quantity: ';
                        echo '<button class="decrease" onclick="updateQuantity(\'' . htmlspecialchars($item["name"]) . '\', -1); return false;">-</button> ';
                        echo '<input type="number" id="' . htmlspecialchars($item["name"]) . '-quantity" class="input" name="items[' . htmlspecialchars($item["name"]) . ']" value="0" readonly>';
                        echo ' <button class="increase" onclick="updateQuantity(\'' . htmlspecialchars($item["name"]) . '\', 1); return false;">+</button>';
                        echo '</p>';
                        echo '</div><br>';
                        echo '</div>';
                    }
                    echo '</div><br>';
                }
            }
            ?>

            <input type="reset" name="reset" value="Reset Order">
            <input type="submit" name="submit" value="Confirm Order">
        </form>
    </section>

    <section id="customer-progress">
        <!-- Customer Progress Table -->
        <h3>Customer Progress</h3>
        <table class="progress-table">
        <thead>
        <tr>
            <th>Order ID</th>
            <th>Table Number</th>
            <th>Progress</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Check if there are rows returned
        if ($progress_result->num_rows > 0) {
            // Loop through each row in the result set
            while ($progress_row = $progress_result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>#' . htmlspecialchars($progress_row['order_id']) . '</td>';
                echo '<td>';
                $table_number = htmlspecialchars($progress_row['table_number']);
                echo $table_number !== 'N/A' ? 'Table ' . $table_number : $table_number;
                echo '</td>';
                echo '<td class="progress-cell">';
                
                // Determine which CSS class to apply based on progress status
                $status = htmlspecialchars($progress_row['progress']);
                $statusClass = '';

                switch ($status) {
                    case 'Pending':
                        $statusClass = 'progress-pending';
                        break;
                    case 'Preparing':
                        $statusClass = 'progress-preparing';
                        break;
                    case 'Delivered':
                        $statusClass = 'progress-delivered';
                        break;
                    default:
                        $statusClass = ''; // Handle any other cases or leave empty for default styling
                        break;
                }

                // Output the progress status with the determined class
                echo '<span class="progress-status ' . $statusClass . '">' . $status . '</span>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            // If no rows are returned
            echo '<tr><td colspan="3">No orders found.</td></tr>';
        }
        ?>
    </tbody>
    </table>
    </section>



    <table class="table-design">
        <tr>
            <th><a href="#Spaghetti" style="background-color: lightsalmon;">Spaghetti</a></th> 
            <th><a href="#Burger" style="background-color: lemonchiffon;">Burger</a></th>
            <th><a href="#Drinks" style="background-color: lavender;">Drinks</a></th>
        </tr>
        <tr>
            <th><a href="#Popular Menu" style="background-color: palegreen;">Popular Menu</a></th>
            <th><a href="#Nasi Goreng" style="background-color: lightcyan;">Nasi Goreng</a></th>
            <th><a href="#Side Dish" style="background-color: lightpink;">Side Dish</a></th>  
        </tr>      
    </table>

    <!-- Scroll to Bottom button -->
    <button id="scroll-down-button" class="scroll-down-button" onclick="scrollToCustomerProgress()">
        Order Progress <i class="fas fa-chevron-down"></i>
    </button>


        <!-- Scroll to Top button -->
        <div id="topcontrol" title="Scroll To Top"><i class="fas fa-chevron-up"></i></div>

        <footer>
    <div id="pages" style="width:90%; margin: 0 auto;">
        <div class="card" style="width:100%; margin-bottom: 120px;">
            <div class="card-header text-center" style="border:0px; background: none;">
                <h6 class="mb-0">
                    <strong>
                        <a href="#" class="footer-link">
                            <i class="fa fa-info-circle"></i> About Us
                        </a>
                        <span class="footer-separator">|</span>
                        <a href="#" class="footer-link">
                            <i class="fa fa-bullhorn"></i> Privacy & Policy
                        </a>
                        <span class="footer-separator">|</span>
                        <a href="#" class="footer-link">
                            <i class="fa fa-book"></i> Terms & Conditions
                        </a>
                        <span class="footer-separator">|</span>
                        <a href="#" class="footer-link">
                            <i class="fa fa-copyright"></i> Copyright Policy
                        </a>
                        <span class="footer-separator">|</span>
                        <a href="#" class="footer-link">
                            <i class="fa fa-envelope"></i> DMCA & Contact Us
                        </a>
                        <span class="footer-separator">|</span>
                        <a href="#" target="_blank" class="footer-link">
                            <i class="fab fa-telegram-plane"></i> Join Telegram
                        </a>
                    </strong>
                </h6>
                <p class="footer-copyright">Copyright © Wafiy © Najmi © Azhan</p>
            </div>
        </div>
    </div>
</footer>
    <script src="script.js"></script>


</body>
</html>
