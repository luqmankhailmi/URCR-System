<?php
session_start();
$logged = TRUE;

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $logged = FALSE;
    header("Location: login.php");
    exit();
}

// Retrieve user's email from session
$user_id = $_GET['id'];
$user_type = $_SESSION['user_type'];

// retrieve all information about user
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uitmclubhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT user_email, user_name FROM users WHERE user_id='$user_id'";
$result = $conn->query($sql);
if ($result->num_rows) {
  $row = $result->fetch_assoc();
  $user_email = $row['user_email'];
  $user_name = $row['user_name'];
}

$sql = "SELECT * FROM student_details WHERE user_id='$user_id'";
$result = $conn->query($sql);
if ($result->num_rows) {
  $row = $result->fetch_assoc();
  $user_matric = $row['stud_matric'];
  $user_sem = $row['stud_semester'];
  $user_prog_code = $row['stud_prog_code'];
}

?>

<!DOCTYPE html>
<html>

<head>
 <link rel="stylesheet" href="/URCRS/css/public_profile.css">
 <link rel="icon" type="image/x-icon" href="/URCRS/images/C.png">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <title>Profile</title>
</head>

<body>
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
 <section id="profile-container">
  <div id="left-side">
    <h1 id="mp-header">Profile</h1>
    <image id="mp-pfp" src="/URCRS/images/no-pfp.png">
    <p id="mp-name"><?php echo $user_name; ?></p>
    <p id="mp-email"><?php echo $user_email; ?></p>
    <div id="mp-data"></div>
    <div id="mp-button">
      <button class="mp-button-items" onclick="location.href='mailto:<?php echo $user_email; ?>';">Contact</button>
    </div>
  </div>
  <div id="right-side">
    <!-- DISPLAY DETAILED INFORMATION -->
     <p class="label">Matric number: </p>
    <p class="data"><?php echo $user_matric; ?></p>
    <p class="label">Current semester: </p>
    <p class="data"><?php echo $user_sem; ?></p>
    <p class="label">Programme code: </p>
    <p class="data"><?php echo $user_prog_code; ?></p>
    <p class="label">Club: </p>
    <div id="my-club">
      <?php
      // get all associated club
      $sql = "SELECT club_id FROM user_club WHERE user_id='$user_id'";
      $result_club = $conn->query($sql);
      if ($result_club && mysqli_num_rows($result_club) > 0) {
        while ($row = mysqli_fetch_assoc($result_club)) {
          $club_id = $row['club_id'];
          $sql = "SELECT club_name FROM clubs WHERE club_id='$club_id'";
          $result_name = $conn->query($sql);
          $get_row = $result_name->fetch_assoc();
          $club_name = $get_row['club_name'];
          echo "<button class='club-items' onclick=\"window.location.href='/URCRS/club_profile.php?id=" . urlencode($club_name) . "';\"><p class='club-name'>" . htmlspecialchars($club_name) . "</p></button>";
        }
      } else {
        echo "<p id='club-empty-text'>No clubs registered!</p>";
      }
      ?>
    </div>
  </div>
 </section>
 <div id="modal-edit" class="modal">
    <div class="modal-content">
      <span class="close-edit close">Close &times;</span>
      <p class="modal-header">EDIT PROFILE DETAILS</p>
      <form action="/URCRS/student/edit_profile.php" method="post">
      <?php
          echo "<p>Current name: </p><p>".$user_name."</p><p>New name: </p>";
          echo "<input type='text' id='new-name' name='new-name'><hr>";
          echo "<p>Current matric number: </p><p>".$user_matric."</p><p>New matric number: </p>";
          echo "<input type='text' id='new-matric' name='new-matric'><hr>";
          echo "<p>Current semester: </p><p>".$user_sem."</p><p>New semester: </p>";
          echo "<input type='number' id='new-semester' name='new-semester'><hr>";
          echo "<p>Current programme code: </p><p>".$user_prog_code."</p><p>New programme code: </p>";
          echo "<input type='text' id='new-prog-code' name='new-prog-code'><hr>";
          echo "<input type='submit'>";
      ?>
      </form>
    </div>
  </div>
 <script>
  function displayEditModal() {
    // Get the <span> element that closes the modal
    modal = document.getElementById("modal-edit");
    span = document.getElementsByClassName("close-edit")[0];

    // When the user clicks on the button, open the modal
    modal.style.display = "block";

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
      modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  }
 </script>
</body>

</html>