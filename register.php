<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="/URCRS/css/register.css">
	<link rel="icon" type="image/x-icon" href="images/C.png">
	<title>Register Test</title>
</head>
<body>
	<div id="register-container">
		<h1 id="register-title">Register</h1>
		<div id="form-container">
		<form action="/URCRS/auth/check_register.php" method="post">
			<p class="register-items">Username :</p>
			<input class="input-bar" type="text" name="input_name" placeholder="e.g. John Doe" required>
			<p class="register-items">Email address :</p>
			<input class="input-bar" type="email" name="input_email" placeholder="example@domain.com" required>
			<p class="register-items">Password :</p>
			<input class="input-bar" type="password" name="input_pass" placeholder="Must have at least 6 characters" required><br>
			<p class="register-items">Confirm password :</p>
			<input class="input-bar" type="password" name="input_pass_confirm" placeholder="Re-enter password" required><br>
			<p class="register-items">Matric number :</p>
			<input class="input-bar" type="text" name="input_matric" placeholder="Enter matric number..." required><br>
			<p class="register-items">Current semester :</p>
			<input class="input-bar" type="number" name="input_semester" placeholder="e.g. 1-5" required><br>
			<p class="register-items">Programme code :</p>
			<input class="input-bar" type="text" name="input_prog_code" placeholder="e.g. CDCS110" required><br>
			<button id="register-button" type="submit">Register Now</button>
		</form>
		</div>
		<p id="or">Or</p>
		<button id="login-button" onclick="window.location.href='/URCRS/login.php'">Back to login</button>
	</div>
</body>
</html>