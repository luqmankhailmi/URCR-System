<?php
session_start();
$logged = TRUE;

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $logged = FALSE;
    header("Location: login.php");
    exit();
}

// Retrieve user's data from session
$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];
$club_name = $_GET["id"];
$user_type = $_SESSION['user_type'];

// Database connection settings
$servername = "localhost";
$username="root";
$password="";
$dbname = "uitmclubhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Connection failed: ".$conn->connect_error);
}

// Get club ID
$sql = "SELECT * FROM clubs WHERE club_name = '$club_name'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$club_id = $row['club_id'];

// Check whether the user had joined the club or not
$join_status = "No";
$sql = "SELECT * FROM user_club WHERE user_id = '$user_id' AND club_id = '$club_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  $join_status = "Yes";
}

// If not joined, check whether the user applied for the club or not
$apply_status = "No";
$sql = "SELECT * FROM applicant WHERE user_id = '$user_id' AND club_id = '$club_id' AND app_status = 'Pending'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  $apply_status = "Yes";
}

// get club info
$sql = "SELECT club_mission, club_vision, club_email, club_phone FROM clubs WHERE club_id='$club_id'";
   $result = $conn->query($sql);
   if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $club_mission = $row['club_mission'];
    $club_vision = $row['club_vision'];
    $club_email = $row['club_email'];
    $club_phone = $row['club_phone'];
}

?>

<!DOCTYPE html>
<html>

<head>
 <link rel="stylesheet" href="/URCRS/css/club_profile.css">
 <link rel="icon" type="image/x-icon" href="/URCRS/images/C.png">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <title>Explore</title>
</head>

<section>
 <section id="navbar">
  <image id="logo" src="/URCRS/images/C.png" />
  <p id="title">URCRS</p>
  <div id="menu">
   <?php
    if ($user_type == "Student") {
      echo "<p class='menu-item'><a class='menu-link' href='/URCRS/student/dashboard.php'>Dashboard</a></p>";
      echo "<p class='menu-item'><a class='menu-link' href='/URCRS/explore.php' style='color: #ac5180;'>Explore</a></p>";
    } else if ($user_type == "Club Admin") {
      echo "<p class='menu-item'><a class='menu-link' href='/URCRS/club_admin/club_manage.php'>Dashboard</a></p>";
      echo "<p class='menu-item'><a class='menu-link' href='/URCRS/explore.php' style='color: #ac5180;'>Explore</a></p>";
    } else {
      echo "<p class='menu-item'><a class='menu-link' href='/URCRS/hep/admin_hep.php'>Dashboard</a></p>";
    }
    if ($user_type == "Student") {
      echo "<p class='menu-item'><a class='menu-link' href='/URCRS/student/help.php'>Help</a></p>";
      echo "<p class='menu-item'><a id='login' href='/URCRS/student/user_profile.php'>My Profile</a>";
    } else if ($user_type == "Club Admin" || $user_type == "HEP") {
      echo "<p class='menu-item'><a id='login' href='/URCRS/auth/logout.php'>Log Out</a>";
    } else {
      // to be determined
    }
   ?>

  </div>
 </section>
 <!-- UPPER CLUB CONTAINER -->
 <section id="club-container">
  <image id="club-pfp" src="/URCRS/images/group.jpg">
  <h1 id="club-header"><?php echo $club_name; ?></h1>
  <div id="club-data"></div>
  <div id="club-button">
    <?php
    if ($user_type == "Student") {
      if ($join_status != "Yes" && $apply_status != "Yes") {
        echo "<button class='club-button-items' onclick=\"window.location.href='/URCRS/student/join_club.php?id=" . $club_id . "&name=" . urlencode($club_name) . "'\">Join club</button>";
      }
      else if ($apply_status == "Yes") {
        echo "<button class='club-button-items' onclick=\"window.location.href='/URCRS/student/unapply.php?id=" . $club_id . "&name=" . urlencode($club_name) . "'\">Applied</button>";
      }
      else {
        echo "<button class='club-button-items' onclick=\"window.location.href='/URCRS/student/leave_club.php?id=" . $club_id . "&name=" . urlencode($club_name) . "'\">Leave club</button>";
      }
    }
    ?>
    <button class="club-button-items" onclick="location.href='mailto:<?php echo $club_email; ?>';">Contact</button>
  </div>
 </section>
 <!-- LOWER CLUB CONTAINER -->
 <section id="club-about">
  <?php
    echo "<p class='club-title'>MISSION</p>";
    echo "<p class='club-desc'>".$club_mission."</p>";
    echo "<p class='club-title'>VISION</p>";
    echo "<p class='club-desc'>".$club_mission."</p>";
    echo "<p class='club-title'>CONTACT</p>";
    echo "<p class='club-desc'>Email: ".$club_email."<br>Phone: ".$club_phone."</p>";
  ?>
 </section>
</body>

</html>