<?php
// Check if the user is logged in as admin
// Database connection settings
session_start();
$host = "fdb1027.runhosting.com";
$username = "4558323_esp8266"; 
$password = "Pass1234Word.";     
$dbname = "4558323_esp8266";

// Connect to MySQL database
$conn = new mysqli($host, $username, $password, $dbname);

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    // Redirect to the admin page
    header("Location: admin.php");
    exit;
}
// Logout functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    // Unset the 'admin' session variable to log out
    unset($_SESSION['admin']);
    // Redirect to index.php after logout
    header("Location: index.php");
    exit;
}

// Fetch latest signal data from database
$sql = "SELECT state1, state2, state3, state4 FROM signal_data ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($sql);

$signalState1 = 0;  // Default value (LOW)
$signalState2 = 0;  // Default value (LOW)
$signalState3 = 0;  // Default value (LOW)
$signalState4 = 0;  // Default value (LOW)

if ($result->num_rows > 0) {
    // Fetch the latest state
    $row = $result->fetch_assoc();
    $signalState1 = $row["state1"];
    $signalState2 = $row["state2"];
    $signalState3 = $row["state3"];
    $signalState4 = $row["state4"];
}

$showLoginForm = true;
$error_message = "";
$timeout_message = "";
$remaining_time = 0;

// Check if the user is currently in a timeout period
if (isset($_SESSION['login_timeout']) && time() < $_SESSION['login_timeout']) {
    $remaining_time = $_SESSION['login_timeout'] - time();
    $timeout_message = "You are currently in a timeout period.<br>Please try again in $remaining_time seconds.";
    if ($_SESSION['login_attempts'] >= 3) {
        $timeout_message .= " You have 3 attempts left.";
    }
    $showLoginForm = false; // Hide the login form during timeout
} elseif (isset($_SESSION['login_timeout']) && time() >= $_SESSION['login_timeout']) {
    // Reset login attempts if timeout period has passed
    $_SESSION['login_attempts'] = 0;
    unset($_SESSION['login_timeout']);
}

// Initialize the number of failed login attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($timeout_message)) {
    // Get the submitted password
    $submitted_password = $_POST['password'];

    // Query to retrieve the stored password from the database
    $sql = "SELECT password FROM login LIMIT 1"; // Assuming there's only one password in the database
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $stored_password = $row['password'];

        // Compare submitted password with stored password
        if ($submitted_password === $stored_password) {
            // Reset the number of failed login attempts
            $_SESSION['login_attempts'] = 0;

            // Set a flag to hide the login form
            $showLoginForm = false;

            // Set admin session variable
            $_SESSION['admin'] = true;

            // Redirect to admin home page
            header("Location: admin.php");
            exit;
        } else {
            // Increment the number of failed login attempts
            $_SESSION['login_attempts']++;

            // Check if the maximum number of attempts has been reached
            if ($_SESSION['login_attempts'] >= 3) {
                // Set the timeout period to 5 seconds
                $_SESSION['login_timeout'] = time() + 5;
                $timeout_message = "You are currently in a timeout period.<br>Please try again in 5 seconds.";
                $remaining_time = 5;
                $showLoginForm = false; // Hide the login form during timeout
            }

            // Passwords do not match, handle accordingly (e.g., display an error message)
            $remaining_attempts = 3 - ($_SESSION['login_attempts']);
            $error_message = "Incorrect password! You have $remaining_attempts attempt(s) left.";
        }
    } else {
        // Handle database query error or no password found
        $error_message = "Invalid password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parking Hub - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
        <link rel="icon" href='LOGO.png'>
    
    <title>Parking Hub</title>
</head>
<body>
    <div class="mainDiv">
    <div class="whole-div">
    <h1 id="titleMain">Parking<span class="highlight">hub</span></h1>
        <div class="main-div">
            <div class="col">
                <div class="parkSlots" title="Slot 1" id="ps1">
                    <h3 id="slot1">SLOT 1</h3>
                    <h2 class="occupied" id="occ1">occupied</h2>
                    <h2 id="counter1">open</h2>
                </div>
                <div class="parkSlots" title="Slot 2" id="ps2">
                    <h3 id="slot2">SLOT 2</h3>
                    <h2 class="occupied" id="occ2">occupied</h2>
                    <h2 id="counter2">open</h2>
                </div>
            </div>
            <div class="col">
                <div class="parkSlots" title="Slot 3" id="ps3">
                    <h3 id="slot3">SLOT 3</h3>
                    <h2 class="occupied" id="occ3">occupied</h2>
                    <h2 id="counter3">open</h2>
                </div>
                <div class="parkSlots" title="Slot 4" id="ps4">
                    <h3 id="slot4">SLOT 4</h3>
                    <h2 class="occupied" id="occ4">occupied</h2>
                    <h2 id="counter4">open</h2>
                </div>
            </div>
            
        </div>
        <div class="button-section">
        <form action="admin-login.php">
              <button type="submit" class="submit" name="submit">Admin</button>
</form>
    </div> 
</div>
            <script>
                setTimeout(() => {
            location.reload();
                }, 500); // Refresh every 500 milliseconds
                // Signal state
                var signalState1 = <?php echo $signalState1; ?>;
                var signalState2 = <?php echo $signalState2; ?>;
                var signalState3 = <?php echo $signalState3; ?>;
                var signalState4 = <?php echo $signalState4; ?>;

                // Get the status box element
                var statusBox1 = document.getElementById("ps1");
                var statusOcc1 = document.getElementById("occ1");
                var statusOpen1 = document.getElementById("counter1");
                var statusBox2 = document.getElementById("ps2");
                var statusOcc2 = document.getElementById("occ2");
                var statusOpen2 = document.getElementById("counter2");
                var statusBox3 = document.getElementById("ps3");
                var statusOcc3 = document.getElementById("occ3");
                var statusOpen3 = document.getElementById("counter3");
                var statusBox4 = document.getElementById("ps4");
                var statusOcc4 = document.getElementById("occ4");
                var statusOpen4 = document.getElementById("counter4");

                // Change the color based on the signal state
                if (signalState1 === 1) {
                    statusBox1.style.backgroundColor="red";
                    statusBox1.style.color = "white";
                    statusOcc1.style.display="block";
                    statusOpen1.style.display="none";
                } else {
                    statusBox1.style.backgroundColor = "#39ff14"; // Signal LOW
                }
                if (signalState2 === 1) {
                    statusBox2.style.backgroundColor="red";
                    statusBox2.style.color = "white";
                    statusOcc2.style.display="block";
                    statusOpen2.style.display="none";
                } else {
                    statusBox2.style.backgroundColor = "#39ff14"; // Signal LOW
                }
                if (signalState3 === 1) {
                    statusBox3.style.backgroundColor="red";
                    statusBox3.style.color = "white";
                    statusOcc3.style.display="block";
                    statusOpen3.style.display="none";
                } else {
                    statusBox3.style.backgroundColor = "#39ff14"; // Signal LOW
                }
                if (signalState4 === 1) {
                    statusBox4.style.backgroundColor="red";
                    statusBox4.style.color = "white";
                    statusOcc4.style.display="block";
                    statusOpen4.style.display="none";
                } else {
                    statusBox4.style.backgroundColor = "#39ff14"; // Signal LOW
                }
            </script>
</body> 
</html>

