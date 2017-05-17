<html>
<body>
<?php


function connect(){
	$conn = new mysqli("localhost", "web", "L69992772","main");
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
			<br><input type='radio' name='Task' value='2'>Register hours #of hours:<input type='number' name='hours'> Day:<input type='date' name='day'>
			<br><input type='radio' name='Task' value='3'>Modify Previous entries(offline)
			<br>Employee:";
	printAgentsList($conn);
	echo "<br>Week:";
	printWeeksList($conn);
	echo "<br><input type='submit' value='Submit'><br><br>
			</form>";
	
	echo "<form action='home.php' method='post'>HR related tasks:
			<input type='text' name='Mode' value='2' hidden>
			<br><input type='radio' name='Task' value='1'>View Current Employees Profiles(offline)
			<br><input type='radio' name='Task' value='2'>Add a new Employee FN:<input type='text' name='FN'>
			LN:<input type='text' name='LN'> SIN:<input type='text' name='SIN'> Pnum:<input type='text' name='Pnum'>
			<br><input type='radio' name='Task' value='3'>Modify/Terminate an Employee(offline)
			<br><input type='radio' name='Task' value='4'>Upload a Drivers License or other file(offline)
			<br>Employee:";
	printAgentsList($conn);
	echo "Payrate:<input type='number' value='0' name='Pay'>";
	echo"<br><input type='submit' value='Submit'>
			</form>";
	//Begin payroll printing
	$week=getcurrentpayrollweek($conn);
	echo "Current Payroll Information!<table>";
	printpayrollbyweek($week,$conn);
	echo "</table>";
}
function getAgentList($conn){
	$sql="SELECT Fname,Lname,IDKey FROM workers";
	$result = $conn->query($sql);
	return $result;
}
function printAgentsList($conn){
	$list=getAgentList($conn);
	echo "<select name='Agent'>";
	if ($list->num_rows > 0) {
		while($row = $list->fetch_assoc()) {
			$ID=$row["IDKey"];
			$FN=$row["Fname"];
			$LN=$row["Lname"];
			echo "<option value='$ID'>$FN $LN</option>";
		}
	}
	echo "</select>";
}
function printWeeksList($conn){
	$sql="SELECT Edate,Sdate,IDKey FROM weeks";
	$result = $conn->query($sql);
	echo "<select name='Week'>";
	echo "<option value='0'>This Week</option>";
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$ID=$row["IDKey"];
			$SD=$row["Sdate"];
			$ED=$row["Edate"];
			echo "<option value='$ID'>$SD - $ED</option>";
		}
	}
	echo "</select>";
}
function printpayrollbyweek($week,$conn){
	echo "<tr><th>Agent</th><th>Gross</th><th>CPP</th><th>EI</th><th>Prov</th><th>Fed</th><th>Total Deduct</th><th>Net Pay</th></tr>";
	$list=getAgentList($conn);
	if ($list->num_rows > 0) {
		while($row = $list->fetch_assoc()) {
			$agent=$row["IDKey"];
			$FN=$row["Fname"];
			$LN=$row["Lname"];
				
			echo "<tr><td>$FN $LN</td>";
			calculateDeductions($week,$agent,$conn);
		}
	}
}
function calculateDeductions($week,$agent,$conn){
	$weekstart=round(getweekearned($agent,$week,$conn),2);
	$cpp=round(calculateCPP($weekstart),2);
	$EI=round(calculateEI($weekstart),2);
	$prov=round(calculateprov($weekstart),2);
	$fed=round(calculatefed($weekstart),2);
	$weekfinish=round($weekstart-$cpp-$EI-$prov-$fed,2);
	$deductionstotal=round($cpp+$EI+$prov+$fed,2);
	echo "<td>$weekstart</td><td>$cpp</td><td>$EI</td><td>$prov</td><td>$fed</td><td>$deductionstotal</td><td><b>$weekfinish<b></td></tr>";
	
}
function calculateCPP($weekstart){
	//Calculate the CPP pay period exemption
	$exemptyear=3500;
	$exemptpay=3500/52;
	//Subtract the pay period exemption from the total
	$minusexempt=$weekstart-$exemptpay;
	//Calculate 4.95% of the remaining as what should be contributed to CPP
	$cpp=$minusexempt*0.0495;
	//Return the calculated amount
	if($cpp<0){
		return 0;
	}
	return $cpp;
}
function calculateEI($weekstart){
	$EI=$weekstart*0.0163;
	return $EI;
}
function calculateprov($weekstart){
	if($weekstart>0){
		$weekstart=$weekstart-319;
		$prov=$weekstart*0.0505;
		return $prov;
	}
	return 0;
}
function calculatefed($weekstart){
	if($weekstart>0){
		$weekstart=$weekstart-279;
		$fed=$weekstart*0.15;
		return $fed;
	}
	return 0;
}
function getweekearned($agent,$week,$conn){
	$monday=getweekstart($conn,$week);
	//Prepare statements to get the total hours worked in a week, and the crrent payrate of the employee
	$sql="SELECT SUM(hours) AS hours FROM hourlog WHERE WeekID='$week' AND AgentID='$agent'";
	$sql2="SELECT payrate FROM states WHERE AgentID='$agent' AND Date<'$monday' ORDER BY IDKey DESC";
	//Request data from the server
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();
	//Calculate the totals
	$total=$row["hours"]*$row2["payrate"]*1.04;
	return $total;
}
function getweekstart($conn,$week){
	$sql="SELECT * FROM weeks WHERE IDKey='$week'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	return $row["Sdate"];
}
function getcurrentpayrollweek($conn){
	$today=getcurrentdate();
	$date = new DateTime($today);
	//$date->modify('-1 week');
	$lastweek=$date->format('Y-m-d');
	
	$sql="SELECT * FROM weeks WHERE Sdate<='$lastweek' ORDER BY IDKey DESC LIMIT 1";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		return $row["IDKey"];
	}
	return 0;
}
function getcurrentdate(){
	$today = date("Y-m-d");
	return $today;
}
function addemployee(){
	
}
function getweekfromday($day,$conn){
	$sql="SELECT * FROM weeks WHERE Sdate<='$day' AND Edate>='$day'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	return $row["IDKey"];
}
function registeremployee($fn,$ln,$sin,$pnum,$pay,$conn){
	$day=getcurrentdate();
	$sql="INSERT INTO workers () VALUES ()";
	$conn->query($sql);
	echo "new employee has been registered";
}











?>
</body>
</html>