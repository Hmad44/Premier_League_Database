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
	
  	if ( (isset($_GET["id"]) && $_GET["id"] !== "") 
        && (isset($_GET["num"]) && $_GET["num"] !== "")) {

		$queryDelete = "DELETE FROM Players WHERE ClubID=? AND ShirtNum=?";
		$stmtDelete = $mysqli->prepare($queryDelete);
		$stmtDelete->execute([$_GET["id"], $_GET["num"]]);

		if ($stmtDelete) {
			$_SESSION["message"] = "Player has been deleted";

		}
		else {
			$_SESSION["message"] = "Player could not be deleted";
		}
		redirect("readPlayers.php?id=".urlencode($_GET['id'])."");
					
	}
	else {
		$_SESSION["message"] = "Player could not be found!";
		redirect("readPlayers.php?id=".urlencode($_GET['id'])."");
	}	
			
	new_footer("Premier League 2022/23 Database");	
	Database::dbDisconnect($mysqli);


?>