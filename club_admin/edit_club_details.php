<?php
 session_start();
 $user_name = $_SESSION['user_name'];

 $servername = "localhost";
 $username = "root";
 $password = "";
 $dbname = "uitmclubhub";

 $conn = new mysqli($servername, $username, $password, $dbname);

 if ($conn->connect_error) {
  die("Connection Failed: ".$conn->connect_error);
 }

 if ($_SERVER['REQUEST_METHOD']==="POST") {
  $new_mission = $_POST['new-mission'];
  $new_vision = $_POST['new-vision'];
  $new_email = $_POST['new-email'];
  $new_phone = $_POST['new-phone'];

  if (!empty($new_mission)) {
   $sql = "UPDATE clubs SET club_mission = '$new_mission' WHERE club_name = '$user_name'";
   $execute = mysqli_query($conn, $sql)  or die ("Error: " . mysqli_error($conn));
   $_SESSION['UpdateStatus'] = "Mission has been updated";

  }
  if (!empty($new_vision)) {
   $sql = "UPDATE clubs SET club_vision = '$new_vision' WHERE club_name = '$user_name'";
   $execute = mysqli_query($conn, $sql)  or die ("Error: " . mysqli_error($conn));
   $_SESSION['UpdateStatus'] = "Vision has been updated";
  }
  if (!empty($new_email)) {
   $sql = "UPDATE clubs SET club_email = '$new_email' WHERE club_name = '$user_name'";
   $execute = mysqli_query($conn, $sql)  or die ("Error: " . mysqli_error($conn));
   $_SESSION['UpdateStatus'] = "Email has been updated";
  }
  if (!empty($new_phone)) {
   $sql = "UPDATE clubs SET club_phone = '$new_phone' WHERE club_name = '$user_name'";
   $execute = mysqli_query($conn, $sql)  or die ("Error: " . mysqli_error($conn));
   $_SESSION['UpdateStatus'] = "Phone has been updated";
  }
 }
 header("Location: /URCRS/club_admin/club_manage.php");
?>