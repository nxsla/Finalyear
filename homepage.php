
<?php
session_start();
include("connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="style.css"> <!-- Include your CSS file -->
</head>
<body>
    <header>
        <h1>Welcome to Our Bank</h1>
        <nav>
            <ul>
                <li><a href="login.php">Online Banking Login</a></li>
                <li><a href="account_services.php">Account Services</a></li>
                <li><a href="loan_information.php">Loan Information</a></li>
                <li><a href="customer_support.php">Customer Support</a></li>
                <li><a href="faqs.php">FAQs</a></li>
            </ul>
        </nav>
        <div id="current-date">
            <?php echo "Current date: " . date("l, F j, Y"); ?>
        </div>
    </header>

    <div style="text-align:top-center; padding:15%;">
      <p style="font-size:50px; font-weight:bold;">
       Hello <?php 
       if (isset($_SESSION['email'])) {
           $email = $_SESSION['email'];
           $query = mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
           while ($row = mysqli_fetch_array($query)) {
               echo $row['firstName'] . ' ' . $row['lastName'];
           }
       }
       ?>
       :)
      </p>
    </div>

    <footer>
        <p>&copy; 2025 BioDefend. All rights reserved.</p>
        <p><a href="privacy_policy.php">Privacy Policy</a> | <a href="terms_conditions.php">Terms & Conditions</a></p>
        <a href="logout.php">Logout</a>
    </footer>

</body>
</html>

