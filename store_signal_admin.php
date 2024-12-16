<?php
// Database connection details
$servername = "fdb1027.runhosting.com"; 
$username = "4558323_esp8266";        
$password = "Pass1234Word.";            
$dbname = "4558323_esp8266";       

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if POST data is received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $signalName = $_POST['signal'];  // Retrieve signal name
    $startTime = $_POST['startTime'];  // Retrieve start time
    $endTime = $_POST['endTime'];  // Retrieve end time

    // SQL to insert data into SignalLogs table
    $sql = "INSERT INTO SignalLogs (signalName, startTime, endTime) 
            VALUES ('$signalName', '$startTime', '$endTime')";

    if ($conn->query($sql) === TRUE) {
        echo "Record added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Invalid request method";
}

// Close the connection
$conn->close();
?>
