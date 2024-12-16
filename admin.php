<?php
session_start();
$host = "fdb1027.runhosting.com";
$username = "4558323_esp8266";  
$password = "Pass1234Word.";   
$dbname = "4558323_esp8266";

// Connect to MySQL database
$conn = new mysqli($host, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the latest state for each signal
$sql1 = "SELECT signalName, startTime, endTime, TIMESTAMPDIFF(SECOND, startTime, endTime) AS duration 
        FROM SignalLogs
        WHERE id IN (
            SELECT MAX(id) FROM SignalLogs GROUP BY signalName
        )";

$result1 = $conn->query($sql1);

// Initialize an array to store signal data
$signals = [];

if ($result1 && $result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        $signals[$row['signalName']] = [
            'startTime' => $row['startTime'],
            'endTime' => $row['endTime'],
            'duration' => $row['duration'],
            'state' => strtotime($row['endTime']) < strtotime($row['startTime']) ? 'HIGH' : 'LOW',
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style-admin.css?v=<?php echo time(); ?>">
     <link rel="icon" href='LOGO.png'>
    <title>Parking Hub - Admin Page</title>
</head>
<body>

    <div class="whole-div">
        <h1 id="titleMain">Parking<span class="highlight">hub</span></h1>

        <table class="styled-table">
            <thead>
                <tr>
                    <th>Signal Name</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // SQL to retrieve all records from the SignalLogs table
            $sql = "SELECT * FROM SignalLogs ORDER BY startTime DESC";  // Order by start time (most recent first)
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                // Fetch all records and display them
                while ($row = $result->fetch_assoc()) {
                    // Calculate duration (in seconds)
                    $startTime = strtotime($row['startTime']);
                    $endTime = strtotime($row['endTime']);
                    $duration = ($endTime - $startTime); // Calculate the difference in seconds

                    echo "<tr>
                            <td>" . htmlspecialchars($row['signalName']) . "</td>
                            <td>" . htmlspecialchars($row['startTime']) . "</td>
                            <td>" . htmlspecialchars($row['endTime']) . "</td>
                            <td>" . ($duration > 0 ? $duration . " seconds" : '-') . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No records found.</td></tr>";
            }

            // Close the connection
            $conn->close();
            ?>
            </tbody>
        </table>

        <div class="admin-section1">
            <div class="admin-background">
                <form action="logout.php" method="POST">   
                    <input type="submit" value="Logout" class="login">
                </form>
            </div>
        </div>     
    </div>

</body> 
</html>
