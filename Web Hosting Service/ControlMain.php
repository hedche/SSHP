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

	// The getAllOutputs() function retrieves all the information from the online database regarding the states of the boards and then stores it in "$result"
    $result = getAllOutputs();
	
	// "$html_buttons" is used to show the button. Currently there is only 1 button but this can be adapted to more buttons
    $html_buttons = null;
    if ($result) {
        while ($row = $result->fetch_assoc()) {
			
			// If the state of the relay is "1" then change the "$button_checked" value to "checked" showing the button is on
            if ($row["state"] == "1"){
                $button_checked = "checked";
            }
			
			// If the state of the relay is anything else (can only be 0) then set tehe "$button_checked" to nothing to show the button is off
            else {
                $button_checked = "";
            }
			// Prepares the "$html_buttons" value for its appearance in the HTML code at the end of this file
            $html_buttons .= '<h1>' . $row["name"] . '</h3><label class="switch"><input type="checkbox" onchange="updateOutput(this)" id="' . $row["id"] . '" ' . $button_checked . '><span class="slider"></span></label>';
        }
    }
?>

<!--
The "esp-style.css" file is responsible for the graphics for the on/off button.
The following HTML code shows a simple button and the state of the button is reflected 
with "$html_buttons" that was determined in the above PHP code. HTTP GET requests
to the "esp-outputs-action.php" file are then used with the updateOutput() function
to change the state of the relay in the online database due to an online user interacting
with the button.
-->
<!DOCTYPE HTML>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="esp-style.css">
        <title>ESP Output Control</title>
    </head>
<body>
    <h2>Control Dashboard</h2>
    <?php echo $html_buttons; ?>
    <br><br>
    <?php echo $html_boards; ?>
    <br><br>
    

    <script>
        function updateOutput(element) {
            var xhr = new XMLHttpRequest();
            if(element.checked){
                xhr.open("GET", "esp-outputs-action.php?action=output_update&id="+element.id+"&state=1", true);
            }
            else {
                xhr.open("GET", "esp-outputs-action.php?action=output_update&id="+element.id+"&state=0", true);
            }
            xhr.send();
        }
    </script>
</body>
</html>