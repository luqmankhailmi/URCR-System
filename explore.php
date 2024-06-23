<?php
session_start();
$logged = TRUE;

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $logged = FALSE;
    header("Location: /URCRS/login.php");
    exit();
}

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

$user_type = $_SESSION['user_type'];
?>

<!DOCTYPE html>
<html>

<head>
 <link rel="stylesheet" href="/URCRS/css/explore.css">
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
  <h1 id="header-text"><b>Explore</b></h1>
  <div id="search-container">
    <form action="/URCRS/explore.php?id=search" method="post">
      <?php
        if (isset($_POST['search-query'])) {
          echo "<input id='search-bar' type='text' name='search-query' placeholder='e.g. Sports Club' value='".$_POST['search-query']."'>";
        } else {
          echo "<input id='search-bar' type='text' name='search-query' placeholder='e.g. Sports Club'>";
        }
      ?>
	<button id="search-button" type="submit">Search</button>
    </form>
    <p id="search-desc">Browse from a total of 10+ clubs and organizations across UiTM Raub.</p>
    <!-- SEARCH RESULT -->
    <?php
      if (isset($_POST['search-query'])) {
        $search_query = $_POST['search-query'];
        $sql = "SELECT club_id, club_name FROM clubs WHERE club_name = '$search_query'";
        $result = $conn->query($sql);
        $total_club = $result->num_rows;
        echo "<p id='total-club'>".$total_club." club(s) found with the keyword '".$search_query."'</p>";
        echo "<section id='club-list'>";
        if ($result->num_rows > 0) {
          echo "<script>document.getElementById('header').style.height = '620px';</script>";
          while($row = $result->fetch_assoc()) {
            $club_name = $row["club_name"];
            echo "<button class='club-items' onclick=\"window.location.href='/URCRS/club_profile.php?id=".$club_name."';\"><image class='club-image' src='/URCRS/images/group.jpg'><p class='club-name'>".$club_name."</p></button>";
          }
        }
        echo "</section>";
      }
    ?>
    </section>
  </div>
 </section>
 <section id="club-type">
  <p id="club-type-text">Or explore from various club types...</p>
  <div id="club-type-grid">
	<div class="club-type-items"><button class="club-type-button" onclick="window.location.href='/URCRS/club_category.php?id=Language';"><p class="club-type-icon"><i  class="fa fa-institution"></i></p><p class="club-type-desc">Language</p></button></div>
	<div class="club-type-items"><button class="club-type-button" onclick="window.location.href='/URCRS/club_category.php?id=Academic';"><p class="club-type-icon"><i class="fa fa-mortar-board"></i></p><p class="club-type-desc">Academic</p></button></div>
	<div class="club-type-items"><button class="club-type-button" onclick="window.location.href='/URCRS/club_category.php?id=Science';"><p class="club-type-icon"><i class="fa fa-rocket"></i></p><p class="club-type-desc">Science</p></button></div>
	<div class="club-type-items"><button class="club-type-button" onclick="window.location.href='/URCRS/club_category.php?id=Sports';"><p class="club-type-icon"><i class="fa fa-soccer-ball-o"></i></p><p class="club-type-desc">Sports</p></button></div>
	<div class="club-type-items"><button class="club-type-button" onclick="window.location.href='/URCRS/club_category.php?id=Photography';"><p class="club-type-icon"><i class="fa fa-camera-retro"></i></p><p class="club-type-desc">Photography</p></button></div>
	<div class="club-type-items"><button class="club-type-button" onclick="window.location.href='/URCRS/club_category.php?id=Tech';"><p class="club-type-icon"><i class="fa fa-code-fork"></i></p><p class="club-type-desc">Tech</p></button></div>
	<div class="club-type-items"><button class="club-type-button" onclick="window.location.href='/URCRS/club_category.php?id=Artistic';"><p class="club-type-icon"><i class="fa fa-signing"></i></p><p class="club-type-desc">Artistic</p></button></div>
	<div class="club-type-items"><button class="club-type-button" onclick="window.location.href='/URCRS/club_category.php?id=ViewAllClub';"><p class="club-type-icon"><i class="fa fa-list"></i></p><p class="club-type-desc">View All Clubs</p></button></div>
  </div>
 </section>
 <section id="footer"></section>
</body>

</html>