<?php

session_start();
$logged = TRUE;

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $logged = FALSE;
    header("Location: login.php");
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
$club_id = $_SESSION['user_id'];

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
 <link rel="stylesheet" href="/URCRS/css/club_manage.css">
 <link rel="icon" type="image/x-icon" href="/URCRS/images/C.png">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <title>Club Manage</title>
</head>

<body>
 <!-- MAIN NAVIGATION BAR -->
 <section id="navbar">
  <image id="logo" src="/URCRS/images/C.png" />
  <p id="title">URCRS</p>
  <div id="menu">
    <p class="menu-item"><a class="menu-link" href="/URCRS/club_admin/club_manage.php" style="color: #ac5180;">Dashboard</a></p>
   <p class="menu-item"><a class="menu-link" href="/URCRS/explore.php">Explore</a></p>
   <p class='menu-item'><a id='login' href='/URCRS/auth/logout.php'>Log Out</a></p>
  </div>
 </section>
 <!-- CLUB CONTAINER -->
 <section id="content-container">
   <!-- LEFT SIDE (CLUB NAVIGATION BAR) -->
   <div id="content-nav">
    <button id="club-profile-button" class="content-nav-button" onclick="display('Club-Profile')" style="background-color: white; color: #ac5180;">Club Profile</button>
    <button id="annc-button" class="content-nav-button" onclick="display('Announcement')">Announcement</button>
    <button id="event-button" class="content-nav-button" onclick="display('Event')">Event</button>
    <button class="content-nav-button" onclick="display('Member')" id="member-button">Member List</button>
    <button class="content-nav-button" onclick="display('Applicant')" id="applicant-button">Applicant List</button>
    <button class="content-nav-button" onclick="displayModal('Add')" id="Add">+ Add</button>
    <button class="content-nav-button" onclick="displayModal('Edit')" id="Edit">Edit Profile</button>
    <!-- <button id="logout-button" class="content-nav-button" onclick="window.location.href='/URCRS/auth/logout.php';">Log Out</button> -->
   </div>
   <!-- LEFT SIDE (CLUB NAVIGATION BAR) -->
   <div id="content-data">
    <div id="club-profile">
      <!-- UPPER SIDE -->
      <div id="profile-upper">
        <?php
          $query = "SELECT club_mission, club_vision, club_email, club_phone FROM clubs WHERE club_id = '$club_id'";
          $result = mysqli_query($conn, $query);
          if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $club_mission = $row['club_mission'];
            $club_vision = $row['club_vision'];
            $club_email = $row['club_email'];
            $club_phone = $row['club_phone'];
          } else {
              echo "Data is not found!";
          }
        ?>
        <image id="club-pfp" src="/URCRS/images/group.jpg"/>
        <h1 id="club-header"><?php echo $user_name; ?></h1>
        <button id="contact-button" onclick="location.href='mailto:<?php echo $club_email; ?>';">Contact</button>
      </div>
      <!-- LOWER SIDE -->
      <div id="profile-lower">
        <div id="mission-vision">
        <?php
          echo "<p class='mv-title'>Mission</p><br><p class='club-about'>".$club_mission."</p>";
          echo "<p class='mv-title'>Vision</p><br><p class='club-about'>".$club_vision."</p>";
          echo "<p class='mv-title'>Contact</p><br><p class='club-about'>Email: ".$club_email."<br>Phone: ".$club_phone."</p>";
        ?>
        </div>
      </div>
    </div>
    <!-- LIST ALL ANNOUNCEMENTS -->
    <div id="announcement" style="display: none;">
    <?php
    // Assuming $username contains the username of the logged-in user
    $query = "SELECT club_id FROM clubs WHERE club_name = '$user_name'";
    $result = mysqli_query($conn, $query);
 
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $club_id = $row['club_id'];
    } else {
        // Handle error or display message if user not found
    }

    $announcements = array(); // Array to store announcements

    $query = "SELECT ann_id, ann_title, ann_content, ann_add_date FROM announcement WHERE club_id = $club_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $announcements[$club_id][] = $row;
        }
    } else {
        echo "<p class='item-empty-text'>No announcement found!</p>";
    }

    foreach ($announcements as $club_id => $club_announcements) {
        foreach ($club_announcements as $announcement) {
          $ann_id = $announcement['ann_id'];
          $ann_title = $announcement['ann_title'];
          $ann_content = $announcement['ann_content'];
          $ann_date = $announcement['ann_add_date'];
            echo "<button class='annc-items' onclick=\"showAnnouncementDetails(" . $ann_id . ", '" . $ann_title . "', '" . $ann_content . "', '" . $ann_date."')\">
                    <p class='annc-title'>".$ann_title."</p><p class='annc-content'>".$ann_content."</p><p class='annc-date'>".$ann_date."</button><br>";
        }
    }
    ?>
    </div>
    <!-- LIST ALL EVENTS -->
    <div id="event" style="display: none;">
    <?php
    // Assuming $username contains the username of the logged-in user
    $query = "SELECT club_id FROM clubs WHERE club_name = '$user_name'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $club_id = $row['club_id'];
    } else {
        // Handle error or display message if user not found
    }

    $events = array(); // Array to store events

    $query = "SELECT event_id, event_name, event_desc, event_date FROM events WHERE club_id = $club_id";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $events[$club_id][] = $row; // Store event details
            }
        } else {
            echo "<p class='item-empty-text'>No event found!</p>";
        }
    foreach ($events as $club_id => $club_events) {
      foreach ($club_events as $event) {
          $event_id = $event['event_id'];
          $event_name = htmlspecialchars($event['event_name'], ENT_QUOTES);
          $event_desc = htmlspecialchars($event['event_desc'], ENT_QUOTES);
          $event_date = $event['event_date'];
          echo "<button class='event-items' onclick=\"showEventDetails('$event_id','$event_name','$event_desc','$event_date')\"><p class='event-name'>".$event_name."</p><p class='event-desc'>".$event_desc."</p><p class='event-date'>Event Date: ".$event_date."</p></button>";
      }
    }

    ?>
    </div>
    <!-- LIST ALL CLUB MEMBERS -->
    <div id="member" style="display: none;">
      <?php
        // Retrieve members from the user_club table
        $query = "SELECT u.user_name, u.user_email, u.user_id 
                  FROM user_club uc 
                  JOIN users u ON uc.user_id = u.user_id 
                  WHERE uc.club_id = $club_id";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='member-items'>";
                echo "<p class='member-name member-details'><b>Name:</b> " . $row['user_name'] . "</p>";
                echo "<p class='member-email member-details'><b>Email:</b> " . $row['user_email'] . "</p>";
                echo "<button class='contact-button app-button' onclick=\"location.href='mailto:".$row['user_email']."'\">Contact</button>";
                echo "<button class='view-button app-button' onclick=\"window.location.href='/URCRS/public_profile.php?id=".$row['user_id']."'\">View Profile</button>";
                echo "<button class='delete-button app-button' onclick=\"window.location.href='/URCRS/club_admin/remove_member.php?id=".$row['user_id']."'\">Remove</button></div>";
            }
        } else {
            echo "<p class='item-empty-text'>No members found for this club.</p>";
        }
        ?>
    </div>
    <!-- LIST ALL APPLICANT -->
    <div id="applicant" style="display: none;">
      <?php
          // Retrieve applicants from the applicant table
          $query = "SELECT a.applicant_id, u.user_name, a.user_id, a.club_id, u.user_email, a.app_status 
                    FROM applicant a 
                    JOIN users u ON a.user_id = u.user_id 
                    WHERE a.club_id = '$club_id' ORDER BY a.applicant_id DESC";
          $result = mysqli_query($conn, $query);

          if ($result && mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<div class='applicant-items'>";
                  echo "<p class='applicant-name app-details'>Name: " . $row['user_name'] . "</p>";
                  echo "<p class='applicant-email app-details'>Email: " . $row['user_email'] . "</p>";
                  echo "<p class='applicant-status app-details'>Status: " . $row['app_status'] . "</p>";
                  
                  if ($row['app_status'] == "Pending") {
                    // Approve Button
                    echo "<button class='approve-button app-button' onclick=\"window.location.href='/URCRS/club_admin/process_applicant.php?id=".$row['user_id']."&value=approve&club=".$row['club_id']."'\">Approve</button>";
                    
                    // Reject Button
                    echo "<button class='reject-button app-button' onclick=\"window.location.href='/URCRS/club_admin/process_applicant.php?id=".$row['user_id']."&value=reject&club=".$row['club_id']."'\">Reject</button>";
                  }
                  echo "<button class='view-button app-button' onclick=\"window.location.href='/URCRS/public_profile.php?id=".$row['user_id']."'\">View Profile</button>";

                  echo "</div>";
              }
          } else {
              echo "<p class='item-empty-text'>No applicants found for this club.</p>";
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
      <p class="modal-header">+ ADD</p>
      <p style="padding: 0 50px 0 50px;">Choose Type:</p>
      <form action="club_admin_add.php" method="post">
        <input type="radio" id="radio-annc" name="add_type" value="Announcement" onchange="display_add_option('annc')">
        <label for="radio-annc">Announcement</label><br>
        <input type="radio" id="radio-event" name="add_type" value="Event" onchange="display_add_option('event')">
        <label for="radio-event">Event</label>
        <div id="modal_add_annc" style="display: none">
          <p>Announcement Title :</p>
          <input type="text" id="add-annc-title" name="add-annc-title"><br>
          <p>Description :</p>
          <input type="text" id="add-description" name="add-description"><br>
          <input class="submit-button" type="submit">
        </div>
        <div id="modal_add_event" style="display: none">
          <p>Event Title :</p>
          <input type="text" id="add-event-title" name="add-event-title"><br>
          <p>Description :</p>
          <input type="text" id="add-event-desc" name="add-event-desc"><br>
          <p>Date: </p>
          <input type="date" id="add-event-date" name="add-event-date"><br>
          <input class="submit-button" type="submit">
        </div> 
      </form>
    </div>

  </div>
  <div id="modal-edit" class="modal">
    <div class="modal-content">
      <span class="close-edit close">Close &times;</span>
      <p class="modal-header">EDIT CLUB DETAILS</p>
      <form action="edit_club_details.php" method="post">
      <?php
        $query = "SELECT club_mission, club_vision, club_email, club_phone FROM clubs WHERE club_name = '$user_name'";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          $club_mission = $row['club_mission'];
          $club_vision = $row['club_vision'];
          $club_email = $row['club_email'];
          $club_phone = $row['club_phone'];
          echo "<p>Current Mission: </p><p>".$club_mission."</p><p>New Mission: </p>";
          echo "<input type='text' id='new-mission' name='new-mission'><hr>";
          echo "<p>Current Vision: </p><p>".$club_vision."</p><p>New Vision: </p>";
          echo "<input type='text' id='new-vision' name='new-vision'><hr>";
          echo "<p>Current Email: </p><p>".$club_email."</p><p>New Email: </p>";
          echo "<input type='text' id='new-email' name='new-email'><hr>";
          echo "<p>Current Phone: </p><p>".$club_phone."</p><p>New Phone: </p>";
          echo "<input type='text' id='new-phone' name='new-phone'><br>";
          echo "<input class='submit-button' type='submit'>";
        }
      ?>
      </form>
    </div>

  </div>
<!-- Modal for Announcement Details -->
    <div id="modal-announcement-details" class="modal">
        <div class="modal-content">
            <span class="close-details close">Close &times;</span>
            <h2 id="m-announcement-title"></h2>
            <p id="m-announcement-content"></p>
            <p id="m-announcement-date"></p>
            <!-- Button to delete announcement -->
            <button id="m-delete-announcement" class="modal-button">Delete</button>
        </div>
    </div>

    <!-- Modal for Event Details -->
    <div id="modal-event-details" class="modal">
        <div class="modal-content">
            <span class="close-event-details close">Close &times;</span>
            <h2 id="m-event-name"></h2>
            <p id="m-event-desc"></p>
            <p id="m-event-date"></p>
            <!-- Button to delete event -->
            <button id="m-delete-event" class="modal-button">Delete</button>
        </div>
    </div>

</div>
 </div>
 <script type="text/javascript">
  function display(tab_type) {
   var displayBlock = "background-color: white; color: #ac5180;";
   var displayNone = "background-color: #ac5180; color: white;";
   if (tab_type == "Announcement") {
    document.getElementById("club-profile-button").style.cssText = displayNone;
    document.getElementById("club-profile").style.display = "none";
    document.getElementById("annc-button").style.cssText = displayBlock;
    document.getElementById("announcement").style.display = "block";
    document.getElementById("event-button").style.cssText = displayNone;
    document.getElementById("event").style.display = "none";
    document.getElementById("member-button").style.cssText = displayNone;
    document.getElementById("member").style.display = "none";
    document.getElementById("applicant-button").style.cssText = displayNone;
    document.getElementById("applicant").style.display = "none";
  } else if (tab_type == "Event") {
    document.getElementById("club-profile-button").style.cssText = displayNone;
    document.getElementById("club-profile").style.display = "none";
    document.getElementById("annc-button").style.cssText = displayNone;
    document.getElementById("announcement").style.display = "none";
    document.getElementById("event-button").style.cssText = displayBlock;
    document.getElementById("event").style.display = "block";
    document.getElementById("member-button").style.cssText = displayNone;
    document.getElementById("member").style.display = "none";
    document.getElementById("applicant-button").style.cssText = displayNone;
    document.getElementById("applicant").style.display = "none";
  } else if (tab_type == "Member") {
    document.getElementById("club-profile-button").style.cssText = displayNone;
    document.getElementById("club-profile").style.display = "none";
    document.getElementById("annc-button").style.cssText = displayNone;
    document.getElementById("announcement").style.display = "none";
    document.getElementById("event-button").style.cssText = displayNone;
    document.getElementById("event").style.display = "none";
    document.getElementById("member-button").style.cssText = displayBlock;
    document.getElementById("member").style.display = "block";
    document.getElementById("applicant-button").style.cssText = displayNone;
    document.getElementById("applicant").style.display = "none";
  } else if (tab_type == "Applicant") {
    document.getElementById("club-profile-button").style.cssText = displayNone;
    document.getElementById("club-profile").style.display = "none";
    document.getElementById("annc-button").style.cssText = displayNone;
    document.getElementById("announcement").style.display = "none";
    document.getElementById("event-button").style.cssText = displayNone;
    document.getElementById("event").style.display = "none";
    document.getElementById("member-button").style.cssText = displayNone;
    document.getElementById("member").style.display = "none";
    document.getElementById("applicant-button").style.cssText = displayBlock;
    document.getElementById("applicant").style.display = "block";
  } else {
    document.getElementById("club-profile-button").style.cssText = displayBlock;
    document.getElementById("club-profile").style.display = "block";
    document.getElementById("annc-button").style.cssText = displayNone;
    document.getElementById("announcement").style.display = "none";
    document.getElementById("event-button").style.cssText = displayNone;
    document.getElementById("event").style.display = "none";
    document.getElementById("member-button").style.cssText = displayNone;
    document.getElementById("member").style.display = "none";
    document.getElementById("applicant-button").style.cssText = displayNone;
    document.getElementById("applicant").style.display = "none";
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
            document.getElementById('m-announcement-title').innerText = annTitle;
            document.getElementById('m-announcement-content').innerText = annContent;
            document.getElementById('m-announcement-date').innerText = annDate;

            var modal = document.getElementById('modal-announcement-details');
            modal.style.display = "block";

            // Button click event to delete announcement
            document.getElementById('m-delete-announcement').onclick = function() {
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
            document.getElementById('m-event-name').innerText = eventTitle;
            document.getElementById('m-event-desc').innerText = eventContent;
            document.getElementById('m-event-date').innerText = eventDate;

            var modal = document.getElementById('modal-event-details');
            modal.style.display = "block";

            // Button click event to delete event
            document.getElementById('m-delete-event').onclick = function() {
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