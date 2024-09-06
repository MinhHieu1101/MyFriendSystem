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
        <title>Friend List Page</title>
	</head>
	<body>

		<div class="container2">
			<h1>My Friend System</h1>
			<p><?php echo $_SESSION["profile_name"]; ?>'s Friend List Page</p>
			<p>Total number of friends is <?php echo $_SESSION["num_of_friends"]; ?> </p>
		</div>

<?php

$error_message = "";
$fid = $_SESSION["friend_id"];

//only logged in users can view this page
if ($_SESSION["login_status"] == FALSE) {
	header ("location: login.php");
} else {
		//connect to the database
		require_once ("settings.php");
		$conn = @mysqli_connect($host, $user, $pswd, $dbnm);
		if (!$conn) {
			$error_message = "<p>We are presently experiencing database difficulties. Please try again later! <p>";
		} else {
			$query_allfriends = "SELECT *
								FROM friends f
								JOIN myfriends m ON m.friend_id1 = '$fid'
								WHERE f.friend_id = m.friend_id2
								ORDER BY f.profile_name;";
			$display_friends = mysqli_query($conn, $query_allfriends);
			$count = mysqli_num_rows($display_friends);
			if ($count > 0) {
				if ($display_friends) {
					//only generate the table when there are records
					echo "<div class=\"container\">\n";
					echo "<table>\n";
					while ($info = mysqli_fetch_assoc($display_friends)) {
						echo "<tr>\n";
						echo "<td>", $info["profile_name"],"</td>\n";
						echo "<td><form class=\"form2\" method=\"post\" action=\"friendlist.php\">\n";
						echo "<input type=\"hidden\" name=\"id1\" value=",$_SESSION["friend_id"],"/>\n";
						echo "<input type=\"hidden\" name=\"id2\" value=",$info["friend_id"],"/>\n";
						echo "<button type=\"submit\" name=\"unfriend\">Unfriend</button>\n";
						echo "</form></td>\n";
						echo "</tr>\n";
					}
					echo "</table>\n";
					echo "</div>\n";
					mysqli_free_result ($display_friends);
				} else {
					//if no records are found
					$error_message = "<p>The Friend list is not available at the moment.</p>";
				}
			} else {
				$error_message = "<p>This user currently does not have any online friends :< </p>";
			}
			
	//when the user clicks on the "unfriend" button
	if (isset($_POST["unfriend"])) {
		$id1 = $_POST["id1"];
		$id2 = $_POST["id2"];
		$delete_query1 = "DELETE FROM myfriends
				WHERE friend_id1 = '$id1' AND friend_id2 = '$id2';";
		$delete_query2 = "DELETE FROM myfriends
				WHERE friend_id1 = '$id2' AND friend_id2 = '$id1';";
		$num_friends1 = "UPDATE friends SET num_of_friends = num_of_friends - 1 WHERE friend_id = '$id1';";
		$num_friends2 = "UPDATE friends SET num_of_friends = num_of_friends - 1 WHERE friend_id = '$id2';";
		$del1 = mysqli_query($conn, $delete_query1);
		$del2 = mysqli_query($conn, $delete_query2);
		$update_friends1 = mysqli_query($conn, $num_friends1);
		$update_friends2 = mysqli_query($conn, $num_friends2);
		if ($del1 && $del2 && $update_friends1 && $update_friends2) {
			$_SESSION["num_of_friends"] -= 1;
			header ("location: friendlist.php");
		} else {
			$error_message = "<p>You cannot unfriend this user at the moment.</p>";
		}
	}
		mysqli_close($conn);
		}
}

?>

		<div class="container">
			<span class="scissors">&#9986;</span>
			<hr class="line">
		</div>
		
		<div class="container">
			<?php $art = "ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ ᓚᘏᗢ";
			echo "<div><pre class='art'>" . $art . "</pre></div>"; ?>
		</div>

		<div class="container">
			<?php echo $error_message; ?>
		</div>
		
		<div class="container">
			<span class="scissors">&#9986;</span>
			<hr class="line">
		</div>
		
		<div class="container">
			<a href="friendadd.php">Add Friends</a>
			<a href="logout.php">Log Out</a>
		</div>

	</body>
</html>