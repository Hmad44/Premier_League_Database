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

	$query = "SELECT CONCAT(M.FName, ' ', M.LName) AS Manager, GROUP_CONCAT(CONCAT(P.FName, ' ', P.LName) ORDER BY P.LName ASC) AS Players FROM Manager AS M INNER JOIN Club AS C ON C.ClubID = M.ClubID INNER JOIN Players AS P ON P.ClubID = C.ClubID GROUP BY M.LName ORDER BY M.LName ASC";
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
					<th>Manager</th>
					<th>List of Players</th>
					<th></th>
				</tr>";
		echo "  </thead>";
		echo "  <tbody>";
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$manager = $row['Manager'];
			$players = $row['Players'];

			echo "<tr>";
			echo "<td></td>";
            echo "<td>".$manager."</td>";
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