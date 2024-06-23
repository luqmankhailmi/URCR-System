<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="/URCRS/css/login.css">
	<link rel="icon" type="image/x-icon" href="images/C.png">
	<title>Login</title>
</head>
<body>
	<div id="login-container">
		<div id="title-container">
			<p id="title">UITM Raub Club Registration System</p>
		</div>
		<h1 id="login-title">Log In</h1>
		<div id="form-container">
		<form action="/URCRS/auth/check_login.php" method="post">
			<p class="login-items">Email address :</p>
			<input class="input-bar" type="email" name="input_email" placeholder="example@domain.com" required>
			<p class="login-items">Password :</p>
			<input class="input-bar" type="password" name="input_pass" placeholder="**********" required><br><br>
			<button id="login-button" type="submit">Log In</button>
		</form>
		</div>
		<p id="or">Or</p>
		<button id="register-button" onclick="window.location.href='/URCRS/register.php'">Register</button>
	</div>
</body>
</html>