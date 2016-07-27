<head>
<title>H&E Payroll</title>
</head>

<body>
<?php
require "Functions.php";
$conn=connect();

if( isset($_POST['Mode']) ){
	$Mode=$_POST['Mode'];
	
}

printmenu($conn);



?>
</body>