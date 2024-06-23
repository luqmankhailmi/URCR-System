<?php
 session_start();
 $user_name = $_SESSION['user_name'];
 $user_id = $_SESSION['user_id'];

 $servername = "localhost";
 $username = "root";
 $password = "";
 $dbname = "uitmclubhub";

 $conn = new mysqli($servername, $username, $password, $dbname);

 if ($conn->connect_error) {
  die("Connection Failed: ".$conn->connect_error);
 }

 if ($_SERVER['REQUEST_METHOD']==="POST") {
  $new_name = $_POST['new-name'];
  $new_matric = $_POST['new-matric'];
  $new_semester = $_POST['new-semester'];
  $new_prog_code = $_POST['new-prog-code'];

  if (!empty($new_name)) {
   $sql = "UPDATE users SET user_name = '$new_name' WHERE user_id = '$user_id'";
   $execute = mysqli_query($conn, $sql)  or die ("Error: " . mysqli_error($conn));
   $_SESSION['user_name'] = $new_name;
   // $_SESSION['UpdateStatus'] = "Mission has been updated";

  }

  if (!empty($new_matric)) {
   $sql = "UPDATE student_details SET stud_matric = '$new_matric' WHERE user_id = '$user_id'";
   $execute = mysqli_query($conn, $sql)  or die ("Error: " . mysqli_error($conn));
   // $_SESSION['UpdateStatus'] = "Mission has been updated";

  }
  if (!empty($new_semester)) {
   $sql = "UPDATE student_details SET stud_semester = '$new_semester' WHERE user_id = '$user_id'";
   $execute = mysqli_query($conn, $sql)  or die ("Error: " . mysqli_error($conn));
   // $_SESSION['UpdateStatus'] = "Vision has been updated";
  }
  if (!empty($new_prog_code)) {
   $sql = "UPDATE student_details SET stud_prog_code = '$new_prog_code' WHERE user_id = '$user_id'";
   $execute = mysqli_query($conn, $sql)  or die ("Error: " . mysqli_error($conn));
   // $_SESSION['UpdateStatus'] = "Email has been updated";
  }
 }
 header("Location: /URCRS/student/user_profile.php");
?>