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

	$query = "SELECT S.SponsorName AS Sponsor, GROUP_CONCAT(C.ClubName ORDER BY C.ClubName) AS Clubs FROM Sponsors AS S INNER JOIN Club AS C ON C.ClubID = S.ClubID WHERE C.ClubID IN (SELECT C2.ClubID FROM Club AS C2 NATURAL JOIN Stats WHERE Stats.Wins>Stats.Losses) GROUP BY S.SponsorName ORDER BY S.SponsorName ASC";
	$stmt = $mysqli->prepare($query);
	$stmt -> execute();

	if ($stmt) {

		echo "<div class='row'>";
		echo "<center>";
		echo "<h3>Nested Query: Sponsors Whose Clubs Have More Wins Than Losses</h3>";
		echo "<table>";
		echo "  <thead>";
		echo " <tr>
					<th></th>
					<th>Sponsor</th>
					<th>List of Clubs</th>
					<th></th>
				</tr>";
		echo "  </thead>";
		echo "  <tbody>";
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$sponsor = $row['Sponsor'];
			$clubs = $row['Clubs'];

			echo "<tr>";
			echo "<td></td>";
            echo "<td>".$sponsor."</td>";
            echo "<td>".$clubs."</td>";
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