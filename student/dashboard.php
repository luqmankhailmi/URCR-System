<!-- 
PAGE NAME: Dashboard
USER_TYPE: Student
-->

<?php
// SESSION CHECKER
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uitmclubhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$logged = TRUE;
$user_name = $_SESSION["user_name"];

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $logged = FALSE;
    header("Location: /URCRS/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/URCRS/css/dashboard.css">
    <link rel="icon" type="image/x-icon" href="/URCRS/images/C.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>URCRS - Dashboard</title>
</head>
<body>
    <!-- MAIN NAVIGATION -->
    <section id="navbar">
        <img id="logo" src="/URCRS/images/C.png" alt="Logo"/>
        <p id="title">URCRS</p>
        <div id="menu">
            <p class="menu-item"><a class="menu-link" href="/URCRS/student/dashboard.php" style="color: #ac5180;">Dashboard</a></p>
            <p class="menu-item"><a class="menu-link" href="/URCRS/explore.php">Explore</a></p>
            <p class="menu-item"><a class="menu-link" href="/URCRS/student/help.php">Help</a></p>
            <p class="menu-item"><a id="login" class="menu-link" href="/URCRS/student/user_profile.php">My Profile</a></p>
        </div>
    </section>
    <!-- CONTENT CONTAINER -->
    <section id="content-container">
        <div id="content-nav">
            <button id="clublist-button" class="content-nav-button" onclick="display('ClubList')" style="background-color: white; color: #ac5180;">My Club</button><br>
            <button id="announcement-button" class="content-nav-button" onclick="display('Announcement')">Announcement</button><br>
            <button id="event-button" class="content-nav-button" onclick="display('Event')">Event</button><br>
            <button id="myapp-button" class="content-nav-button" onclick="display('MyApp')">My Application</button><br>
            <button id="logout-button" class="content-nav-button" onclick="window.location.href='/URCRS/auth/logout.php';">Log Out</button>
        </div>
        <div id="content-data">
            <section id="tab-content">
                <!-- LIST ALL JOINED CLUB -->
                <div id="my-club">
                    <?php
                    $query = "SELECT user_id FROM users WHERE user_name = '$user_name'";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $user_id = $row['user_id'];
                    } else {
                        // do nothing
                    }

                    $query = "SELECT club_id FROM user_club WHERE user_id = $user_id";
                    $result = mysqli_query($conn, $query);

                    $club_ids = array(); // Array to store club IDs

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $club_ids[] = $row['club_id'];
                        }
                    } else {
                        echo "<p id='club-empty-text'>No clubs registered!</p>";
                    }

                    $club_info = array(); // Array to store club information

                    foreach ($club_ids as $club_id) {
                        $query = "SELECT club_name FROM clubs WHERE club_id = $club_id";
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $club_info[] = $row['club_name'];
                        } else {
                            // do nothing
                        }
                    }

                    foreach ($club_info as $club) {
                        echo "<button class='club-items' onclick=\"window.location.href='/URCRS/club_profile.php?id=" . urlencode($club) . "';\"><img class='club-image' src='/URCRS/images/group.jpg' alt='Club Image'><p class='club-name'>" . htmlspecialchars($club) . "</p></button>";
                    }
                    ?>
                </div>
                <!-- LIST ALL ANNOUNCEMENT FROM JOINED CLUB -->
                <div id="announcement" style="display: none;">
                    <?php
                    $query = "SELECT user_id FROM users WHERE user_name = '$user_name'";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $user_id = $row['user_id'];
                    } else {
                        // do nothing
                    }

                    $query = "SELECT club_id FROM user_club WHERE user_id = $user_id";
                    $result = mysqli_query($conn, $query);

                    $club_ids = array(); // Array to store club IDs

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $club_ids[] = $row['club_id'];
                        }
                        $announcements = array(); // Array to store announcements

                        foreach ($club_ids as $club_id) {
                            $query = "SELECT ann_id, ann_title, ann_content, ann_add_date, club_id FROM announcement WHERE club_id = '$club_id' ORDER BY ann_id DESC";
                            $result = mysqli_query($conn, $query);

                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $announcements[$club_id][] = $row;
                                }
                            }
                        }

                        if (!empty($announcements)) {
                            foreach ($announcements as $club_announcements) {
                                foreach ($club_announcements as $announcement) {
                                    $club_id = $announcement['club_id'];
                                    // get club name
                                    $get_club_name = $conn->query("SELECT club_name FROM clubs WHERE club_id='$club_id'");
                                    $club_name_row = $get_club_name->fetch_assoc();
                                    $club_name = $club_name_row['club_name'];
                                    $ann_id = $announcement['ann_id'];
                                    $ann_title = $announcement['ann_title'];
                                    $ann_content = $announcement['ann_content'];
                                    $ann_date = $announcement['ann_add_date'];
                                    echo "<button class='annc-items' onclick=\"showAnnouncementDetails('".$ann_title."', '".addslashes($ann_content)."', '".addslashes($ann_date)."')\">
                                            <div class='annc-header'><p class='annc-club'>".$club_name."</p><p class='annc-date'>".$ann_date."</p></div><p class='annc-title'>".$ann_title."</p><p class='annc-content'>".$ann_content."</p></button><br>";
                                }
                            }
                        } else {
                            echo "<p class='annc-empty-text'>No announcement found!</p>";
                        }
                    } else {
                        echo "<p class='annc-empty-text'>No clubs registered!</p>";
                    }
                    ?>
                </div>
                <!-- LIST ALL EVENT FROM JOINED CLUB -->
                <div id="event" style="display: none;">
                    <?php
                    $query = "SELECT user_id FROM users WHERE user_name = '" . mysqli_real_escape_string($conn, $user_name) . "'";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $user_id = $row['user_id'];
                    } else {
                        // do nothing?
                    }

                    $query = "SELECT club_id FROM user_club WHERE user_id = " . intval($user_id);
                    $result = mysqli_query($conn, $query);

                    $club_ids = array(); // Array to store club IDs

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $club_ids[] = $row['club_id'];
                        }
                        $events = array(); // Array to store events

                        foreach ($club_ids as $club_id) {
                            $query = "SELECT club_id, event_id, event_name, event_desc, event_date FROM events WHERE club_id = " . intval($club_id);
                            $result = mysqli_query($conn, $query);

                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $events[$club_id][] = $row;
                                }
                            }
                        }

                        if (!empty($events)) {
                            foreach ($events as $club_events) {
                                foreach ($club_events as $event) {
                                    // get club name based on club id
                                    $event_club = $event['club_id'];
                                    $result_club = $conn->query("SELECT club_name FROM clubs WHERE club_id='$event_club'");
                                    $row_club = $result_club->fetch_assoc();
                                    $club_name = $row_club['club_name'];
                                    // continue
                                    $event_id = $event['event_name'];
                                    $event_name = $event['event_name'];
                                    $event_desc = $event['event_desc'];
                                    $event_date = $event['event_date'];
                                    echo "<button class='event-items' onclick=\"showEventDetails('" .addslashes($event_name) . "', '" . addslashes($event_desc) ."', '" . addslashes($event_date) . "')\">
                                            <p class='event-club'>".$club_name."</p><p class='event-name'>Event Name: " . $event_name . "</p><p class='event-desc'>".$event_desc."</p><p class='event-date'>Event Date: ".$event_date."</p>
                                        </button><br>";
                                }
                            }
                        } else {
                            echo "<p class='annc-empty-text'>No event found!</p>";
                        }
                    } else {
                        echo "<p class='annc-empty-text'>No clubs registered!</p>";
                    }
                    ?>
                </div>
                <!-- LIST ALL USER'S APPLICATION TO JOIN CLUB -->
                <div id="myapp" style="display: none;">
                    <?php
                        $query = "SELECT user_id FROM users WHERE user_name = '" . mysqli_real_escape_string($conn, $user_name) . "'";
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $user_id = $row['user_id'];
                        } else {
                            // User not found
                            echo "<p class='annc-empty-text'>User not found!</p>";
                            exit();
                        }

                        // Fetch clubs the user has applied to and their application status
                        $query = "
                            SELECT clubs.club_name, applicant.app_status 
                            FROM applicant 
                            JOIN clubs ON applicant.club_id = clubs.club_id 
                            WHERE applicant.user_id = " . intval($user_id)." ORDER BY applicant_id DESC";
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $club_name = $row['club_name'];
                                $app_status = $row['app_status'];
                                echo "<button class='applicant-items'>
                                        <p class='club-app-name'>" . htmlspecialchars($club_name) . "</p>
                                        <p class='app-status'>Status: " . htmlspecialchars($app_status) . "</p>
                                    </button><br>";
                            }
                        } else {
                            echo "<p class='annc-empty-text'>No applications found!</p>";
                        }
                    ?>
                </div>
            </section>
        </div>
    </section>
    <!-- MODAL CONTAINER -->
    <div id="modal-container">
        <!-- MODAL FOR ANNOUNCEMENT DETAILS -->
        <div id="modal-announcement-details" class="modal">
            <div class="modal-content">
                <span class="close-details close">Close &times;</span>
                <h2 id="announcement-title"></h2>
                <p id="announcement-content"></p>
                <p id="announcement-date"></p>
            </div>
        </div>

        <!-- MODAL FOR EVENT DETAILS -->
        <div id="modal-event-details" class="modal">
            <div class="modal-content">
                <span class="close-event-details close">Close &times;</span>
                <h2 id="event-title"></h2>
                <p id="event-content"></p>
                <p id="event-date"></p>
            </div>
        </div>
    </div>
    <!-- JAVASCRIPT -->
    <script type="text/javascript">
        // DISPLAY DATA BASED ON BUTTON CLICKED
        function display(tab_type) {
            var displayBlock = "background-color: white; color: #ac5180; outline: 1px solid #ac5180;";
            var displayNone = "background-color: #ac5180; color: white;"
            if (tab_type == "ClubList") {
                document.getElementById("clublist-button").style.cssText = displayBlock;
                document.getElementById("my-club").style.display = "flex";
                document.getElementById("announcement-button").style.cssText = displayNone;
                document.getElementById("announcement").style.display = "none";
                document.getElementById("event-button").style.cssText = displayNone;
                document.getElementById("event").style.display = "none";
                document.getElementById("myapp-button").style.cssText = displayNone;
                document.getElementById("myapp").style.display = "none";
            }
            else if (tab_type == "Announcement") {
                document.getElementById("clublist-button").style.cssText = displayNone;
                document.getElementById("my-club").style.display = "none";
                document.getElementById("announcement-button").style.cssText = displayBlock;
                document.getElementById("announcement").style.display = "block";
                document.getElementById("event-button").style.cssText = displayNone;
                document.getElementById("event").style.display = "none";
                document.getElementById("myapp-button").style.cssText = displayNone;
                document.getElementById("myapp").style.display = "none";
            }
            else if (tab_type == "Event") {
                document.getElementById("clublist-button").style.cssText = displayNone;
                document.getElementById("my-club").style.display = "none";
                document.getElementById("announcement-button").style.cssText = displayNone;
                document.getElementById("announcement").style.display = "none";
                document.getElementById("event-button").style.cssText = displayBlock;
                document.getElementById("event").style.display = "block";
                document.getElementById("myapp-button").style.cssText = displayNone;
                document.getElementById("myapp").style.display = "none";
            }
            else {
                document.getElementById("clublist-button").style.cssText = displayNone;
                document.getElementById("my-club").style.display = "none";
                document.getElementById("announcement-button").style.cssText = displayNone;
                document.getElementById("announcement").style.display = "none";
                document.getElementById("event-button").style.cssText = displayNone;
                document.getElementById("event").style.display = "none";
                document.getElementById("myapp-button").style.cssText = displayBlock;
                document.getElementById("myapp").style.display = "block";
            }
        }

        // MODAL DISPLAYER (ANNOUNCEMENT)
        function showAnnouncementDetails(annTitle, annContent, annDate) {
            document.getElementById('announcement-title').innerText = annTitle;
            document.getElementById('announcement-content').innerText = annContent;
            document.getElementById('announcement-date').innerText = annDate;

            var modal = document.getElementById('modal-announcement-details');
            modal.style.display = "block";

            var span = modal.querySelector(".close");
            span.onclick = function() {
                modal.style.display = "none";
            }
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }

        // MODAL DISPLAYER (EVENT)
        function showEventDetails(eventTitle, eventContent, eventDate) {
            document.getElementById('event-title').innerText = eventTitle;
            document.getElementById('event-content').innerText = eventContent;
            document.getElementById('event-date').innerText = eventDate;

            var modal = document.getElementById('modal-event-details');
            modal.style.display = "block";

            var span = modal.querySelector(".close");
            span.onclick = function() {
                modal.style.display = "none";
            }
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
