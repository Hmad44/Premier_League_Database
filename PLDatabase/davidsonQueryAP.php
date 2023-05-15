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

	$query = "SELECT ClubName, GROUP_CONCAT(CONCAT(' ',FName, ' ', LName) ORDER BY LName) AS 'Player Names Ordered by Last Name' FROM Club NATURAL JOIN Players GROUP BY ClubName ORDER BY ClubName";
	$stmt = $mysqli->prepare($query);
	$stmt -> execute();

	if ($stmt) {

		echo "<div class='row'>";
		echo "<center>";
		echo "<h3>Dr. Davidson's Query: Managers and Every Player of Their Respective Club</h3>";
		echo "<table>";
		echo "  <thead>";
		echo " <tr>
					<th></th>
					<th>Club</th>
					<th>Player Names Ordered by Last Name</th>
					<th></th>
				</tr>";
		echo "  </thead>";
		echo "  <tbody>";
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$club = $row['ClubName'];
			$players = $row['Player Names Ordered by Last Name'];

			echo "<tr>";
			echo "<td></td>";
            echo "<td>".$club."</td>";
            echo "<td>".$players."</td>";
			echo "</tr>";
		}
		echo "  </tbody>";
		echo "</table>";
		echo "</center>";
		echo "</div>";
	}
	echo "<center><br /><p>&laquo:<a href='readMain.php'>Back to Main Page</a></center>";

	new_footer("Premier League 2022/23 Database");	
	Database::dbDisconnect($mysqli);
 ?>