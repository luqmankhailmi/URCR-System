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
$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

// retrieve all information about user
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uitmclubhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT user_email FROM users WHERE user_id='$user_id'";
$result = $conn->query($sql);
if ($result->num_rows) {
  $row = $result->fetch_assoc();
  $user_email = $row['user_email'];
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
 <link rel="stylesheet" href="/URCRS/css/user_profile.css">
 <link rel="icon" type="image/x-icon" href="/URCRS/images/C.png">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <title>Profile</title>
</head>

<body>
 <section id="navbar">
  <image id="logo" src="/URCRS/images/C.png" />
  <p id="title">URCRS</p>
  <div id="menu">
   <p class="menu-item"><a class="menu-link" href="/URCRS/student/dashboard.php">Dashboard</a></p>
   <p class="menu-item"><a class="menu-link" href="/URCRS/explore.php">Explore</a></p>
   <p class="menu-item"><a class="menu-link" href="/URCRS/student/help.php">Help</a></p>
   <p class='menu-item'><a id='login' href='/URCRS/student/user_profile.php'>My Profile</a></p>

  </div>
 </section>
 <section id="profile-container">
  <div id="left-side">
    <h1 id="mp-header">My Profile</h1>
    <image id="mp-pfp" src="/URCRS/images/no-pfp.png">
    <p id="mp-name"><?php echo $user_name; ?></p>
    <p id="mp-email"><?php echo $user_email; ?></p>
    <div id="mp-data"></div>
    <div id="mp-button">
      <button class="mp-button-items" onclick="displayEditModal()">Edit Informations</button>
      <button class="mp-button-items" onclick="window.location.href='/URCRS/auth/logout.php';">Log Out</button>
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
          echo "<p class='label-data'>Current name: </p><p class='content'>".$user_name."</p><p class='label-data'>New name: </p>";
          echo "<input type='text' id='new-name' name='new-name' placeholder='e.g. John Doe'><hr>";
          echo "<p class='label-data'>Current matric number: </p><p class='content'>".$user_matric."</p><p class='label-data'>New matric number: </p>";
          echo "<input type='text' id='new-matric' name='new-matric' placeholder='e.g. 2022123456'><hr>";
          echo "<p class='label-data'>Current semester: </p><p class='content'>".$user_sem."</p><p class='label-data'>New semester: </p>";
          echo "<input type='number' id='new-semester' name='new-semester' placeholder='e.g. 4'><hr>";
          echo "<p class='label-data'>Current programme code: </p><p class='content'>".$user_prog_code."</p><p class='label-data'>New programme code: </p>";
          echo "<input type='text' id='new-prog-code' name='new-prog-code' placeholder='e.g. CS110'><hr>";
          echo "<input id='submit-button' type='submit'>";
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