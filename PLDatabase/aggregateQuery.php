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

	$query = "SELECT ClubName, FORMAT(AVG(Salary), 'C') AS 'Average Salary' FROM Club NATURAL JOIN Players GROUP BY ClubName ORDER BY AVG(Salary) DESC;";
	$stmt = $mysqli->prepare($query);
	$stmt -> execute();

	if ($stmt) {

		echo "<div class='row'>";
		echo "<center>";
		echo "<h3>Aggregate Query: Average Salary of A Player for Each Club</h3>";
		echo "<table>";
		echo "  <thead>";
		echo " <tr>
					<th></th>
					<th>Club</th>
					<th>Average Salary (USD)</th>
					<th></th>
				</tr>";
		echo "  </thead>";
		echo "  <tbody>";
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$club = $row['ClubName'];
			$avgSalary = $row['Average Salary'];

			echo "<tr>";
			echo "<td></td>";
            echo "<td>".$club."</td>";
            echo "<td>$".$avgSalary."</td>";
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