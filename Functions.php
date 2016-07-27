<?php


function connect(){
	$conn = new mysqli("localhost", "web", "L69992772");
	return $conn;
}
function printmenu($conn){
	echo "Welcome to the H&E Databanks, please select what you would like to do.";
			

//Mode 1	X Payroll Related Tasks:
//	Task 1	X View Payroll: For this week / A Different Week
//	Task 2	X Register hours: Agent Dropdown: Hour box : For this week / A different Week
//	Task 3	X Modify Previous entries: For this week / A Different Week
//	Submit	Submit
//			
//Mode 2	HR related tasks:
//	Task 1	X View Current Employees Profiles: Employee Dropdown
//	Task 2	X Add a new Employee
//	Task 3	X Modify/Terminate an Employee
//	Task 4	X Upload a Drivers License or other file
//	Submit	Submit

	echo "<br><br>
			<form action='home.php' method='post'>Payroll Related Tasks:
			<input type='text' name='Mode' value='1' hidden>
			<br><input type='radio' name='Task' value='1'>View Payroll
			<br><input type='radio' name='Task' value='2'>Register hours
			<br><input type='radio' name='Task' value='3'>Modify Previous entries
			<br>Employee:";
	printAgentsList($conn);
	echo "<br>Week:";
	printWeeksList($conn);
	echo "<br><input type='submit' value='Submit'><br><br>
			</form>";
	
	echo "<form action='home.php' method='post'>HR related tasks:
			<input type='text' name='Mode' value='2' hidden>
			<br><input type='radio' name='Task' value='1'>View Current Employees Profiles
			<br><input type='radio' name='Task' value='2'>Add a new Employee FN:<input type='text' name='FN'>
			LN:<input type='text' name='LN'> SIN:<input type='text' name='SIN'> Pnum:<input type='text' name='Pnum'>
			<br><input type='radio' name='Task' value='3'>Modify/Terminate an Employee
			<br><input type='radio' name='Task' value='4'>Upload a Drivers License or other file
			<br>Employee:";
	printAgentsList($conn);
	echo"<br><input type='submit' value='Submit'>
			</form>";
			
}
function printAgentsList($conn){
	$sql="SELECT Fname,Lname,IDKey FROM workers";
	$result = $conn->query($sql);
	echo "<select name='Agent'>";
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$ID=$row["IDKey"];
			$FN=$row["Fname"];
			$LN=$row["Lname"];
			echo "<option value='$ID'>$FN $LN</option>";
		}
	}
	echo "</select>";
}
function printWeeksList($conn){
	$sql="SELECT Edage,Sdate,IDKey FROM weeks";
	$result = $conn->query($sql);
	echo "<select name='Week'>";
	echo "<option value='0'>This Week</option>";
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$ID=$row["IDKey"];
			$SDD=$row["Sdate"];
			$ED=$row["Edate"];
			echo "<option value='$ID'>$SD-$ED</option>";
		}
	}
	echo "</select>";
}

?>