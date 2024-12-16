<?php
session_start();
// Database configuration
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

// Get the signal states from POST data
$state1 = isset($_POST['state1']) ? $_POST['state1'] : 0;
$state2 = isset($_POST['state2']) ? $_POST['state2'] : 0;
$state3 = isset($_POST['state3']) ? $_POST['state3'] : 0;
$state4 = isset($_POST['state4']) ? $_POST['state4'] : 0;


// Get current timestamp
$currentTime = date('Y-m-d H:i:s');

// Insert signal states into the database
$sql = "INSERT INTO signal_data (state1, state2, state3, state4, timestamp) 
        VALUES ('$state1', '$state2', '$state3', '$state4', '$currentTime')";

if ($conn->query($sql) === TRUE) {
    echo "Data saved successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
