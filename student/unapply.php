<?php
 session_start();

 // Retrieve user's email from session
 $user_id = $_SESSION['user_id'];
 $club_id = $_GET["id"];
 $club_name = $_GET["name"];

 // Database connection settings
 $servername = "localhost";
 $username="root";
 $password="";
 $dbname = "uitmclubhub";

 $conn = new mysqli($servername, $username, $password, $dbname);

 if ($conn->connect_error) {
  die("Connection failed: ".$conn->connect_error);
 }

 // Add new data to 'applicant' table
 $sql = "DELETE FROM applicant WHERE user_id='$user_id' AND club_id='$club_id' AND app_status='Pending';";
 $result = $conn->query($sql);

 if ($result === true) {
  echo "Success";
  header("Location: /URCRS/club_profile.php?id=" . urlencode($club_name));
 }

?>