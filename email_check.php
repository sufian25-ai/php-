<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'wdpf64';

$dbcon = new PDO("mysql:host={$host};dbname={$dbname}",$user,$pass);

if($_POST)
{
	$name = strip_tags($_POST['email']);
	$stmt = $dbcon->prepare("SELECT userEmail FROM tbl_user WHERE userEmail = :email");
	$stmt->execute(array(':email'=>$name));
	$count = $stmt->rowCount();
	if($count > 0)
	{
		echo '<span style="color:brown;">Sorry, this email already exists</span>';
	}

	else
	{
		echo '<span style="color:green;">Available</span>';
	}
}


?>