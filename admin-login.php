<?php
session_start();
$host = "fdb1027.runhosting.com";
$username = "4558323_esp8266"; // Default XAMPP username
$password = "Pass1234Word.";     // Default XAMPP password
$dbname = "4558323_esp8266";
// Connect to MySQL database
$conn = new mysqli($host, $username, $password, $dbname);

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    // Redirect to the admin home page
    header("Location: admin.php");
    exit;
}
// Logout functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    // Unset the 'admin' session variable to log out
    unset($_SESSION['admin']);
    // Redirect to admin.php after logout
    header("Location: admin-login.php");
    exit;
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
    <title>Parking Hub - Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" href='LOGO.png'>
</head>
<body>
<div class="admin-section">
<?php if ($showLoginForm): ?>
  
  <form id="login-form" action="" method="POST" style="display: block;">
      <div class="admin-label">       
      <h4 id="titleMain">Admin<span class="highlight1">Login</span></h4>
      </div>
      <div class="password-container">
          <input class="password" type="password" id="password" name="password" placeholder="Password" autocomplete="new-password" required>
      </div>
      <?php if (!empty($error_message)): ?>
          <div class="error-message" style="color: #ffa31a; text-align: center; margin-top: 10px; font-size: 20px;">
              <?php echo $error_message; ?>
          </div>
      <?php endif; ?>
      <div class="login-section">
          <input class="login" type="submit" value="Login">
      </div>
  </form>
<?php else: ?>
  <?php if (!empty($timeout_message)): ?>
      <div id="timeout-message" class="timeout-message" style="color: #FAD02C; text-align: center; margin-top: 10px; font-size: 20px;">
          <?php echo "<div>$timeout_message</div>"; ?>
      </div>
      <script>
          function startCountdown(seconds) {
              var countdownElement = document.getElementById('timeout-message');
              var interval = setInterval(function() {
                  countdownElement.innerHTML = 'You are currently in a timeout period.<br>Please try again in ' + seconds + ' seconds.';
                  if (seconds <= 0) {
                      clearInterval(interval);
                      window.location.href = 'admin-login.php'; 
                  }
                  seconds--;
              }, 1000);
          }
          startCountdown(4); 
      </script>
  <?php else: ?>
      <p style="color: green;">Login successful. Redirecting...</p>
  <?php endif; ?>
<?php endif; ?> 
  </div>
</body> 
</html>