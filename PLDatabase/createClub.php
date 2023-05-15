<?php 
	require_once("session.php"); 
	require_once("included_functions.php");
	require_once("database.php");

	new_header("Premier League 2022/2023"); 
	$mysqli = Database::dbConnect();
	$mysqli -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if (($output = message()) !== null) {
		echo $output;
	}
	
	echo "<center>";
	echo "<h3>Add Club</h3>";
	echo "<div class='row'>";
	echo "<label for='left-label' class='left inline'>";

	if (isset($_POST["submit"])) {
		if( (isset($_POST["clubName"]) && $_POST["clubName"] !== "") 
			&& (isset($_POST["stadium"]) && $_POST["stadium"] !== "") 
			&& (isset($_POST["wins"]) && $_POST["wins"] !== "")
			&& (isset($_POST["draws"]) && $_POST["draws"] !== "")
			&& (isset($_POST["losses"]) && $_POST["losses"] !== "")
			&& (isset($_POST["gd"]) && $_POST["gd"] !== "")
			&& (isset($_POST["fname"]) && $_POST["fname"] !== "") 
			&& (isset($_POST["lname"]) && $_POST["lname"] !== "") 
			&& (isset($_POST["salary"]) && $_POST["salary"] !== "")
			&& (isset($_POST["managerYear"]) && $_POST["managerYear"] !== "")) {
        
			$queryVerify = "SELECT * FROM Club NATURAL JOIN Stats WHERE ClubName=?";
			$stmtVerify = $mysqli->prepare($queryVerify);
			$stmtVerify->execute([$_POST["clubName"]]);

			if ($stmtVerify->rowCount() == 0) {
				$queryInsertClub = "INSERT INTO Club (ClubName, Stadium) VALUES(?, ?)";
				$stmtInsertClub = $mysqli->prepare($queryInsertClub);
				$stmtInsertClub->execute([$_POST["clubName"], $_POST["stadium"]]);

				if ($stmtInsertClub) {
					$queryGetClub = "SELECT ClubID FROM Club WHERE ClubName=?";
					$stmtGetClub = $mysqli->prepare($queryGetClub);
					$stmtGetClub->execute([$_POST["clubName"]]);

					if ($stmtGetClub) {
						$rowGetClub = $stmtGetClub->fetch(PDO::FETCH_ASSOC);
						$_SESSION["message"] = "".$rowGetClub["ClubID"]."";
						$queryInsertStats = "INSERT INTO Stats VALUES(?, ?, ?, ?, ?)";
						$stmtInsertStats = $mysqli -> prepare($queryInsertStats);
						$stmtInsertStats -> execute([$rowGetClub["ClubID"], $_POST["wins"], $_POST["draws"], $_POST["losses"], $_POST["gd"]]);
						if (!$stmtInsertStats) {
							$_SESSION["message"] = "Error! Could not add Stats";
						}

						$queryInsertManager = "INSERT INTO Manager VALUES(?, ?, ?, ?, ?)";
						$stmtInsertManager = $mysqli -> prepare($queryInsertManager);
						$stmtInsertManager -> execute([$rowGetClub["ClubID"], $_POST["fname"], $_POST["lname"], $_POST["salary"], $_POST["managerYear"]]);
						if (!$stmtInsertManager) {
							$_SESSION["message"] = "Error! Could not add Manager";
						}

						if ( (isset($_POST["kitSponsor"]) && $_POST["kitSponsor"] !== "") 
							&& (isset($_POST["kitSponsorYear"]) && $_POST["kitSponsorYear"] !== "") ) {
							$queryInsertSponsor = "INSERT INTO Sponsors VALUES(?, ?, 'Kit', ?)";
							$stmtInsertSponsor = $mysqli->prepare($queryInsertSponsor);
							$stmtInsertSponsor->execute([$rowGetClub["ClubID"], $_POST["kitSponsor"], $_POST["kitSponsorYear"]]);
							if (!$stmtInsertSponsor) {
								$_SESSION["message"] = "Error! Could not add Kit Sponsor";
							}
						}
						if ( (isset($_POST["shirtSponsor"]) && $_POST["shirtSponsor"] !== "") 
							&& (isset($_POST["shirtSponsorYear"]) && $_POST["shirtSponsorYear"] !== "") ) {
							$queryInsertSponsor = "INSERT INTO Sponsors VALUES(?, ?, 'Shirt', ?)";
							$stmtInsertSponsor = $mysqli->prepare($queryInsertSponsor);
							$stmtInsertSponsor->execute([$rowGetClub["ClubID"], $_POST["shirtSponsor"], $_POST["shirtSponsorYear"]]);
							if (!$stmtInsertSponsor) {
								$_SESSION["message"] = "Error! Could not add Shirt Sponsor";
							}
						}
					}
					$_SESSION["message"] = "Club has been added";
					redirect("readMain.php");

				} else {
					$_SESSION["message"] = "Error! Could not add Club.";
					redirect("createClub.php");
				}

			} else {
				$_SESSION["message"] = "Error! Club Name is already in use.";
				redirect("createClub.php");
			}	

		} else {
			$_SESSION["message"] = "Error! Not enough information";
			redirect("createClub.php");
		}
		
	} else {
		echo "<form action='createClub.php' method='POST'>";
            echo "<details open><summary><h5>Club Info</h5></summary>";
			echo "Club Name:<input type='text' name='clubName' />";
			echo "Stadium Name:<input type='text' name='stadium' />";
            echo "</details>";

            echo "<details><summary><h5>Stats (Dropdown)</h5></summary>";
            echo "Wins:<input type='number' name='wins' step='1' min='0' max='99' />";
            echo "Draws:<input type='number' name='draws' step='1' min='0' max='99' />";
            echo "Losses:<input type='number' name='losses' step='1' min='0' max='99' />";
			echo "Goal Difference:<input type='number' name='gd' step='1' min='-99' max='99' />";
			echo "</details>";

            echo "<details><summary><h5>Manager Info (Dropdown)</h5></summary>";
			echo "Manager's First Name:<input type='text' name='fname' />";
            echo "Manager's Last Name:<input type='text' name='lname' />";
			echo "Salary:<input type='number' name='salary' step='1' min='0' max='999999999' />";
			echo "Years in Position:<input type='number' name='managerYear' step='1' min='0' max='999999999' />";
            echo "</details>";

            echo "<details><summary><h5>Kit Sponsor (Optional)</h5></summary>";
			echo "Sponsor Name:<input type='text' name='kitSponsor' />";
			echo "Year Started:<input type='number' name='kitSponsorYear' step='1' min='0' max='2023' />";
            echo "</details>";

			echo "<details><summary><h5>Shirt Sponsor (Optional)</h5></summary>";
			echo "Sponsor Name:<input type='text' name='shirtSponsor' />";
			echo "Year Started:<input type='number' name='shirtSponsorYear' step='1' min='0' max='2023' />";
            echo "</details>";

            echo "<input type='submit' name='submit' class='button tiny round' value='Add Club' />";

		echo "</form>";
					
	}
	echo "</label>";
	echo "</div>";
	echo "<br /><p>&laquo:<a href='readMain.php'>Back to Main Page</a>";
	echo "</center>";

	new_footer("Premier League 2022/23 Database");	
	Database::dbDisconnect($mysqli);

?>