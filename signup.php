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
        <title>Sign Up Page</title>
	</head>
	<body>

<?php
function sanitise($x) {
	$x = trim($x);
	$x = stripslashes($x);
	$x = htmlspecialchars($x);
	return $x;
}

$_SESSION["login_status"] = FALSE;
$_SESSION["profile_name"] = $_SESSION["friend_id"] = "";
$email = $profile = $pass1 = $pass2 = $email_error = $profile_error = $pass1_error = $pass2_error = $db_error = "";
$profile_regex = "/^[a-zA-Z]+$/";
$pass_regex = "/^[a-zA-Z0-9]+$/";

//validate user input
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
	
	if (empty($_POST["profile"])) {
		$profile_error = "*Profile Name is required";
		$errors += 1;
	} else {
		$profile = sanitise($_POST["profile"]);
		if (!preg_match($profile_regex,$profile)) {
			$profile_error = "*Profile Name can only contain letters";
			$errors += 1;
		}
	}
	
	if (empty($_POST["pass1"])) {
		$pass1_error = "*Password is required";
		$errors += 1;
	} else {
		$pass1 = sanitise($_POST["pass1"]);
		if (!preg_match($pass_regex,$pass1)) {
			$pass1_error = "*Password can only contain letters and numbers";
			$errors += 1;
		}
	}
	
	if (empty($_POST["pass2"])) {
		$pass2_error = "*Password confirmation is required";
		$errors += 1;
	} else {
		$pass2 = sanitise($_POST["pass2"]);
		if (strcmp($pass1,$pass2) != 0) {
			$pass2_error = "*Password confirmation does not match";
			$errors += 1;
		}
	}
	
	/////////////////mysql queries//////////////////////
		//connect to the database
		require_once ("settings.php");
		$conn = @mysqli_connect($host, $user, $pswd, $dbnm);
		if (!$conn) {
			$db_error = "<p>We are presently experiencing database difficulties. Please try again later!</p>";
		} else {
			$query_mail = "SELECT friend_email FROM friends WHERE friend_email = '$email';";
			$result = mysqli_query($conn, $query_mail);
			$num_rows = mysqli_num_rows($result);
			if ($num_rows > 0) {
				$email_error = "*This email is already registered";
				$errors += 1;
			}
			mysqli_free_result($result);
			if ($errors == 0) {
				//insert when there are no errors
				$insert_query = "INSERT INTO friends 
									(friend_id, friend_email, password, profile_name, date_started, num_of_friends)
								VALUES
									(NULL, '$email', '$pass1', '$profile', NOW(), '0');";
				$add_account = mysqli_query($conn, $insert_query);
				if ($add_account) {
					//set up the session variables
					$_SESSION["profile_name"] = $profile;
					$_SESSION["friend_id"] = mysqli_insert_id($conn);
					$_SESSION["num_of_friends"] = 0;
					$_SESSION["login_status"] = TRUE;
					header ("location: friendadd.php");
				} else {
					$db_error = "<p>We are presently experiencing database difficulties. Please try again later!</p>";
				}
			}
			mysqli_close($conn);
		}
}
?>

		<div>
			<h1>My Friend System - Registration Page</h1>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" novalidate="novalidate">
			
			<p>
			<label for="email">Email:</label>
			<input type="text" id="email" name="email" value="<?php echo $email;?>"/>
			<span class="error"> <?php echo $email_error;?></span>
			</p>
			
			<p>
			<label for="profile">Profile Name:</label>
			<input type="text" id="profile" name="profile" value="<?php echo $profile;?>"/>
			<span class="error"> <?php echo $profile_error;?></span>
			</p>
			
			<p>
			<label for="pass1">Password:</label>
			<input type="text" id="pass1" name="pass1"/>
			<span class="error"> <?php echo $pass1_error;?></span>
			</p>
			
			<p>
			<label for="pass2">Confirm Password:</label>
			<input type="text" id="pass2" name="pass2"/>
			<span class="error"> <?php echo $pass2_error;?></span>
			</p>

			<input type= "submit" value="Register">
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