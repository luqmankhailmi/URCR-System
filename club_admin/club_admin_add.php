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

 $query = "SELECT club_id FROM clubs WHERE club_name = '$user_name'";
 $result = mysqli_query($conn, $query);

 if ($result && mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  $club_id = $row['club_id'];
 } else {
  // Handle error or display message if user not found
 }

 if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (isset($_POST["add_type"])) {
    $add_type = $_POST["add_type"];

    if ($add_type == "Announcement") {
     $annc_title = $_POST['add-annc-title'];
     $desc = $_POST['add-description'];
     $current_date = date("d-m-Y");
     $sql = "INSERT INTO announcement (ann_content, club_id, ann_title, ann_add_date) VALUES ('$desc','$club_id','$annc_title','$current_date')";

     if ($conn->query($sql)===true) {
      $_SESSION['UpdateStatus'] = "Announcement has been added!";
      header("Location: /URCRS/club_admin/club_manage.php");
     } else {
      echo "Error: ".$sql."<br>".$conn->error;
     }
    }
    else {
     $title = $_POST['add-event-title'];
     $desc = $_POST['add-event-desc'];
     $date = $_POST['add-event-date'];
     $formattedDate = date("d-m-Y", strtotime($date));
     $sql = "INSERT INTO events (event_name, event_desc, club_id, event_date) VALUES ('$title', '$desc', '$club_id','$formattedDate')";

     if ($conn->query($sql)===true) {
      $_SESSION['UpdateStatus'] = "Event has been added!";
      header("Location: /URCRS/club_admin/club_manage.php");
     } else {
      echo "Error: ".$sql."<br>".$conn->error;
     }
    }
   }
 }
?>