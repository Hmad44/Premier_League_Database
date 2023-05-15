<?php 
	require_once("session.php"); 
	require_once("included_functions.php");
	require_once("database.php");

	new_header("Premier League"); 
	$mysqli = Database::dbConnect();
	$mysqli -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if (($output = message()) !== null) {
		echo $output;
	}

	$query = "SELECT ShirtNum, P.FName AS 'First Name', P.LName AS 'Last Name', floor(datediff(now(), DOB) / 365.25) AS Age, DOB, PrimaryPos, FORMAT(P.Salary, 'C') AS Player_Salary FROM Players AS P WHERE ClubID = ? ORDER BY ShirtNum ASC";
	$stmt = $mysqli->prepare($query);
	$stmt -> execute([$_GET['id']]);

	if ($stmt) {

        $name_query = "SELECT ClubName FROM Club WHERE ClubID = ?";
        $name_stmt = $mysqli->prepare($name_query);
        $name_stmt -> execute([$_GET['id']]);
        $name_row = $name_stmt->fetch(PDO::FETCH_ASSOC);

		echo "<div class='row'>";
		echo "<center>";
		echo "<h2>".$name_row['ClubName']." Players</h2>";
		echo "<table>";
		echo "  <thead>";
		echo " <tr>
					<th></th>
					<th>Shirt Number</th>
					<th>First Name</th>
					<th>Last Name</th>
                    <th>Age</th>
                    <th>Date of Birth</th>
                    <th>Position</th>
                    <th>Salary</th>
					<th></th>
				</tr>";
		echo "  </thead>";
		echo "  <tbody>";
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$num = $row['ShirtNum'];
			$fname = $row['First Name'];
			$lname = $row['Last Name'];
            $age = $row['Age'];
            $dob = $row['DOB'];
            $pos = $row['PrimaryPos'];
            $salary = $row['Player_Salary'];

			echo "<tr>";
			echo "<td><a href='deletePlayer.php?id=".urlencode($_GET['id'])."&num=".urlencode($num)."' onclick='return confirm(\"Are you sure you wish to delete?\");' style='color:red'>X</a></td>";
			echo "<td>".$num."</td>";
			echo "<td>".$fname."</td>";
			echo "<td>".$lname."</td>";
            echo "<td>".$age."</td>";
            echo "<td>".$dob."</td>";
            echo "<td>".$pos."</td>";
            echo "<td>$".$salary."</td>";
			echo "<td><a href='updatePlayer.php?id=".urlencode($_GET['id'])."&num=".urlencode($num)."' onclick='return confirm(\"Are you sure you want to edit?\");'>Edit</a></td>";
			
			echo "</tr>";
		}
		echo "  </tbody>";
		echo "</table>";

		echo "<a href='createPlayer.php?id=".urlencode($_GET['id'])."'> Add a Player </a>";

		echo "</center>";
		echo "</div>";
	}
	echo "<center><br /><p>&laquo:<a href='readMain.php'>Back to Main Page</a></center>";

	new_footer("Premier League 2022/23 Database");	
	Database::dbDisconnect($mysqli);
 ?>