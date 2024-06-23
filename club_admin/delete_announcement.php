<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

// Check if ann_id is provided
if (!isset($_GET['ann_id'])) {
    $_SESSION['UpdateStatus'] = "Announcement ID not provided!";
    header("Location: main_page.php");
    exit();
}

$ann_id = $_GET['ann_id'];

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uitmclubhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete announcement from database
$query = "DELETE FROM announcement WHERE ann_id = $ann_id";
$result = mysqli_query($conn, $query);

if ($result) {
    $_SESSION['UpdateStatus'] = "Announcement deleted successfully!";
} else {
    $_SESSION['UpdateStatus'] = "Error deleting announcement: " . mysqli_error($conn);
}

$conn->close();

header("Location: /URCRS/club_admin/club_manage.php");
exit();
?>