<?php
	// Database Connection
	$conn = new mysqli("localhost","root","","admin_management"); 
	
	// Check if the connection was successful
	if($conn -> connect_error)
	{

		// Display an error message if the database connection failed to connect
		die("Connection failed: " . $conn->connect_error);
	}
?>