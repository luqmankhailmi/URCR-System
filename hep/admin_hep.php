<?php

session_start();
$logged = TRUE;

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $logged = FALSE;
    header("Location: /URCRS/login.php");
    exit();
}

if (isset($_SESSION['UpdateStatus'])) {
  if ($_SESSION['UpdateStatus'] != "") {
    echo "<script>alert('".$_SESSION['UpdateStatus']."');</script>";
    $_SESSION['UpdateStatus'] = "";
  }
}

// Database connection settings
$servername = "localhost";
$username="root";
$password="";
$dbname = "uitmclubhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Connection failed: ".$conn->connect_error);
}

// Retrieve user's email from session
$user_name = $_SESSION['user_name'];
$admin_id = $_SESSION['user_id'];

// Function to remove an announcement
function removeAnnouncement($conn, $ann_id) {
    $query = "DELETE FROM announcement WHERE ann_id = $ann_id";
    $result = mysqli_query($conn, $query);

    if($result) {
        return true; // Return true if deletion is successful
    } else {
        return "Error deleting announcement: " . mysqli_error($conn); // Return error message if deletion fails
    }
}

// Function to remove an event
function removeEvent($conn, $event_id) {
    $query = "DELETE FROM events WHERE event_id = $event_id";
    $result = mysqli_query($conn, $query);

    if($result) {
        return true; // Return true if deletion is successful
    } else {
        return "Error deleting event: " . mysqli_error($conn); // Return error message if deletion fails
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<meta name="google" content="notranslate">
<head>
 <link rel="stylesheet" href="/URCRS/css/admin_hep.css">
 <link rel="icon" type="image/x-icon" href="/URCRS/images/C.png">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <title>Admin Page</title>
</head>

<body>
 <!-- MAIN NAVIGATION BAR -->
 <section id="navbar">
  <image id="logo" src="/URCRS/images/C.png" />
  <p id="title">URCRS</p>
  <div id="menu">
    <p class="menu-item"><a class="menu-link" href="/URCRS/hep/admin_hep.php" style="color: #ac5180;">Dashboard</a></p>
   <p class='menu-item'><a id='login' href='/URCRS/auth/logout.php'>Log Out</a></p>
  </div>
 </section>
 <!-- CLUB CONTAINER -->
 <section id="content-container">
   <!-- LEFT SIDE (CLUB NAVIGATION BAR) -->
   <div id="content-nav">
    <button id="club-list-button" class="content-nav-button" onclick="display('Club')" style="background-color: white; color: #ac5180;">Show All Club</button>
    <button class="content-nav-button" onclick="display('Student')" id="student-button">Show All Student</button>
    <button id="annc-button" class="content-nav-button" onclick="display('Announcement')">Show All Announcement</button>
    <button id="event-button" class="content-nav-button" onclick="display('Event')">Show All Event</button>
    <button class="content-nav-button" onclick="displayModal('Add')" id="Add">+ Add New Club</button>
   </div>
   <!-- LEFT SIDE (CLUB NAVIGATION BAR) -->
   <div id="content-data">
    <!-- LIST ALL CLUB -->
     <div id="club">
      <?php
      $sql = "SELECT * FROM clubs";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $club_id = $row['club_id'];
            $club_name = $row['club_name'];
            $club_email = $row['club_email'];
            
            echo "<div class='list-club-items'>";
            echo "<p class='applicant-name app-details'>Club Name: " . $club_name . "</p>";
            echo "<p class='applicant-email app-details'>Club Email: " . $club_email . "</p>";
            echo "<button class='contact-button app-button' onclick=\"location.href='mailto:".$club_email."'\">Contact</button>";
            echo "<button class='view-button app-button' onclick=\"window.location.href='/URCRS/club_profile.php?id=".$club_name."'\">View Profile</button>";
            echo "<button class='delete-button app-button' onclick=\"window.location.href='/URCRS/hep/delete.php?data=club&id=".$club_id."'\">Delete</button>";
            echo "</div>";
        }
      } else {
        echo "<p class='item-empty-text'>No club found!</p>";
      }
      ?>
     </div>
     <!-- LIST ALL REGISTERED STUDENT -->
     <div id="student" style="display: none;">
      <?php
      $sql_get_student = "SELECT * FROM users WHERE user_type='Student'";
      $result_get_student = $conn->query($sql_get_student);
      if ($result_get_student->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result_get_student)) {
          $stud_id = $row['user_id'];
          $stud_name = $row['user_name'];
          $stud_email = $row['user_email'];

          echo "<div class='list-stud-items'>";
          echo "<p class='applicant-name app-details'>Student Name: " . $stud_name . "</p>";
          echo "<p class='applicant-email app-details'>Student Email: " . $stud_email . "</p>";
          echo "<button class='contact-button app-button' onclick=\"location.href='mailto:".$stud_email."'\">Contact</button>";
          echo "<button class='view-button app-button' onclick=\"window.location.href='/URCRS/public_profile.php?id=".$stud_id."'\">View Profile</button>";
          echo "<button class='delete-button app-button' onclick=\"window.location.href='/URCRS/hep/delete.php?data=student&id=".$stud_id."'\">Delete</button>";
          echo "</div>";
        }
      } else {
        echo "<p class='item-empty-text'>No student found!</p>";
      }
      ?>
     </div>
    <!-- LIST ALL ANNOUNCEMENTS -->
    <div id="announcement" style="display: none;">
    <?php
    $sql_get_annc = "SELECT * FROM announcement ORDER BY ann_add_date DESC";
    $result_get_annc = mysqli_query($conn, $sql_get_annc);
 
    if ($result_get_annc && mysqli_num_rows($result_get_annc) > 0) {
        while ($row = mysqli_fetch_assoc($result_get_annc)) {
          $club_id = $row['club_id'];
          // get club id
          $sql_get_club = "SELECT club_name FROM clubs WHERE club_id='$club_id'";
          $result_get_club = $conn->query($sql_get_club);
          if ($result_get_club->num_rows > 0) {
            $row_club = $result_get_club->fetch_assoc();
            $get_club = $row_club['club_name'];
            $get_id = $row['ann_id'];
            $get_title = $row['ann_title'];
            $get_content = $row['ann_content'];
            $get_ann_date = $row['ann_add_date'];
            echo "<div class='list-annc-items'>";
            echo "<p class='annc-club app-details'>Announcer : " . $get_club . "</p>";
            echo "<p class='annc-title app-details'>Title : " . $get_title . "</p>";
            echo "<p class='annc-content app-details'>Description : " . $get_content . "</p>";echo "<p class='annc-add-date app-details'>Date : " . $get_ann_date . "</p>";
            echo "<button class='delete-button app-button' onclick=\"window.location.href='/URCRS/hep/delete.php?data=annc&id=".$get_id."'\">Delete</button>";
            echo "</div>";
          }
        }
    } else {
        echo "<p class='item-empty-text'>No announcement found!</p>";
    }

    ?>
    </div>
    <!-- LIST ALL EVENTS -->
    <div id="event" style="display: none;">
    <?php
    $sql_get_event = "SELECT * FROM events ORDER BY event_date DESC";
    $result_get_event = mysqli_query($conn, $sql_get_event);
 
    if ($result_get_event && mysqli_num_rows($result_get_event) > 0) {
      while ($row = mysqli_fetch_assoc($result_get_event)) {
        $club_id = $row['club_id'];
        // get club id
        $sql_get_club = "SELECT club_name FROM clubs WHERE club_id='$club_id'";
        $result_get_club = $conn->query($sql_get_club);
        if ($result_get_club->num_rows > 0) {
          $row_club = $result_get_club->fetch_assoc();
          $get_club = $row_club['club_name'];
          $get_id = $row['event_id'];
          $get_name = $row['event_name'];
          $get_desc = $row['event_desc'];
          $get_date = $row['event_date'];
          echo "<div class='list-event-items'>";
          echo "<p class='event-club app-details'>Announcer : " . $get_club . "</p>";
          echo "<p class='event-name app-details'>Title : " . $get_name . "</p>";
          echo "<p class='event-desc app-details'>Description : " . $get_desc . "</p>";echo "<p class='event-date app-details'>Date : " . $get_date . "</p>";
          echo "<button class='delete-button app-button' onclick=\"window.location.href='/URCRS/hep/delete.php?data=event&id=".$get_id."'\">Delete</button>";
          echo "</div>";
        }
      }
    } else {
        echo "<p class='item-empty-text'>No announcement found!</p>";
    }
    ?>
    </div>
   </div>
 </section>
 <!-- OTHERS -->
 <div id="modal-container">
  <div id="modal-add" class="modal">
    <div class="modal-content">
      <span class="close-add close">Close &times;</span>
      <p class="modal-header">+ ADD NEW CLUB</p>
      <form action="add_club.php" method="post">
          <p>Club Name :</p>
          <input type="text" id="add-club-name" name="add-club-name" required><br>
          <p>Club Email :</p>
          <input type="text" id="add-club-email" name="add-club-email" required><br>
          <p>Password :</p>
          <input type="password" id="add-club-pass" name="add-club-pass" required><br>
          <p>Club Phone :</p>
          <input type="text" id="add-club-phone" name="add-club-phone" required><br>
          <p>Club Category: </p>
          <select name="add-club-category" id="add-club-type" required>
            <option value="1">Language</option>
            <option value="2">Academic</option>
            <option value="3">Science</option>
            <option value="4">Sports</option>
            <option value="5">Photography</option>
            <option value="6">Tech</option>
            <option value="7">Artistics</option>
            <option value="Others">Others</option>
          </select><br>
          <p>Club Mission (Optional): </p>
          <input type="text" id="add-club-mission" name="add-club-mission"><br>
          <p>Club Vision (Optional): </p>
          <input type="text" id="add-club-vision" name="add-club-vision"><br>
          <input class="submit-button" type="submit">
      </form>
    </div>

  </div>

  </div>
 </div>
 <script type="text/javascript">
  function display(tab_type) {
   var displayBlock = "background-color: white; color: #ac5180;";
   var displayNone = "background-color: #ac5180; color: white;";
   if (tab_type == "Announcement") {
    document.getElementById("annc-button").style.cssText = displayBlock;
    document.getElementById("announcement").style.display = "block";
    document.getElementById("event-button").style.cssText = displayNone;
    document.getElementById("event").style.display = "none";
    document.getElementById("club-list-button").style.cssText = displayNone;
    document.getElementById("club").style.display = "none";
    document.getElementById("student-button").style.cssText = displayNone;
    document.getElementById("student").style.display = "none";
  } else if (tab_type == "Event") {
    document.getElementById("annc-button").style.cssText = displayNone;
    document.getElementById("announcement").style.display = "none";
    document.getElementById("event-button").style.cssText = displayBlock;
    document.getElementById("event").style.display = "block";
    document.getElementById("club-list-button").style.cssText = displayNone;
    document.getElementById("club").style.display = "none";
    document.getElementById("student-button").style.cssText = displayNone;
    document.getElementById("student").style.display = "none";
  } else if (tab_type == "Club") {
    document.getElementById("annc-button").style.cssText = displayNone;
    document.getElementById("announcement").style.display = "none";
    document.getElementById("event-button").style.cssText = displayNone;
    document.getElementById("event").style.display = "none";
    document.getElementById("club-list-button").style.cssText = displayBlock;
    document.getElementById("club").style.display = "block";
    document.getElementById("student-button").style.cssText = displayNone;
    document.getElementById("student").style.display = "none";
  } else if (tab_type == "Student") {
    document.getElementById("annc-button").style.cssText = displayNone;
    document.getElementById("announcement").style.display = "none";
    document.getElementById("event-button").style.cssText = displayNone;
    document.getElementById("event").style.display = "none";
    document.getElementById("club-list-button").style.cssText = displayNone;
    document.getElementById("club").style.display = "none";
    document.getElementById("student-button").style.cssText = displayBlock;
    document.getElementById("student").style.display = "block";
  }
  }

  // modal for ADD
  function displayModal(modalName) {
    // Get the button that opens the modal
    var btn = document.getElementById(modalName);
    // Get the <span> element that closes the modal
    var span = "";
    var modal = "";
    if (modalName == "Add") {
      modal = document.getElementById("modal-add");
      span = document.getElementsByClassName("close-add")[0];
    }
    else {
      modal = document.getElementById("modal-edit");
      span = document.getElementsByClassName("close-edit")[0];
    }

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

  // display add type
  function display_add_option(add_type) {
    if (add_type == "annc") {
      document.getElementById("modal_add_annc").style.display = "block";
      document.getElementById("modal_add_event").style.display = "none";
    } else {
      document.getElementById("modal_add_annc").style.display = "none";
      document.getElementById("modal_add_event").style.display = "block";
    }
  }

  function showAnnouncementDetails(annId, annTitle, annContent, annDate) {
            document.getElementById('announcement-title').innerText = annTitle;
            document.getElementById('announcement-content').innerText = annContent;
            document.getElementById('announcement-date').innerText = annDate;

            var modal = document.getElementById('modal-announcement-details');
            modal.style.display = "block";

            // Button click event to delete announcement
            document.getElementById('delete-announcement').onclick = function() {
                if (confirm('Are you sure you want to delete this announcement?')) {
                    window.location.href = 'delete_announcement.php?ann_id=' + annId;
                }
            };

            var span = modal.querySelector(".close");
        span.onclick = function() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

            // Previous span.onclick and window.onclick event handlers...
        }

        // Function to show event details
        function showEventDetails(eventId, eventTitle, eventContent, eventDate) {
            document.getElementById('event-name').innerText = eventTitle;
            document.getElementById('event-desc').innerText = eventContent;
            document.getElementById('event-date').innerText = eventDate;

            var modal = document.getElementById('modal-event-details');
            modal.style.display = "block";

            // Button click event to delete event
            document.getElementById('delete-event').onclick = function() {
                if (confirm('Are you sure you want to delete this event?')) {
                    window.location.href = 'delete_event.php?event_id=' + eventId;
                }
            };
            var span = modal.querySelector(".close");
        span.onclick = function() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
            // Previous span.onclick and window.onclick event handlers...
        }
function print() {
  console.log("Clicked");
}

 </script>
</body>

</html>