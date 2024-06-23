<?php
 session_start();

 $servername = "localhost";
 $username = "root";
 $password = "";
 $dbname = "uitmclubhub";

 $conn = new mysqli($servername, $username, $password, $dbname);

 if ($conn->connect_error) {
  die("Connection failed: ".$conn->connect_error);
 }

 $user_name = $_SESSION['user_name'];

 if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $club_name = $_POST['add-club-name'];
  $club_email = $_POST['add-club-email'];
  $club_pass = $_POST['add-club-pass'];
  $club_phone = $_POST['add-club-phone'];
  $club_category = $_POST['add-club-category'];
  $club_mission = $_POST['add-club-mission'];
  $club_vision = $_POST['add-club-vision'];

  if (empty($club_mission)) {
   $club_mission = "Not set.";
  }
  if (empty($club_vision)) {
   $club_vision = "Not set.";
  }

  // INSERT NEW USER (CLUB ADMIN)
  $sql_user = "INSERT INTO users (user_name, user_email, user_pass, user_type) VALUES ('$club_name','$club_email','$club_pass','Club Admin')";
  $result_user = $conn->query($sql_user);
  if ($result_user) {
   // INSERT DATA INTO CLUBS TABLE
   // get new club id
   $sql_get_id = "SELECT user_id FROM users WHERE user_email='$club_email'";
   $result_get_id = $conn->query($sql_get_id);
   if ($result_get_id->num_rows > 0) {
    $row = $result_get_id->fetch_assoc();
    $club_id = $row['user_id'];
   }
   // continue
   $sql_insert = "INSERT INTO clubs (club_id, club_name, club_email, club_phone, club_mission, club_vision) VALUES ('$club_id','$club_name','$club_email','$club_phone','$club_mission','$club_vision')";
   $result_insert = $conn->query($sql_insert);
   if ($result_insert) {
    // INSERT DATA INTO club_category TABLE
    $sql_category = "INSERT INTO club_category (club_id, cat_id) VALUES ('$club_id','$club_category')";
    $result_category = $conn->query($sql_category);
    if ($result_category) {
     $_SESSION['UpdateStatus'] = "Club has been added!";
     header("Location: /URCRS/hep/admin_hep.php");
    }
   }
  } else {
   echo "Error: ".$sql."<br>".$conn->error;
  }
 }
?>