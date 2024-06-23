<?php
// Include database connection
session_start();
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "uitmclubhub";

$conn = new mysqli($hostname,$username,$password,$dbname);


$user_id = $_GET['id'];
$action = $_GET['value'];
$club_id = $_GET['club'];

$sql = "SELECT user_id, club_id FROM applicant WHERE user_id='$user_id' AND club_id='$club_id' AND app_status='Pending'";
$result = mysqli_query($conn,$sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = $result->fetch_assoc();
    if ($action == "approve") {
    // update applicant status
    $sql = "UPDATE applicant SET app_status = 'Approved' WHERE user_id='$user_id' AND club_id='$club_id' AND app_status='Pending'";
    $result = $conn->query($sql);
    // insert into user_club (new member)
    $sql = "INSERT INTO user_club (user_id, club_id) VALUES ('$user_id', '$club_id')";
    $result = $conn->query($sql);

    $_SESSION['UpdateStatus'] = "Application has been approved!";
    } else {
    $sql = "UPDATE applicant SET app_status = 'Rejected' WHERE user_id='$user_id' AND club_id='$club_id' AND app_status='Pending'";
    $result = $conn->query($sql);

    $_SESSION['UpdateStatus'] = "Application has been rejected!";
    }
}
header("Location: /URCRS/club_admin/club_manage.php");
?>