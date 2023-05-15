<?php 
//Add beginning code to 
//1. Require the needed 3 files
//2. Connect to your database
//3. Output a message, if there is one
	require_once("session.php"); 
	require_once("included_functions.php");
	require_once("database.php");

	new_header("Premier League 2022/23"); 
	$mysqli = Database::dbConnect();
	$mysqli -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if (($output = message()) !== null) {
		echo $output;
	}
	
	echo "<center>";
	echo "<h3>Add a Player</h3>";
	echo "<div class='row'>";
	echo "<label for='left-label' class='left inline'>";

	if (isset($_POST["submit"])) {
		if ( (isset($_POST["shirtnum"]) && $_POST["shirtnum"] !== "") 
			&& (isset($_POST["fname"]) && $_POST["fname"] !== "") 
			&& (isset($_POST["lname"]) && $_POST["lname"] !== "") 
			&& (isset($_POST["dob"]) && $_POST["dob"] !== "") 
			&& (isset($_POST["position"]) && $_POST["position"] !== "")
			&& (isset($_POST["salary"]) && $_POST["salary"] !== "")) {

		$queryVerify = "SELECT * FROM Players WHERE ShirtNum = ? AND ClubID = ?";
		$stmtVerify = $mysqli -> prepare($queryVerify);
		$stmtVerify -> execute([$_POST["shirtnum"], $_POST["clubid"]]);

		if ($stmtVerify -> rowCount() == 0) {

			$queryInsert = "INSERT INTO Players (ClubID, ShirtNum, FName, LName, DOB, PrimaryPos, Salary) VALUES(?, ?, ?, ?, ?, ?, ?)";
			$stmtInsert = $mysqli->prepare($queryInsert);
			$stmtInsert->execute([$_POST["clubid"], $_POST["shirtnum"], $_POST["fname"], $_POST["lname"], $_POST["dob"], $_POST["position"], $_POST["salary"]]);

			if ($stmtInsert) {
				$_SESSION["message"] = "Player has been added";
				redirect("readPlayers.php?id=".urlencode($_POST["clubid"])."");
			}
			else {
				$_SESSION["message"] = "Error! Could not add Player.";
				redirect("createPlayer.php?id=".urlencode($_POST["clubid"])."");
			}
		} else {
			$_SESSION["message"] = "Error! Shirt number is already in use.";
			redirect("createPlayer.php?id=".urlencode($_POST["clubid"])."");
		}

	} else {
		$_SESSION["message"] = "Shirt Number already exists!";

	}
	} else {
		echo "<center>";
		echo "<form action='createPlayer.php' method='POST'>";
			echo "<input type='hidden' name='clubid' value='".$_GET['id']."' />";
			echo "Shirt Number:<input type='number' name='shirtnum' step='1' min='1' max='99' />";
			echo "First Name:<input type='text' name='fname' />";
			echo "Last Name:<input type='text'  name='lname' />";
			echo "Date of Birth:<input type='date' name='dob' />";
			
			echo "Position:<select name='position'>";
			$query2 = "SELECT DISTINCT PrimaryPos FROM Players";
			$stmt2 = $mysqli->prepare($query2);	
			$stmt2 -> execute();
			while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
				$name = $row['PrimaryPos'];
				echo "<option value=\"".$name."\">".$name."</option>";
			}
			echo "</select>";
			
			echo "Salary:<input type='number' step='1' min='0' max='999999999' name='salary' />";
			echo "<input type='submit' name='submit' class='button tiny round' value='Add Player' />";
		echo "</form>";
		echo "</center>";
				
	}
	echo "</label>";
	echo "</div>";
	echo "<br /><p>&laquo:<a href='readPlayers.php?id=".urlencode($_GET['id'])."'>Back to Players Page</a>";
	echo "</center>";

	new_footer("Premier League 2022/23 Database");	
	Database::dbDisconnect($mysqli);

?>