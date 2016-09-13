<head>
<title>H&E Payroll</title>
<link rel="stylesheet" type="text/css" href="main.css">
</head>

<body>
<?php
require "Functions.php";
$conn=connect();

if( isset($_POST['Mode']) ){
	$Mode=$_POST['Mode'];
	if($Mode==1){//Payroll tasks
	$Task=$_POST["Task"];
		if($Task==1){//View a payroll for a specified week
				$week=$_POST["Week"];
				echo "Here is the payroll for that week #$week.<table>";
				printpayrollbyweek($week,$conn);
				echo "</table>";
		}else if($Task==2){//Add hours to the payroll record
			$hours=$_POST["hours"];
			$day=$_POST["day"];
			$week=getweekfromday($day,$conn);
			$agent=$_POST["Agent"];
			$sql="INSERT INTO hourlog (WeekID,AgentID,Hours,Day) VALUES ('$week','$agent','$hours','$day')";
			$conn->query($sql);
			echo $sql;
			echo "Hours have been registered in payroll databanks as per request.";
		}else if($Task==3){//Modify existing entries
			
		}
	}else if($Mode==2){//HR tasks
		if($Task==1){//View Employee profiles
			
		}else if($Task==2){//Add a new employee
			$fn=$_POST["FN"];
			$ln=$_POST["LN"];
			$sin=$_POST["SIN"];
			$pnum=$_POST["Pnum"];
			$payrate=$_POST["Pay"];
		}else if($Task==3){//Modify an employee
			
		}else if($Task==4){//Upload an image and attach to employee
			
		}
	}
}

printmenu($conn);



?>
</body>