<!--
  Rui Santos
  Complete project details at https://RandomNerdTutorials.com
  
  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.
  
  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  Various projects by Rui Santos (above) and others (when used have been mentioned) have been modified 
  and combined by Monty Rhodes to create this file for the design of a platform 
  to integrate IoT and low cost SSHP
-->
<?php

// MySQL online database credentials hosted by Hostinger.com. 
// It is "localhost" because this PHP file is located on the same server as the database.
$servername = "localhost";
$dbname = "u700381576_HydroMonitor";
$username = "u700381576_esp8266";
$password = "0HHVK5b+";

// All of the following functions have been used in the "esp-outputs-action.php" and "ControlMain.php" files for interaction with the database


	// This function updates the state of the relay in the online database according to the "id" used with this funciton
    function updateOutput($id, $state) {
		
		// Uses the database credentials (global values) specified
        global $servername, $username, $password, $dbname;
		
        // An SQL query that connects to the MySQL database with the credentials above
        $conn = new mysqli($servername, $username, $password, $dbname);
		
        // If there was an error in connecting to the database return error code and exit code
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		// SQL query stored in "$sql" to update the "state" according to it's "id"
        $sql = "UPDATE Outputs SET state='" . $state . "' WHERE id='". $id .  "'";

		// If there was an error in the SQL ("$sql") query then return an error code
		// If not then return a success message. This was used for troubleshooting with the Postman API program
		if ($conn->query($sql) === TRUE) {
            return "Output state updated successfully";
        }
        else {
            return "Error: " . $sql . "<br>" . $conn->error;
        }
		// Close the SQL connection
        $conn->close();
    }
	
	
	// This function returns the "id", "name", "board", "gpio", and "state" values from the "Outputs" table in the online database
    function getAllOutputs() {
        
		// Uses the database credentials (global values) specified
        global $servername, $username, $password, $dbname;
		
        // An SQL query that connects to the MySQL database with the credentials above
        $conn = new mysqli($servername, $username, $password, $dbname);
		
        // If there was an error in connecting to the database return error code and exit code
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		// SQL query stored in "$sql" to select the "id", "name", "board", "gpio", and "state" values from the "Outputs" table 
        $sql = "SELECT id, name, board, gpio, state FROM Outputs ORDER BY board";
		
		// If there was no error in the SQL ("$sql") query then return the result of the query to the function user
        if ($result = $conn->query($sql)) {
            return $result;
        }
        else {
            return false;
        }
		// Close the SQL connection
        $conn->close();
    }
	
	
	// This function returns the "gpio" and "state" values from the "Outputs" table in the online database
    function getAllOutputStates($board) {
        
		// Uses the database credentials (global values) specified
        global $servername, $username, $password, $dbname;
		
        // An SQL query that connects to the MySQL database with the credentials above
        $conn = new mysqli($servername, $username, $password, $dbname);
		
        // If there was an error in connecting to the database return error code and exit code
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		// SQL query stored in "$sql" to select the "gpio" and "state" values from the "Outputs" table according to the "board" value used with this function
        $sql = "SELECT gpio, state FROM Outputs WHERE board='" . $board . "'";
		
		// If there was no error in the SQL ("$sql") query then return the result of the query to the function user
        if ($result = $conn->query($sql)) {
            return $result;
        }
        else {
            return false;
        }
		// Close the SQL connection
        $conn->close();
    }


	// This function gets the "board" and "last_request" values from the "Boards" table in the online database
    function getBoard($board) {
        
		// Uses the database credentials (global values) specified
        global $servername, $username, $password, $dbname;
		
        // An SQL query that connects to the MySQL database with the credentials above
        $conn = new mysqli($servername, $username, $password, $dbname);
		
        // If there was an error in connecting to the database return error code and exit code
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		// SQL query stored in "$sql" to select the "board" and "last_request" values from the "Boards" table according to the "board" value used with this function
        $sql = "SELECT board, last_request FROM Boards WHERE board='" . $board . "'";
		
		// If there was no error in the SQL ("$sql") query then return the result of the query to the function user
        if ($result = $conn->query($sql)) {
            return $result;
        }
        else {
            return false;
        }
		// Close the SQL connection
        $conn->close();
    }


?>