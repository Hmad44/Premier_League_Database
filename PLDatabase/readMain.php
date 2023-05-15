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
	$queryRead = "SELECT C.ClubID, (S.Wins*3+S.Draws*1) AS Points, ClubName, Stadium, CONCAT(M.FName,' ', M.LName) AS Manager FROM Club AS C NATURAL JOIN Manager AS M NATURAL JOIN Stats AS S GROUP BY ClubID ORDER BY Points DESC, GD DESC";
	$stmtRead = $mysqli->prepare($queryRead);
	$stmtRead -> execute();

	if ($stmtRead) {
		echo "<div class='row'>";
		echo "<center>";
		echo "<h2>Premier League 2022/23 Database</h2>";
		echo "<table>";
		echo "  <thead>";
		echo " <tr>
					<th></th>
					<th>Standing</th>
					<th>Points</th>
					<th>Club Name</th>
					<th>Stadium</th>
					<th>Manager</th>
					<th>Players</th>
					<th></th>
				</tr>";
		echo "  </thead>";
		echo "  <tbody>";
		
		$count = 1;
		while($rowRead = $stmtRead->fetch(PDO::FETCH_ASSOC)) {	
			$points = $rowRead['Points'];
			$clubname = $rowRead['ClubName'];
			$stadium = $rowRead['Stadium'];
			$manager = $rowRead['Manager'];
			$players = $rowRead['Players'];

			echo "<tr>";
			echo "<td><a href='deleteClub.php?id=".urlencode($rowRead['ClubID'])."' onclick='return confirm(\"Are you sure you want to delete?\");' style='color:red'>X</a></td>";
			echo "<td>".$count."</td>";
			echo "<td>".$points."</td>";
			echo "<td>".$clubname."</td>";
			echo "<td>".$stadium."</td>";
			echo "<td>".$manager."</td>";
			echo "<td><a href='readPlayers.php?id=".urlencode($rowRead['ClubID'])."');'> View Players </a></td>";
			echo "<td><a href='updateClub.php?id=".urlencode($rowRead['ClubID'])."' onclick='return confirm(\"Are you sure you want to edit?\");'> Edit </a></td>";
			
			echo "</tr>";
			$count += 1;
		}
		echo "  </tbody>";
		echo "</table>";

		echo "<a href='createClub.php'> Add a Club </a>";
		echo "<br></br><br></br>";
		echo "<a href='aggregateQuery.php'> Aggregate Query: Average Salary of A Player for Each Club </a>";
		echo "<br></br>";
		echo "<a href='nestedQuery.php'> Nested Query: Sponsors Whose Clubs Have More Wins Than Losses</a>";
		echo "<br></br>";
		echo "<a href='davidsonQueryAM.php'> Dr. Davidson's Query (Ahmed): Managers and Every Player of Their Respective Club</a>";
		echo "<br /><br />";
		echo "<a href='davidsonQueryAP.php'> Dr. Davidson's Query (Albert): Managers and Every Player of Their Respective Club</a>";

		echo "</center>";
		echo "</div>";
	}

	new_footer("Premier League 2022/23 Database");	
	Database::dbDisconnect($mysqli);
 ?>
