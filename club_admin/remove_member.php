<?php

 session_start();
 $logged = TRUE;

 // Check if user is logged in, if not, redirect to login page
 if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
     $logged = FALSE;
     header("Location: login.php");
     exit();
 }

 // Database connection settings
 $servername = "localhost";
 $username="root";
 $password="";
 $dbname = "uitmclubhub";

 $conn = new mysqli($servername, $username, $password, $dbname);

 if ($conn->connect_error) {
  die("Connection failed: ".$conn->connect_error);
 }

 // Retrieve user's email from session
 $user_name = $_SESSION['user_name'];
 $club_id = $_SESSION['user_id'];
 $member_id = $_GET['id'];

 // remove member based on id
 $sql = "DELETE FROM user_club WHERE user_id='$member_id'";
 $result = $conn->query($sql);

 if ($result) {
    $_SESSION['UpdateStatus'] = "Member removed successfully!";
} else {
    $_SESSION['UpdateStatus'] = "Error removing member: " . mysqli_error($conn);
}

 $conn->close();

 header("Location: /URCRS/club_admin/club_manage.php");
 exit();

?>