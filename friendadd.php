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
        <title>Add Friend List Page</title>
	</head>
	<body>

		<div class="container2">
			<h1>My Friend System</h1>
			<p><?php echo $_SESSION["profile_name"]; ?>'s Add Friend Page</p>
			<p>Total number of friends is <?php echo $_SESSION["num_of_friends"]; ?> </p>
		</div>

<?php

//at first the query will return only 5 records, start on record 1 (OFFSET 0)
$offset = 0;
$each_page = 5;
//if the user clicks on the "next" or "previous" button
if (isset($_GET["page"])) {
	if ($_GET["page"] > 1) {
		$offset = ($_GET["page"] - 1) * $each_page;
	}
} else {
	$_GET["page"] = 1;
}

$message = "";
$fid = $_SESSION["friend_id"];

//only logged in users can view this page
if ($_SESSION["login_status"] == FALSE) {
	header ("location: login.php");
} else {
		//connect to the database
		require_once ("settings.php");
		$conn = @mysqli_connect($host, $user, $pswd, $dbnm);
		if (!$conn) {
			$message = "<p>We are presently experiencing database difficulties. Please try again later! <p>";
		} else {				
			$query_addfriends = "SELECT f.friend_id, f.profile_name, COUNT(DISTINCT m.friend_id2) AS mutual_friends
								FROM friends f
								LEFT JOIN myfriends m
								ON (f.friend_id = m.friend_id1 
									AND m.friend_id2 IN 
										(SELECT m.friend_id2 FROM myfriends m WHERE m.friend_id1 = '$fid'))
								WHERE f.friend_id <> '$fid' 
									AND f.friend_id NOT IN 
										(SELECT m.friend_id2 FROM myfriends m WHERE m.friend_id1 = '$fid')
								GROUP BY f.friend_id
								ORDER BY f.profile_name
								LIMIT $offset, $each_page;";
								
			$display_friends = mysqli_query($conn, $query_addfriends);
			$count = mysqli_num_rows($display_friends);
			if ($count > 0) {
				if ($display_friends) {
					//only generate the table when there are records
					echo "<div class=\"container\">\n";
					echo "<table>\n";
					while ($info = mysqli_fetch_assoc($display_friends)) {
						echo "<tr>\n";
						echo "<td>", $info["profile_name"],"</td>\n";
						echo "<td>", $info["mutual_friends"] . " mutual friends","</td>\n";
						echo "<td><form class=\"form2\" method=\"post\" action=\"friendadd.php\">\n";
						echo "<input type=\"hidden\" name=\"id1\" value=",$_SESSION["friend_id"],"/>\n";
						echo "<input type=\"hidden\" name=\"id2\" value=",$info["friend_id"],"/>\n";
						echo "<button type=\"submit\" name=\"addfriend\">Add as friend</button>\n";
						echo "</form></td>\n";
						echo "</tr>\n";
					}
					echo "</table>\n";
					echo "</div>\n";
					mysqli_free_result ($display_friends);
				} else {
					$message = "<p>The Add Friend list is not available at the moment.</p>";
				}
			} else {
				$message = "<p>There is currently no users that you can add right now :< </p>";
			}
	
	//when the user clicks on the "add as friend" button
	if (isset($_POST["addfriend"])) {
		$id1 = $_POST["id1"];
		$id2 = $_POST["id2"];
		$add_query1 = "INSERT INTO myfriends
						(friend_id1, friend_id2)
					   VALUES
						('$id1','$id2');";
		$add_query2 = "INSERT INTO myfriends
						(friend_id1, friend_id2)
					   VALUES
						('$id2','$id1');";
		$num_friends1 = "UPDATE friends SET num_of_friends = num_of_friends + 1 WHERE friend_id = '$id1';";
		$num_friends2 = "UPDATE friends SET num_of_friends = num_of_friends + 1 WHERE friend_id = '$id2';";
		$add1 = mysqli_query($conn, $add_query1);
		$add2 = mysqli_query($conn, $add_query2);
		$update_friends1 = mysqli_query($conn, $num_friends1);
		$update_friends2 = mysqli_query($conn, $num_friends2);
		if ($add1 && $add2 && $update_friends1 && $update_friends2) {
			$_SESSION["num_of_friends"] += 1;
			$message = "<p>Your new friend has been added to your friend list!</p>";
			header ("location: friendadd.php");
		} else {
			$message = "<p>You cannot add this user as friend at the moment.</p>";
		}
	}
			
			//calculate the total of pages to display
			$query_addfriends2 = "SELECT f.friend_id, f.profile_name, COUNT(DISTINCT m.friend_id2) AS mutual_friends
								FROM friends f
								LEFT JOIN myfriends m
								ON (f.friend_id = m.friend_id1 
									AND m.friend_id2 IN 
										(SELECT m.friend_id2 FROM myfriends m WHERE m.friend_id1 = '$fid'))
								WHERE f.friend_id <> '$fid' 
									AND f.friend_id NOT IN 
										(SELECT m.friend_id2 FROM myfriends m WHERE m.friend_id1 = '$fid')
								GROUP BY f.friend_id;";
			$display_friends2 = mysqli_query($conn, $query_addfriends2);
			$count2 = mysqli_num_rows($display_friends2);
			$pages = ceil($count2/$each_page);
			mysqli_free_result ($display_friends2);
		mysqli_close($conn);
		}
}

?>

		<div class="container">
			<?php
				if ($_GET["page"] > 1) {
					echo "<a href = \"friendadd.php?page=" . ($_GET["page"] - 1) . " \">Previous</a>";
				}
				if ($pages != 1 && $_GET["page"] < $pages) {
					echo "<a href = \"friendadd.php?page=" . ($_GET["page"] + 1) . " \">Next</a>";
				}
			?>
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
			<?php echo $message; ?>
		</div>
		
		<div class="container">
			<span class="scissors">&#9986;</span>
			<hr class="line">
		</div>
		
		<div class="container">
			<a href="friendlist.php">Friend List</a>
			<a href="logout.php">Log Out</a>
		</div>
		
	</body>
</html>