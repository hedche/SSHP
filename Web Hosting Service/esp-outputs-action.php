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
	// Include the "ControlDatabase.php" file to call various funcitons to interact with the online database
    include_once('ControlDatabase.php');

    $action = $id = $name = $gpio = $state = "";

	// If the request to the URL: "esp-outputs-action.php" is an HTTP GET request then process it using the following code
	// If NOT, then echo an error code
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
		
		// Use the test_input function to extract the "action" value from the HTTP GET request
        $action = test_input($_GET["action"]);
		
		// If the "action" value in the HTTP request from the acquisiton board is "outputs_state" then:
		// "outputs_state" is used when the acquisiton board is requesting the state value of the relay
        if ($action == "outputs_state") {
			
			// Use the test_input function to extract the board value
            $board = test_input($_GET["board"]);
			
			// The getAllOutputStates() function returns the gpio number and state for the specified board
            $result = getAllOutputStates($board);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[$row["gpio"]] = $row["state"];
                }
            }
			// Echo the state in a JSON response for the acquistion board to process
            echo json_encode($rows);
            $result = getBoard($board);
            
        }
		
		// If the action in the HTTP request from the acquisiton board is "output_update" then:
		// "output_update" is used when the acquisiton board is requesting to alter state value of the relay in the online database
        else if ($action == "output_update") {
			
			// Get the "id" value from the request
            $id = test_input($_GET["id"]);
			
			// Get the "state" value from the request
            $state = test_input($_GET["state"]);
			
			// Using the updateOutput() function from the "ControlDatabase.php" file to change the state in teh online database
            $result = updateOutput($id, $state);
			echo $result;
        }
        
		// If there was no recognised "action" value then return an error code
        else {
            echo "Invalid HTTP request.";
        }
    }

	// test_input funciton is used to extract data from the HTTP request
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>