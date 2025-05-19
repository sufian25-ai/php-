<?php

require_once 'class.user.php';

$user = new USER();

$userName = 'Admin';

if(isset($_SESSION['userSession']))
{
	$stmt = $user->runQuery("SELECT userName, user_type FROM tbl_user WHERE id = :uid");
	$stmt->execute([':uid'=>$_SESSION['userSession']]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if($row)
	{
		$userName = htmlspecialchars($row['userName']);
		$userType = htmlspecialchars($row['user_type']);
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin Panel</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" integrity="sha512-rt/SrQ4UNIaGfDyEXZtNcyWvQeOq0QLygHluFQcSjaGB04IxWhal71tKuzP6K8eYXYB6vJV4pHkXcmFGGQ1/0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<style type="text/css">
		body
		{
			
			font-family: 'Arial', sans-serif;
		}

		.navbar-brand
		{
			font-weight: bold;
		}

		.sidebar
		{
			min-height: 100vh;
			background-color: #f8f9fa;
			border-right: 1px solid #dee2e6;
		}

		.sidebar .nav-link .active
		{
			background-color: #007bff;
			color: white !important;
		}

		.content-area
		{
			padding: 20px;
		}

	</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<a href="index.php" class="navbar-brand">Admin Panel</a>
		<div class="ml-auto d-flex align-items-center">
		<form class="form-inline mr-3">
			<input type="search" name="" class="form-control form-control-sm mr-sm-2" placeholder="search...">
			<button class="btn btn-sm btn-outline-light" type="submit">search</button>
		</form>

		<div class="dropdown">
			<a href="#" class="dropdown-toggle text-white d-flex align-item-center" data-toggle="dropdown">
				<img src="assets/images/admin.jpg" alt="admin" width="30" height="30" class="rounded-circle mr-2">

				<?php echo ($userType == 'admin') ? $userName : $userType ?>
			</a>
			<div class="dropdown-menu dropdown-menu-right">
				<a href="index.php?page=profile" class="dropdown-item">Profile</a>
				<div class="dropdown-divider"></div>
				<a href="logout.php" class="dropdown-item text-danger">Logout</a>
			</div>
		</div>
	</div>
</nav>

<div class="container-fluid">
	<div class="row">

