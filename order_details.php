<!DOCTYPE html>
<html>
<head>
    <title>User Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .logout-button {
            width: fit-content;
            padding: 10px;
            margin: 20px auto;
        }

        .logout-button button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #dc3545; /* Adjusted the color to red */
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .logout-button button[type="submit"]:hover {
            background-color: #c82333; /* Darker shade of red on hover */
        }
        .back-button {
            margin-top: 20px;
            text-align: center;
        }

        .back-button button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .back-button button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php
include "connection.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if the logout button is clicked
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Fetch user information based on username
$username = $_SESSION['username'];
$sql = "SELECT * FROM user_activity WHERE username <> '$username'
 ORDER BY CASE WHEN role = 'Admin' THEN 1 ELSE 2 END, role ASC"; // Exclude the current user and order by role

// Check the role of the user
if ($_SESSION['role'] === 'Admin') {
    // Display user greeting
    echo "<h2>Hello, {$_SESSION['username']}!<span style='color:red;'> [{$_SESSION['role']}] (use session)</span></h2>";

    // Fetch all users excluding the current user
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Display user information in a table
        echo "<table>";
        echo "<tr><th>Name</th><th>Email</th><th>Username</th><th>Password</th><th>Role</th><th>Status</th><th>Action</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['username']}</td>";
            echo "<td>{$row['password']}</td>";
            echo "<td>{$row['role']}</td>";
            echo "<td>{$row['status']}</td>";

            echo "<td>";
            echo "<a href='edit.php?id={$row['id']}'>Edit</a><br><br>"; // Link to edit.php 
            echo "<a href='delete.php?id={$row['id']}' onclick='return confirm
            (\"Are you sure you want to delete this user?\")' style='color: red;'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Display Back button
        echo "<div class='back-button'><a href='admin.php'><button>Back</button></a></div>";

        // Display Logout button
        echo "<form method='post' action=''><div class='logout-button'><button type='submit' name='logout'>Logout</button></div></form>";
    } else {
        echo "No records found";
    }
} elseif ($_SESSION['role'] === 'Lecturer') {
    // Display user greeting
    echo "<h2>Hello, {$_SESSION['username']}!<span style='color:red;'> [{$_SESSION['role']}] (use session)</span></h2>";

    // Fetch all students excluding the current user
    $sql = "SELECT * FROM user_activity WHERE role='Student'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Display user information in a table
        echo "<table>";
        echo "<tr><th>Name</th><th>Email</th><th>Username</th><th>Password</th><th>Role</th><th>Status</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['username']}</td>";
            echo "<td>{$row['password']}</td>";
            echo "<td>{$row['role']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        // Display Back button
        echo "<div class='back-button'><a href='lecturer.php'><button>Back</button></a></div>";

        // Display Logout button
        echo "<form method='post' action=''><div class='logout-button'><button type='submit' name='logout'>Logout</button></div></form>";
    } else {
        echo "No other students found.";
    }
} elseif ($_SESSION['role'] === 'Student') {
    // Display user greeting
    echo "<h2>Hello, {$_SESSION['username']}!<span style='color:red;'> [{$_SESSION['role']}] (use session)</span></h2>";

    // Fetch all other students excluding the current user
    $sql = "SELECT * FROM user_activity WHERE role='Lecturer'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Display user information in a table
        echo "<table>";
        echo "<tr><th>Name</th><th>Email</th><th>Role</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['role']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        // Display Back button
        echo "<div class='back-button'><a href='student.php'><button>Back</button></a></div>";

        // Display Logout button
        echo "<form method='post' action=''><div class='logout-button'><button type='submit' name='logout'>Logout</button></div></form>";
    } else {
        echo "No other students found.";
    }
}
?>

</body>
</html>
