<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
		<meta name="description" content="A Sorrowful Social Network System" />
		<meta name="keywords" content="HTML, CSS, PHP, MySQL" />
		<meta name="author" content="An Average Man From The Earth"  />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"  />
		<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,700" rel="stylesheet"  />
        <link href="styles/style.css" rel="stylesheet"  />
        <title>Log In Page</title>
	</head>
	<body>

<?php
function sanitise($x) {
	$data = trim($x);
	$data = stripslashes($x);
	$data = htmlspecialchars($x);
	return $x;
}

$_SESSION["login_status"] = FALSE;
$_SESSION["profile_name"] = $_SESSION["friend_id"] = "";
$email = $pass = $email_error = $pass_error = $db_error = "";
$pass_regex = "/^[a-zA-Z0-9]+$/";

//validate user account
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$errors = 0;
	if (empty($_POST["email"])) {
		$email_error = "* Email address is required";
		$errors += 1;
		} else {
			$email = sanitise($_POST["email"]);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$email_error = "* Please enter a valid email";
				$errors += 1;
			}
		}
	
	if (empty($_POST["pass"])) {
		$pass_error = "*Password is required";
		$errors += 1;
	} else {
		$pass = sanitise($_POST["pass"]);
		if (!preg_match($pass_regex,$pass)) {
			$pass_error = "*Password can only contain letters and numbers";
			$errors += 1;
		}
	}
	
	/////////////////mysql queries//////////////////////
		//connect to the database
		require_once ("settings.php");
		$conn = @mysqli_connect($host, $user, $pswd, $dbnm);
		if (!$conn) {
			$db_error = "<p>We are presently experiencing database difficulties. Please try again later! <p>";
		} else {
			$query_mail = "SELECT * FROM friends WHERE friend_email = '$email' AND password = '$pass';";
			$result = mysqli_query($conn, $query_mail);
			$num_rows = mysqli_num_rows($result);
			if ($num_rows == 0) {
				//when we don't have any email or password match
				$db_error = "<p>This account could not be found!</p>";
				$errors += 1;
			}
			if ($errors == 0) {
				//set up the session variables
				$row = mysqli_fetch_assoc($result);
				$_SESSION["profile_name"] = $row["profile_name"];
				$_SESSION["friend_id"] = $row["friend_id"];
				$_SESSION["num_of_friends"] = $row["num_of_friends"];
				$_SESSION["login_status"] = TRUE;
				mysqli_free_result($result);
				header ("location: friendlist.php");
			}
			mysqli_close($conn);
		}
}
?>

		<div>
			<h1>My Friend System - Log In Page</h1>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" novalidate="novalidate">
			
			<p>
			<label for="email">Email:</label>
			<input type="text" id="email" name="email" value="<?php echo $email;?>"/>
			<span class="error"> <?php echo $email_error;?></span>
			</p>
			
			<p>
			<label for="pass">Password:</label>
			<input type="text" id="pass" name="pass"/>
			<span class="error"> <?php echo $pass_error;?></span>
			</p>

			<input type= "submit" value="Log In">
			<input type= "reset" value="Clear">
			<br>
			
			<div class="container">
				<span class="scissors">&#9986;</span>
				<hr class="line">
			</div>
			
			<div class="container">
				<?php $art = "ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ";
				echo "<div><pre class='art'>" . $art . "</pre></div>"; ?>
			</div>
			
			<div class="container">
				<?php echo $db_error; ?>
			</div>
			
			<div class="container">
				<span class="scissors">&#9986;</span>
				<hr class="line">
			</div>
			
			<div class="container">
				<a href="index.php">Return to Home Page</a>
			</div>

			</form>
		</div>
	</body>
</html>