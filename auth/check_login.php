<?php
session_start();

// Database connection settings
$servername = "localhost";
$username="root";
$password="";
$dbname = "uitmclubhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Connection failed: ".$conn->connect_error);
}

$email = $_POST["input_email"];
$pass = $_POST["input_pass"];

// Query to check if user exists and credentials are correct
$sql = "SELECT * FROM users WHERE user_email = '$email' AND user_pass = '$pass'";
$result = $conn->query($sql);

// Check if user exists and credentials are correct
if ($result->num_rows > 0) {
    // User authenticated successfully, set up session
    $row = $result->fetch_assoc();
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['user_name'] = $row['user_name'];
    $_SESSION['user_type'] = $row['user_type'];

    // Redirect user to respective dashboard
    if ($_SESSION['user_type'] == "HEP") {
        header("Location: /URCRS/hep/admin_hep.php");
    } else if ($_SESSION['user_type'] == "Club Admin") {
        header("Location: /URCRS/club_admin/club_manage.php");
    } else {
        header("Location: /URCRS/student/dashboard.php");
        exit();
    }
} else {
    // Invalid credentials, redirect back to login page
    header("Location: /URCRS/login.php?error=invalid_credentials");
    exit();
}	

$conn->close();
?>