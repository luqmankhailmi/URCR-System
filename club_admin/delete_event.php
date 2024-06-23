<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

// Check if event_id is provided
if (!isset($_GET['event_id'])) {
    $_SESSION['UpdateStatus'] = "Event ID not provided!";
    header("Location: main_page.php");
    exit();
}

$event_id = $_GET['event_id'];

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uitmclubhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete event from database
$query = "DELETE FROM events WHERE event_id = $event_id";
$result = mysqli_query($conn, $query);

if ($result) {
    $_SESSION['UpdateStatus'] = "Event deleted successfully!";
} else {
    $_SESSION['UpdateStatus'] = "Error deleting event: " . mysqli_error($conn);
}

$conn->close();

header("Location: /URCRS/club_admin/club_manage.php");
exit();
?>
