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
        <title>Definitely Not Facebook</title>
	</head>
	<body>
<?php
	$db_message = "";
	
		/////////////////mysql queries//////////////////////
	$create_query1 = "CREATE TABLE IF NOT EXISTS friends (
						friend_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
						friend_email VARCHAR(50) NOT NULL,
						password VARCHAR(20) NOT NULL,
						profile_name VARCHAR(30) NOT NULL,
						date_started date NOT NULL,
						num_of_friends INT UNSIGNED
						);";

	$create_query2 = "CREATE TABLE IF NOT EXISTS myfriends (
						friend_id1 INT NOT NULL,
						friend_id2 INT NOT NULL
						);";

	$insert_query1 = "INSERT IGNORE INTO friends
						(friend_id, friend_email, password, profile_name, date_started, num_of_friends)
					VALUES
						('1','mleedes0@google.ca','m7kNu0zi','Nimbus','2021-10-26','4'),
						('2','kgrabert1@phoca.cz','auc2iqaP','Mudblood','2022-09-16','4'),
						('3','bdurdy2@weather.com','xYxq7Yb9','Cornelius','2021-08-07','2'),
						('4','mradborn3@prweb.com','w28vWDFa','Malfroy','2021-11-09','3'),
						('5','ninworth4@washington.com','0VbHd0UD','Weasley','2022-05-25','2'),
						('6','alutwyche5@irs.gov','M7Nko5wh','Kedavra','2021-10-16','1'),
						('7','cbrassington6@purevolume.com','8qwQw6z5','Morgana','2021-11-03','4'),
						('8','jescolme7@prnewswire.com','ZqVEJZ7o','Severus','2022-03-26','0'),
						('9','etysack8@quantcast.com','bx7MT2KF','Mcgonagall','2022-04-24','0'),
						('10','cbenardeau9@columbia.edu','hkW2PxLc','Ravenclaw','2022-11-02','0');";

	$insert_query2 = "INSERT INTO myfriends
						(friend_id1, friend_id2)
					VALUES
						('1','3'),
						('1','4'),
						('1','5'),
						('3','1'),
						('4','1'),
						('5','1'),
						('2','4'),
						('2','5'),
						('2','6'),
						('4','2'),
						('5','2'),
						('6','2'),
						('7','1'),
						('7','2'),
						('7','3'),
						('7','4'),
						('1','7'),
						('2','7'),
						('3','7'),
						('4','7');";
	
	//connect to the database
	require_once ("settings.php");
	$conn = @mysqli_connect($host, $user, $pswd, $dbnm);
	if (!$conn) {
		$db_message = "<p>We are presently experiencing database difficulties. Please try again later!</p>";
	} else {
		$result1 = mysqli_query($conn, $create_query1);
		$result2 = mysqli_query($conn, $create_query2);
		if ($result1 && $result2) {
			$result3 = mysqli_query($conn, $insert_query1);
			$myfriends_query = mysqli_query($conn, "SELECT * FROM myfriends");
			$rows = mysqli_num_rows($myfriends_query);
			if ($rows == 0) {
				$result4 = mysqli_query($conn, $insert_query2);
				$rows += 20;
			}
			if ($result3 && $rows > 0) {
				$db_message = "<p>Tables successfully created and populated!</p>";
			} else {
				$db_message = "<p>We cannot insert data into the tables at the moment!</p>";
			}
		} else {
				$db_message = "<p>We cannot create the tables at the moment!</p>";
		}
	mysqli_close($conn);
	}
?>
		<div class="frame">
		<h1>My Friend System</h1>
        
		<dl>
		  <dt>Name</dt>
		  <dd>Doan Minh Hieu</dd>
		  <dt>Student ID</dt>
		  <dd>104168106</dd>
		  <dt>Email</dt>
		  <dd>104168106@student.swin.edu.au</dd>
		</dl>			
	
		<p>I declare that this assignment is my individual work. 
		I have not worked collaboratively nor have I copied from any other student’s work or from any other source.</p>

			<div class="container">
				<span class="scissors">&#9986;</span>
				<hr class="line">
			</div>
			
			<div class="container">
				<a href="signup.php">Sign Up Page</a>
				<a href="login.php">Log In Page</a>
				<a href="about.php">My Humble Achievements</a>
			</div>
			
			<div class="container">
				<span class="scissors">&#9986;</span>
				<hr class="line">
			</div>
			
			<div class="container">
				<?php $art = "ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ";
				echo "<div><pre class='art'>" . $art . "</pre></div>"; ?>
			</div>
			
			<div class="container">
				<?php echo $db_message;	?>
			</div>
			
			<div class="container">
				<span class="scissors">&#9986;</span>
				<hr class="line">
			</div>
		</div>
	</body>
</html>