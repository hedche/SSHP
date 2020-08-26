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

// Target email address for the alert email to be sent to
$email_address = "mr17889@bristol.ac.uk";

// MySQL online database credentials hosted by Hostinger.com. It is 'localhost' because this PHP file is located on the same server as the database.
$servername = "localhost";
$dbname = "u700381576_HydroMonitor";
$username = "u700381576_esp8266";
$password = "0HHVK5b+";

// API key that matches with the API key in the TemperatureMonitor.ino file on the acquistion board.
$api_key_value = "SeHVWDv3sukYgmQh";

$api_key= $value = "";

// If the request was not an HTTP POST request then echo an error code
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get the API key (api_key) value from the POST request and store it as $api_key
	$api_key = test_input($_POST["api_key"]);
	
    // Compare API key's, if they don't match then echo an error code
    if($api_key == $api_key_value) {
        
        // Use the test_input function to get the power value and store it temporarily in $value
        $value = test_input($_POST["value"]);

        // If the power value surpasses the threshold value then prepare an alert email to be sent
        if($value > 55){
            
            // Compile the email with the value which surpassed the threshold value
            $email_msg = "The threshold value has been met!\nTemperature: " . $value . "°C";
            $email_msg = wordwrap($email_msg, 70);
            
            // Send the compiled $email_msg to the target email address with the subject "SSHP Alert: Temperature too high!"
            mail($email_address, "SSHP Alert: Temperature too high!", $email_msg);}
        
        // If the threshold value was surpassed or not: connect to the MySQL database with the credentials above
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // If there was an error in connecting to the database return error code and exit code
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);}
            
        // When connected to the database, compile SQL code to insert the $value from the IoT monitoring unit into the database under the table: 'TemperatureData' and in the column: 'Temperature'
        $sql = "INSERT INTO TemperatureData (Temperature) VALUES ('" . $value . "')";
        
        // If the SQL query to the database was unsuccessful then echo a success message if not error message
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";}
            
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;}
    
        // Close the database connection
        $conn->close();}
        
    else {
        echo "Wrong API Key";}
}
else {
    echo "No data posted with HTTP POST.";}

// The test_input function is responsible for grabbing information from the HTTP POST request
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;}