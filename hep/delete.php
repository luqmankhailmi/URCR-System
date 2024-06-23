<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

$data_type = $_GET['data'];
$data_id = $_GET['id'];

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uitmclubhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($data_type == "club") {
 $result_club_1 = $conn->query("DELETE FROM user_club WHERE club_id='$data_id';");
 $result_club_2 = $conn->query("DELETE FROM club_category WHERE club_id='$data_id';"); 
 $result_club_3 = $conn->query("DELETE FROM announcement WHERE club_id='$data_id';"); 
 $result_club_4 = $conn->query("DELETE FROM events WHERE club_id='$data_id';"); 
 $result_club_5 = $conn->query("DELETE FROM applicant WHERE club_id='$data_id';");
 $result_club_6 = $conn->query("DELETE FROM clubs WHERE club_id='$data_id';");
 $result_club_7 = $conn->query("DELETE FROM users WHERE user_id='$data_id';");  
 if ($result_club_1 && $result_club_2 && $result_club_3 && $result_club_4 && $result_club_5 && $result_club_6 && $result_club_7) {
  $_SESSION['UpdateStatus'] = "Club had been removed!";
  header("Location: /URCRS/hep/admin_hep.php");
 }
} else if ($data_type == "student") {
 $result_stud_1 = $conn->query("DELETE FROM user_club WHERE user_id='$data_id';");
 $result_stud_2 = $conn->query("DELETE FROM applicant WHERE user_id='$data_id';");
 $result_stud_3 = $conn->query("DELETE FROM student_details WHERE user_id='$data_id';");
 $result_stud_4 = $conn->query("DELETE FROM users WHERE user_id='$data_id';");
 if ($result_stud_1 && $result_stud_2 && $result_stud_3 && $result_stud_4) {
  $_SESSION['UpdateStatus'] = "Student had been removed!";
  header("Location: /URCRS/hep/admin_hep.php");
 }
} else if ($data_type == "annc") {
 $sql_annc = "DELETE FROM announcement WHERE ann_id='$data_id';";
 $result_annc = $conn->query($sql_annc);
 if ($result_annc) {
  $_SESSION['UpdateStatus'] = "Announcement had been removed!";
  header("Location: /URCRS/hep/admin_hep.php");
 }
} else if ($data_type == "event") {
 $sql_event = "DELETE FROM events WHERE event_id='$data_id';";
 $result_event = $conn->query($sql_event);
 if ($result_event) {
  $_SESSION['UpdateStatus'] = "Event had been removed!";
  header("Location: /URCRS/hep/admin_hep.php");
 }
} else {
 // future data
}
exit();
?>
