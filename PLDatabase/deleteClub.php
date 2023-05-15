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
	
  	if (isset($_GET["id"]) && $_GET["id"] !== "")  {

		$queryDelete = "DELETE FROM Club WHERE ClubID=?";
		$stmtDelete = $mysqli->prepare($queryDelete);
		$stmtDelete->execute([$_GET["id"]]);

		if ($stmtDelete) {
			$_SESSION["message"] = "Club has been deleted";

		}
		else {
			$_SESSION["message"] = "Club could not be deleted";
		}
		redirect("readMain.php");
					
	}
	else {
		$_SESSION["message"] = "Club could not be found!";
		redirect("readMain.php");
	}	
			
	new_footer("Premier League 2022/23 Database");	
	Database::dbDisconnect($mysqli);


?>