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
	echo "<div class='row'>";
	echo "<label for='left-label' class='left inline'>";

	if (isset($_POST["submit"])) {
		$queryUpdate = "UPDATE Players SET FName=?, LName=?, DOB=?, Salary=? WHERE ClubID=? AND ShirtNum=?";
		$stmtUpdate = $mysqli->prepare($queryUpdate);
		$stmtUpdate->execute([$_POST["fname"], $_POST["lname"], $_POST["dob"], $_POST["salary"], $_POST["clubID"], $_POST["shirtNum"]]);

		if ($stmtUpdate) {
			if ($_POST["position"] != "") {
				$queryVerify = "SELECT PrimaryPos, LName FROM Players WHERE ClubID=? AND ShirtNum=? AND PrimaryPos=?";
				$stmtVerify = $mysqli->prepare($queryVerify);
				$stmtVerify->execute([$_POST["clubID"], $_POST["shirtNum"], $_POST["position"]]);
				if ($stmtVerify->rowCount() == 0) {
					$queryPos = "UPDATE Players SET PrimaryPos=? WHERE ClubID=? AND ShirtNum=?";
					$stmtPos = $mysqli->prepare($queryPos);
					$stmtPos->execute([$_POST["position"], $_POST["clubID"], $_POST["shirtNum"]]);
					if (!$stmtPos) {
						$_SESSION["message"] = "Error! Could not update the position of player";
					}
				} else {
					$_SESSION["message"] = "Player already has this position";
                    redirect("updatePlayer.php?id=".urlencode($_POST["clubID"])."&num=".urlencode($_POST["shirtNum"])."");
				}
			} else {
                $_SESSION["message"] = "Player has been updated";
            }
            redirect("readPlayers.php?id=".urlencode($_POST["clubID"])."");
		} else {
			$_SESSION["message"] = "Error! Could not update Player";
            redirect("readPlayers.php?id=".urlencode($_POST["clubID"])."");
		}
		
	} else {

		if ( (isset($_GET["id"]) && $_GET["id"] !== "") 
			&& (isset($_GET["num"]) && $_GET["num"] !== "") ){
				$queryRead = "SELECT P.FName, P.LName, DOB, PrimaryPos, P.Salary FROM Players AS P WHERE ClubID=? AND ShirtNum=?";
				$stmtRead = $mysqli->prepare($queryRead);
				$stmtRead->execute([$_GET["id"], $_GET["num"]]);

			if ($stmtRead)  {
				$rowRead = $stmtRead->fetch(PDO::FETCH_ASSOC);
				echo "<h3>".$rowRead["LName"]."'s Information</h3>";
				echo "<form action='updatePlayer.php' method='POST'>";
					echo "<input type='hidden' name='clubID' value='".$_GET["id"]."'/>";
					echo "<input type='hidden' name='shirtNum' value='".$_GET["num"]."'/>";
					echo "First Name:<input type='text' name='fname' value='".$rowRead["FName"]."' />";
					echo "Last Name:<input type='text' name='lname' value='".$rowRead["LName"]."' />";
					echo "Date of Birth:<input type='date' name='dob' value='".$rowRead["DOB"]."' />";

					echo "Change Primary Position:<select name='position'>";
					echo "<option></option>";
					$queryPos = "SELECT DISTINCT PrimaryPos FROM Players";
					$stmtPos = $mysqli->prepare($queryPos);	
					$stmtPos -> execute();
					while($rowPos = $stmtPos->fetch(PDO::FETCH_ASSOC)) {
						echo "<option value=\"".$rowPos['PrimaryPos']."\">".$rowPos['PrimaryPos']."</option>";
					}
					echo "</select>";

					echo "Salary (USD):<input type='number' name='salary' step='1' min='0' max='999999999' value='".$rowRead["Salary"]."' />";	

					echo "<input type='submit' name='submit' class='button tiny round' value='Update Player Info' />";
				echo "</form>";	
			} else {
				$_SESSION["message"] = "Player could not be found!";
				redirect("readPlayers.php?id=".urlencode($_POST["clubid"])."");
		}
	  }
    }
	
    echo "</label>";
    echo "</div>";
    echo "<br /><p>&laquo:<a href='readPlayers.php?id=".urlencode($_GET['id'])."'>Back to Players Page</a>";
	echo "</center>";

	new_footer("Premier League 2022/23 Database");	
	Database::dbDisconnect($mysqli);
?>