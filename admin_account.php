<?php
    // Include the file for database connection
    include "connection.php";

    // Start the session
    session_start();
    if (!isset($_SESSION['username']) || $_SESSION['job'] == 'Chef') {
        // Redirect to the login page or another page if not logged in as chef
        header("Location: chef.php");
        exit();
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

    // Check if remove button is clicked
    if (isset($_GET['remove_id'])) {
        $id = $_GET['remove_id'];

        // SQL to delete staff details based on ID
        $sql = "DELETE FROM staff_details WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Redirect to refresh the page after removal
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error removing staff: " . $stmt->error;
        }
    }

    // Check if add staff form is submitted
    if (isset($_POST['add_staff'])) {
        $name = $_POST['staff_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $job = $_POST['job'];

        // SQL query to insert new staff details
        $sql = "INSERT INTO staff_details (staff_name, username, password, job) VALUES ('$name', '$username', '$password', '$job')";

        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("New staff member added successfully"); window.location.href = "admin_account.php";</script>';
        } else {
            echo "Error adding new staff member: " . $conn->error;
        }
    }

    // Check if save button is clicked
    if (isset($_POST['save'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $job = $_POST['job'];

        // SQL query to update staff details
        $sql = "UPDATE staff_details SET staff_name='$name', username='$username', password='$password', job='$job' WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("Staff member updated successfully"); window.location.href = "admin_account.php";</script>';
        } else {
            echo "Error updating staff member: " . $conn->error;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page - Staff Management</title>
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    max-height: 100vh;
    padding: 20px;
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
    word-wrap: break-word; /* Prevents long text from overflowing */
}

th {
    background-color: #007bff;
    color: #ffffff;
    font-weight: bold;
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

input[type="text"]:focus,  
select:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    outline: none;
}

button {
    padding: 5px 10px;
    margin-right: 5px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

/* Style for buttons */
.action-buttons a {
    display: inline-block;
    margin: 0 5px;
    padding: 5px 10px;
    color: #fff;
    border-radius: 3px;
    text-decoration: none;
    font-size: 14px;
}

.approve-button {
    background-color: #28a745;
}

.reject-button {
    background-color: #dc3545;
}

.edit-button {
    background-color: #ffc107;
    color: #fff;
}

.remove-button {
    background-color: #6c757d;
    color: #fff;
}

.action-buttons .fa {
    margin-right: 5px;
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
    margin: 0 10px; 
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

/* Dropdown styles */
.dropdown {
    position: relative;
    display: inline-block;
    margin-right: 10px;
}

.dropbtn {
    padding: 10px 20px;
    font-size: 16px;
    margin-right: 5px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    background-color: #007bff; /* Button color */
    color: white;
}

.dropdown:hover .dropbtn {
    background-color: grey;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 1;
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

/* Add Staff Button */
.add-button {
    background-color: #28a745;
    color: #ffffff;
    border: none;
    padding: 8px 100px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.add-button:hover {
    background-color: #218838;
}

/* Save Button */
.save-button {
    background-color: #007bff;
    color: #ffffff;
    border: none;
    padding: 8px 5px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s, box-shadow 0.3s;
}

.save-button:hover {
    background-color: #0056b3;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

/* Remove Button */
.remove-button {
    background-color: #dc3545;
    color: #ffffff;
    border: none;
    padding: 8px 5px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s, box-shadow 0.3s;
}

.remove-button:hover {
    background-color: #c82333;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

/* Responsive design for mobile */
@media (max-width: 768px) {
    .container {
        width: 100%;
        margin: 10px;
    }

    .header h1 {
        font-size: 18px;
    }

    .logout-button input[type="submit"],
    .admin-link {
        padding: 12px; /* Increase padding to make the button larger */
        font-size: 20px; /* Adjust font size as needed */
    }

    table {
        width: 100%; /* Ensure table spans full width of container */
        max-width: 100%;
        display: block;
        overflow-x: auto; /* Allows horizontal scrolling on small screens */
    }

    th, td {
        padding: 15px; /* Adjust padding for better visibility */
        text-align: center; /* Center-align content for better readability */
    }

    th {
        white-space: nowrap; /* Prevent line breaks in table headers */
    }

    /* Adjust width for table columns */
    th:nth-child(1), td:nth-child(1) { width: 35%; } /* Increased from 25% to 35% */
    th:nth-child(2), td:nth-child(2) { width: 30%; } /* Maintained at 30% */
    th:nth-child(3), td:nth-child(3) { width: 25%; } /* Increased from 20% to 25% */
    th:nth-child(4), td:nth-child(4) { width: 20%; } /* Decreased from 25% to 20% */

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
        <div class="header">
            <?php if (isset($_SESSION['staff_name'])) : ?>
                <h1>Hello <?php echo $_SESSION['staff_name']; ?>!</h1>
            <?php endif; ?>
            <h2>(ADMIN PAGE - ACCOUNT)</h2>
        </div>
        
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
            <tr>
                <th>ID</th>
                <th>Staff Name</th>
                <th>Username</th>
                <th>Password</th>
                <th>Job</th>
                <th>Action</th>
            </tr>
            <tr>
                <form method="POST">
                    <td>#</td>
                    <td><input type="text" name="staff_name" placeholder="Staff Name" required></td>
                    <td><input type="text" name="username" placeholder="Username" required></td>
                    <td><input type="text" name="password" placeholder="Password" required></td>
                    <td>
                        <select name="job" required>
                            <option value="">Select Job</option>
                            <option value="Admin">Admin</option>
                            <option value="Chef">Chef</option>
                        </select>
                    </td>
                    <td><button type="submit" class="add-button" name="add_staff"><i class="fas fa-plus"></i> Add</button></td>
                </form>
            </tr>
            <?php
                $sql = "SELECT * FROM staff_details ORDER BY id DESC";
                
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<form method='POST'>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td><input type='text' name='name' value='" . $row["staff_name"] . "' required></td>";
                        echo "<td><input type='text' name='username' value='" . $row["username"] . "' required></td>";
                        echo "<td><input type='text' name='password' value='" . $row["password"] . "' required></td>";
                        echo "<td>
                                <select name='job' required>
                                    <option value='Admin'" . ($row["job"] == "Admin" ? " selected" : "") . ">Admin</option>
                                    <option value='Chef'" . ($row["job"] == "Chef" ? " selected" : "") . ">Chef</option>
                                </select>
                              </td>";
                        echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                        echo "<td><button type='submit' class='save-button' name='save'><i class='fas fa-save'></i> Save</button>";
                        echo "<a href='?remove_id=" . $row["id"] . "' class='remove-button'><i class='fas fa-trash'></i> Remove</a></td>";
                        echo "</form>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No staff members found.</td></tr>";
                }
                $conn->close();
            ?>

        </table>
        
    </div>

    <!-- Include Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
