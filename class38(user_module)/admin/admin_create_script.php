<?php

try 
{
	$conn = new PDO("mysql:host=localhost;dbname=wdpf64","root","");
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$password = password_hash('zahir', PASSWORD_DEFAULT);

	$sql = "INSERT INTO tbl_user (userName, userEmail, uerPass, status, tokenCode, user_type, is_active) VALUES (:uname, :email, :pass, 'active','','deliveryman',1)";

	$stmt = $conn->prepare($sql);
	$stmt->execute([

		':uname' => 'zahir',
		':email' => 'deliveryman@gmail.com',
		':pass' => $password
	]);

	echo "deliveryman user created successfully";
} 
catch (PDOException $e) 
{
	echo "DB Error:".$e->getMessage();
}

?>