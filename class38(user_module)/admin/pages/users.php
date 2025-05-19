<?php
$user = new USER();

if(!isset($_SESSION['userSession']))
{
	header('location: login.php');
	exit;
}

//Handle Form Submission

if(isset($_POST['add_user']))
{
	$userName = trim($_POST['username']);
	$userEmail = trim($_POST['email']);
	$userPass = trim($_POST['password']);
	$userType = $_POST['user_type'];

	$hashed_password = password_hash($userPass, PASSWORD_DEFAULT);

	try 
	{
		$check = $user->runQuery("SELECT id FROM tbl_user WHERE userEmail = ?");
		$check->execute([$userEmail]);

		if($check->rowCount() > 0)
		{
			$error = "Email already exists";
		}

		else
		{
			$stmt = $user->runQuery("INSERT INTO tbl_user(userName, userEmail, uerPass, user_type, status, is_active) VALUES (?,?,?,?,'active',1)");
			$stmt->execute([$userName, $userEmail, $hashed_password, $userType]);

			//send mail

			$subject = "Your login credentials";
			$message = "Hello {$userName}, <br><br>Your account has been created. <br> Email: {$userEmail} <br> Password: {$userPass}<br><br>Now you can login with this credential.";
			$user->sendMail($userEmail, $message, $subject);
			$success = "New user added and credentials sent";
		}
	}

	catch (PDOException $e) 
	{
		$error = "Error:".$e->getMessage();
	}
}

$currentAdminId = $_SESSION['userSession'];

//Block/unblock action

if(isset($_GET['toggle_block']))
{
	$userId = intval($_GET['toggle_block']);
	if($userId !== $currentAdminId)
	{
		$user->toggleBlockUser($userId);
		header('location: index.php?page=users');
		exit;
	}
}

//Fetch all users except current logged in user

$stmt = $user->runQuery("SELECT * FROM tbl_user WHERE id != :id ORDER BY id DESC");
$stmt->execute([':id'=>$currentAdminId]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
	<title>Add New User</title>
</head>
<body>

	<div class="container mt-4">
		<?php if(isset($error)):?>
			<div class="alert alert-danger"><?= $error; ?></div>
		<?php elseif(isset($success)): ?>
			<div class="alert alert-success"><?= $success; ?></div>
		<?php endif; ?>

		<h4 class="mb-4">Add New User</h4>
		<form method="post" action="">
			<div class="form-row">
				<div class="form-group col-md-3">
					<input type="text" name="username" class="form-control" placeholder="Username" required>
				</div>
				<div class="form-group col-md-3">
					<input type="email" name="email" class="form-control" placeholder="Email" required>
				</div>
				<div class="form-group col-md-2">
					<select name="user_type" class="form-control" required>
						<option value="">Select User</option>
						<option value="admin">Admin</option>
						<option value="manager">Manager</option>
						<option value="deliveryman">Deliveryman</option>
					</select>
				</div>
				<div class="form-group col-md-2">
					<input type="password" name="password" class="form-control" placeholder="Password" required>
				</div>
				<div class="form-group col-md-2">
					<button type="submit" name="add_user" class="btn btn-success btn-block">Add User</button>
				</div>
			</div>
		</form>

		<hr>

		<h4 class="mt-4">User List</h4>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>#ID</th>
					<th>Username</th>
					<th>Email</th>
					<th>Role</th>
					<th>Status</th>
					<th width="15%">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($users as $u): ?>
					<tr>
						<td><?= $u['id']; ?></td>
						<td><?= htmlspecialchars($u['userName']); ?></td>
						<td><?= htmlspecialchars($u['userEmail']); ?></td>
						<td><?= ucfirst($u['user_type']); ?></td>
						<td>
							<?=
								$u['status'] == 'active' ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>';
							?>
						</td>

						<td>
							<?php if(in_array($u['user_type'], ['admin','manager','deliveryman'])): ?>

								<a href="index.php?page=edit_user&id=<?= $u['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
							<?php endif; ?>

							<a href="index.php?page=users&toggle_block=<?= $u['id']; ?>" class="btn btn-sm <?= $u['status'] == 'active' ? 'btn-warning' : 'btn-success'; ?>" onclick="return confirm('Are you sure to <?= $u['status'] == 'active' ? 'block' : 'unblock'; ?>this user?');"><?= $u['status'] == 'active' ? 'Block' : 'Unblock'; ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</body>
</html>