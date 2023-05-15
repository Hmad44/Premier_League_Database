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
	echo "<h3>Update Club</h3>";
	echo "<div class='row'>";
	echo "<label for='left-label' class='left inline'>";
    
	if (isset($_POST["submit"])) {
        $queryVerify = "SELECT * FROM Club NATURAL JOIN Stats WHERE ClubID!=? AND ClubName=?";
        $stmtVerify = $mysqli->prepare($queryVerify);
        $stmtVerify->execute([$_POST["clubID"], $_POST["clubName"]]);
        if ($stmtVerify->rowCount() == 0) {

            $queryUpdateClub = "UPDATE Club SET ClubName=?, Stadium=? WHERE ClubID=?";
            $stmtUpdateClub = $mysqli->prepare($queryUpdateClub);
            $stmtUpdateClub->execute([$_POST["clubName"], $_POST["stadium"], $_POST["clubID"]]);
            
            if ($stmtUpdateClub) {
                $queryUpdateStats = "UPDATE Stats SET Wins=?, Draws=?, Losses=?, GD=? WHERE ClubID=?";
                $stmtUpdateStats = $mysqli->prepare($queryUpdateStats);
                $stmtUpdateStats->execute([$_POST["wins"], $_POST["draws"], $_POST["losses"], $_POST["gd"], $_POST["clubID"]]);
                if (!$stmtUpdateStats) {
                    $_SESSION["message"] = "Error! Could not update Stats";
                    redirect("updateClub.php?id=".urlencode($_POST["clubID"])."");
                }

                $queryUpdateManager = "UPDATE Manager SET FName=?, LName=?, Salary=?, YearsInPosition=? WHERE ClubID=?";
                $stmtUpdateManager = $mysqli->prepare($queryUpdateManager);
                $stmtUpdateManager->execute([$_POST["fname"], $_POST["lname"], $_POST["salary"], $_POST["managerYear"], $_POST["clubID"]]);
                if (!$stmtUpdateManager) {
                    $_SESSION["message"] = "Error! Could not update Manager";
                    redirect("updateClub.php?id=".urlencode($_POST["clubID"])."");
                }
                
                if ( (isset($_POST["kitSponsor"]) && $_POST["kitSponsor"] !== "") 
                    && (isset($_POST["kitSponsorYear"]) && $_POST["kitSponsorYear"] !== "") ) {
                    $queryCheckKitSponsor = "SELECT * FROM Sponsors WHERE SponsorType='Kit' AND SponsorType IS NOT NULL AND ClubID=?";
                    $stmtCheckKitSponsor = $mysqli->prepare($queryCheckKitSponsor);
                    $stmtCheckKitSponsor->execute([$_POST["clubID"]]);
                    if ($stmtCheckKitSponsor->rowCount() > 0) {
                        $queryUpdateKitSponsor = "UPDATE Sponsors SET SponsorName=?, StartDate=? WHERE ClubID=? AND SponsorType='Kit'";
                        $stmtUpdateKitSponsor = $mysqli->prepare($queryUpdateKitSponsor);
                        $stmtUpdateKitSponsor->execute([$_POST["kitSponsor"], $_POST["kitSponsorYear"], $_POST["clubID"]]);
                    } else {
                        $queryUpdateKitSponsor = "INSERT INTO Sponsors VALUES(?, ?, 'Kit', ?)";
                        $stmtUpdateKitSponsor = $mysqli->prepare($queryUpdateKitSponsor);
                        $stmtUpdateKitSponsor->execute([$_POST["clubID"], $_POST["kitSponsor"], $_POST["kitSponsorYear"]]);
                    }
                    
                    if (!$stmtUpdateKitSponsor) {
                        $_SESSION["message"] = "Error! Could not update Kit Sponsor";
                        redirect("updateClub.php?id=".urlencode($_POST["clubID"])."");
                    }
                }

                if ( (isset($_POST["shirtSponsor"]) && $_POST["shirtSponsor"] !== "") 
                    && (isset($_POST["shirtSponsorYear"]) && $_POST["shirtSponsorYear"] !== "") ) {
                    $queryCheckShirtSponsor = "SELECT * FROM Sponsors WHERE SponsorType='Shirt' AND SponsorType IS NOT NULL AND ClubID=?";
                    $stmtCheckShirtSponsor = $mysqli->prepare($queryCheckShirtSponsor);
                    $stmtCheckShirtSponsor->execute([$_POST["clubID"]]);

                    if ($stmtCheckShirtSponsor->rowCount() > 0) {
                        $queryUpdateShirtSponsor = "UPDATE Sponsors SET SponsorName=?, StartDate=? WHERE ClubID=? AND SponsorType='Shirt'";
                        $stmtUpdateShirtSponsor = $mysqli->prepare($queryUpdateShirtSponsor);
                        $stmtUpdateShirtSponsor->execute([$_POST["shirtSponsor"], $_POST["shirtSponsorYear"], $_POST["clubID"]]);
                    } else {
                        $queryUpdateShirtSponsor = "INSERT INTO Sponsors VALUES(?, ?, 'Shirt', ?)";
                        $stmtUpdateShirtSponsor = $mysqli->prepare($queryUpdateShirtSponsor);
                        $stmtUpdateShirtSponsor->execute([$_POST["clubID"], $_POST["shirtSponsor"], $_POST["shirtSponsorYear"]]);
                    }

                    if (!$stmtUpdateShirtSponsor) {
                        $_SESSION["message"] = "Error! Could not update Shirt Sponsor";
                        redirect("updateClub.php?id=".urlencode($_POST["clubID"])."");
                    }
                }
                $_SESSION["message"] = "Club has been updated";
                redirect("readMain.php");

            } else {
                $_SESSION["message"] = "Error! Could not update Club";
                redirect("updateClub.php?id=".urlencode($_POST["clubID"])."");
            }

        } else {
            $_SESSION["message"] = "Error! Club Name is already in use.";
			redirect("updateClub.php?id=".urlencode($_POST["clubID"])."");
        }

	} else {
        if (isset($_GET["id"]) && $_GET["id"] !== "") {
			$queryRead = "SELECT ClubName, Stadium, Wins, Draws, Losses, GD, M.FName, M.LName, M.Salary, M.YearsInPosition, (SELECT Spon.SponsorName FROM Sponsors AS Spon WHERE ClubID=? AND Spon.SponsorType='Kit') AS KitSponsor, (SELECT Spon.StartDate FROM Sponsors AS Spon WHERE ClubID=? AND Spon.SponsorType='Kit' ) AS KitSponsorDate, (SELECT Spon.SponsorName FROM Sponsors AS Spon WHERE ClubID=? AND Spon.SponsorType='Shirt') AS ShirtSponsor, (SELECT Spon.StartDate FROM Sponsors AS Spon WHERE ClubID=? AND Spon.SponsorType='Shirt') AS ShirtSponsorDate FROM Club AS C NATURAL JOIN Manager AS M NATURAL JOIN Stats LEFT OUTER JOIN Sponsors AS Spon ON C.ClubID=Spon.ClubID WHERE C.ClubID=?";
			$stmtRead = $mysqli->prepare($queryRead);
			$stmtRead->execute([$_GET["id"], $_GET["id"], $_GET["id"], $_GET["id"], $_GET["id"]]);
            
            if($stmtRead) {
                $rowRead = $stmtRead->fetch(PDO::FETCH_ASSOC);
				echo "<h3>".$rowRead["ClubName"]."</h3>";
                echo "<form action='updateClub.php' method='POST'>";
                    echo "<details open><summary><h5>Club Info</h5></summary>";
                    echo "<input type='hidden' name='clubID' value='".$_GET["id"]."'/>";
                    echo "Club Name:<input type='text' name='clubName' value='".$rowRead["ClubName"]."' />";
                    echo "Stadium Name:<input type='text' name='stadium' value='".$rowRead["Stadium"]."' />";
                    echo "</details>";

                    echo "<details><summary><h5>Stats (Dropdown)</h5></summary>";
                    echo "Wins:<input type='number' name='wins' value='".$rowRead["Wins"]."' step='1' min='0' max='99' />";
                    echo "Draws:<input type='number' name='draws' value='".$rowRead["Draws"]."' step='1' min='0' max='99' />";
                    echo "Losses:<input type='number' name='losses' value='".$rowRead["Losses"]."' step='1' min='0' max='99' />";
                    echo "GD:<input type='number' name='gd' value='".$rowRead["GD"]."' step='1' min='-99' max='99' />";
                    echo "</details>";

                    echo "<details><summary><h5>Manager Info (Dropdown)</h5></summary>";
                    echo "Manager's First Name:<input type='text' name='fname' value='".$rowRead["FName"]."' />";
                    echo "Manager's Last Name:<input type='text' name='lname' value='".$rowRead["LName"]."' />";
                    echo "Salary:<input type='number' name='salary' value='".$rowRead["Salary"]."' step='1' min='0' max='999999999' />";
                    echo "Years in Position:<input type='number' name='managerYear' value='".$rowRead["YearsInPosition"]."' step='1' min='0' max='999999999' />";
                    echo "</details>";

                    echo "<details><summary><h5>Kit Sponsor (Optional)</h5></summary>";
                    echo "Sponsor Name:<input type='text' name='kitSponsor' value='".$rowRead["KitSponsor"]."' />";
                    echo "Starting Year:<input type='number' name='kitSponsorYear' value='".$rowRead["KitSponsorDate"]."' step='1' min='0' max='2023' />";
                    echo "</details>";
                    
                    echo "<details><summary><h5>Shirt Sponsor (Optional)</h5></summary>";
                    echo "Sponsor Name:<input type='text' name='shirtSponsor' value='".$rowRead["ShirtSponsor"]."' />";
                    echo "Starting Year:<input type='number' name='shirtSponsorYear' value='".$rowRead["ShirtSponsorDate"]."' step='1' min='0' max='2023' />";
                    echo "</details>";

                    echo "<input type='submit' name='submit' class='button tiny round' value='Update Club' />";

                echo "</form>";
            } else {
                $_SESSION["message"] = "Club could not be found!";
				redirect("readPlayers.php?id=".urlencode($_POST["clubid"])."");
            }
        }			
	}
	echo "</label>";
	echo "</div>";
	echo "<br /><p>&laquo:<a href='readMain.php'>Back to Main Page</a>";
	echo "</center>";

	new_footer("Premier League 2022/23 Database");	
	Database::dbDisconnect($mysqli);

?>