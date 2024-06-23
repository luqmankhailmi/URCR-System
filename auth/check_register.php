<?php

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uitmclubhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Connection Error : ".$conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$new_name = $_POST["input_name"];
	$new_email = $_POST["input_email"];
	$new_pass = $_POST["input_pass"];
	$new_pass_confirm = $_POST["input_pass_confirm"];
	$new_matric = $_POST['input_matric'];
	$new_semester = $_POST['input_semester'];
	$new_prog_code = $_POST['input_prog_code'];
	$sql_all =  "SELECT * FROM users";
	$result_all = $conn->query($sql_all);
	$email_exist = "False";
	$password_match = "True";

	while ($row = $result_all->fetch_assoc()) {
		if ($new_email == $row["user_email"]) {
			$email_exist = "True";
		}
	}
	if ($new_pass != $new_pass_confirm) {
		$password_match = "False";
	}
	if ($email_exist == "True") {
		header("Location: /URCRS/register.php?error=invalid_credentials");
    		exit();
	} else if ($password_match == "False") {
		header("Location: /URCRS/register.php?error=invalid_credentials");
    		exit();
	} else {
		$reg_date = date("d-m-Y");
		$sql_insert = "INSERT INTO users (user_name,user_email,user_pass,user_type) VALUE ('$new_name', '$new_email', '$new_pass', 'Student')";
		$sql_insert_reg = "INSERT INTO registration (reg_name, reg_email, reg_date) VALUES ('$new_name','$new_email','$reg_date')";
		if ($conn->query($sql_insert) == TRUE && $conn->query($sql_insert_reg) == TRUE) {
			// now input to the student_details table
			// get the new generated user_id first
			$sql = "SELECT user_id FROM users WHERE user_email='$new_email'";
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			$user_id = $row['user_id'];
			echo "User id : ".$user_id;
			$sql = "INSERT INTO student_details (stud_matric, stud_semester, stud_prog_code, user_id) VALUES ('$new_matric','$new_semester','$new_prog_code','$user_id');";
			$result = $conn -> query($sql);

			// need to be fixed
			echo "<html><script>alert('Registered successfully! You can log in now.');</script></html>";
			header("Location: /URCRS/login.php");
   exit();
		} else {
			header("Location: /URCRS/register.php?error=invalid_credentials");
    			exit();
		}
	}
}
$conn->close();
?>
	