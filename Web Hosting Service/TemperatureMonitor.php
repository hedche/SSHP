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

// MySQL online database credentials hosted by Hostinger.com
$servername = "localhost";
$dbname = "u700381576_HydroMonitor";
$username = "u700381576_esp8266";
$password = "0HHVK5b+";

// An SQL query that connects to the MySQL database with the credentials above
$conn = new mysqli($servername, $username, $password, $dbname);

// If there was an error in connecting to the database return error code and exit code
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Store an SQL query in "$sql" that selects the values from the "ID", "Temperature", and "DateTime" columns from the "TemperatureData" table
// Also selects the values by descending values of "DateTime" so the readings shown on the graph are the most recent
$sql = "SELECT ID, Temperature, DateTime FROM TemperatureData order by DateTime desc limit 40";

// Uses  "$conn" and "$sql" to query the connected database and select the latest 40 values
// Store the result from the SQL query in "$result"
$result = $conn->query($sql);

// Store the data from the database query in an array called "$sensor_data"
while ($data = $result->fetch_assoc()){
    $sensor_data[] = $data;
}

// Filtering the "DateTime" column from the "$sensor_data" array into "$readings_time"
$readings_time = array_column($sensor_data, 'DateTime');

// Filtering the "Temperature" column from the "$sensor_data" array into "$value"
$value1 = json_encode(array_reverse(array_column($sensor_data, 'Temperature')), JSON_NUMERIC_CHECK);

// Reverse the order of the "$readings_time" so that when plotting the graph with respect to date and time, time increases from left to right
$reading_time = json_encode(array_reverse($readings_time), JSON_NUMERIC_CHECK);

// Close database connection and free resources
$result->free();
$conn->close();
?>

<!--
  The following HTML code uses the highcharts javascript from https://code.highcharts.com/ to plot the data from the "$value" (y-axis) and "$readings_time" (x-axis) arrays
  It has a title "Temperature" with the axis labeled with the units and the Date and Time for each value on the x-axis.
-->
<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <style>
    body {
      min-width: 310px;
    	max-width: 1280px;
    	height: 500px;
      margin: 0 auto;
    }
    h2 {
      font-family: Arial;
      font-size: 2.5rem;
      text-align: center;
    }
  </style>
  <body>
    <div id="chart-temperature" class="container"></div>
<script>

var value1 = <?php echo $value1; ?>;
var reading_time = <?php echo $reading_time; ?>;

var chartT = new Highcharts.Chart({
  chart:{ renderTo : 'chart-temperature' },
  title: { text: 'Temperature' },
  series: [{
    showInLegend: false,
    data: value1
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    },
    series: { color: '#059e8a' }
  },
  xAxis: { 
    type: 'datetime',
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Temperature (Celsius)' }
  },
  credits: { enabled: false }
});

</script>
</body>
</html>