<?php
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $logged = FALSE;
    header("Location: /URCRS/login.php");
    exit();
}

$club_type = $_GET['id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uitmclubhub";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the category name from the URL parameter (assuming passed via GET)
$category_name = $_GET['id'];

// Check if the category is "ViewAllClub"
if ($category_name === "ViewAllClub") {
    // Prepare the SQL query to select all club names
    $sql = "SELECT club_name FROM CLUBS";
} else {
    // Prepare the SQL query to select club names by category
    $sql = "SELECT c.club_name
            FROM CLUBS c
            INNER JOIN CLUB_CATEGORY cc ON c.club_id = cc.club_id
            INNER JOIN CATEGORY cat ON cc.cat_id = cat.cat_id
            WHERE cat.cat_name = '$category_name'";
}

// Execute the SQL query
$result = $conn->query($sql);

$user_type = $_SESSION['user_type'];

?>

<!DOCTYPE html>
<html>

<head>
 <link rel="stylesheet" href="/URCRS/css/club_category.css">
 <link rel="icon" type="image/x-icon" href="/URCRS/images/C.png">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <title>Explore</title>
</head>

<body>
 <section id="navbar">
  <image id="logo" src="/URCRS/images/C.png" />
  <p id="title">URCRS</p>
  <div id="menu">
   <?php
    if ($user_type == "Student") {
      echo "<p class='menu-item'><a class='menu-link' href='/URCRS/student/dashboard.php'>Dashboard</a></p>";
    } else {
      echo "<p class='menu-item'><a class='menu-link' href='/URCRS/club_admin/club_manage.php'>Dashboard</a></p>";
    }
   ?>
   <p class="menu-item"><a class="menu-link" href="/URCRS/explore.php" style="color: #ac5180;">Explore</a></p>
   <?php
    if ($user_type == "Student") {
      echo "<p class='menu-item'><a class='menu-link' href='/URCRS/student/help.php'>Help</a></p>";
      echo "<p class='menu-item'><a id='login' href='/URCRS/student/user_profile.php'>My Profile</a>";
    } else if ($user_type == "Club Admin") {
      echo "<p class='menu-item'><a id='login' href='/URCRS/auth/logout.php'>Log Out</a>";
    } else {
      // to be determined
    }
   ?>

  </div>
 </section>
 <section id="header">
  <h1 id="header-text"><b><?php if ($club_type != "ViewAllClub") { echo $club_type; } else { echo "View All"; } ?> Club</b></h1>
  <button id="back-button" onclick="window.location.href='/URCRS/explore.php';">Back to Explore</button>
  <?php
    if ($result->num_rows > 0) {
      $total_club = $result->num_rows;
      if ($club_type == "ViewAllClub") {
        echo "<p id='total-club'>".$total_club." club(s) found</p><br>";
      }
      else {
        echo "<p id='total-club'>".$total_club." club(s) found with the tag '".$club_type."'</p><br>";
      }
    }
  ?>
 </section>
 <section id="club-list">
  <?php

   // Check if any rows were returned
   if ($result->num_rows > 0) {
     // Output data of each row
     while($row = $result->fetch_assoc()) {
	$club_name = $row["club_name"];
	echo "<button class='club-items' onclick=\"window.location.href='/URCRS/club_profile.php?id=".$club_name."';\"><image class='club-image' src='/URCRS/images/group.jpg'><p class='club-name'>".$club_name."</p></button>";
     }
   } else {
     echo "<p id='club-empty-text'>No clubs found for category: ".$category_name."</p>";
   }

   // Close the database connection
   $conn->close();
   ?>

 </section>
 <section id="footer"></section>
</body>

</html>