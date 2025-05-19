<?php

require_once 'class.user.php';

$user = new USER();

$conn = $user->getConnection();

if(!$user->is_logged_in())
{
	header("location: login.php");
}

//Fetch Products

$stmt = $user->runQuery("SELECT id, product_name FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


if(isset($_POST['submit_stock']))
{
	$product_id = $_POST['product_id'];

	$stock_in =  $_POST['stock_in'];

	try 
	{
		$conn->beginTransaction();

		$stmt = $conn->prepare("INSERT INTO stocks (product_id, stock_in) VALUES (?,?)");

		$stmt->execute([$product_id, $stock_in]);

		$updateStmt = $conn->prepare("UPDATE products SET stock_amount =  stock_amount + ? WHERE id = ?");
		 $updateStmt->execute([$stock_in, $product_id]);

		 $pstmt = $conn->prepare("SELECT product_name FROM products WHERE id = ?");
		 $pstmt->execute([$product_id]);
		 $product = $pstmt->fetch(PDO::FETCH_ASSOC);

		 $userStmt = $conn->prepare("SELECT userEmail FROM tbl_user WHERE user_type IN('manager','user')");
		 $userStmt->execute();
		 $emails = $userStmt->fetchAll(PDO::FETCH_COLUMN);

		 $subject = "New Stock Update: {$product['product_name']}";
		 $message = "Stck of '{$product['product_name']}' has been updated. New stock added: {$stock_in}";

		 foreach($emails as $email)
		 {
		 	$user->sendMail($email, $message, $subject);
		 }

		 $conn->commit();
		 $success = "Stock updated successfully and notification sent";
	} 
	catch (PDOException $e) 
	{
		$conn->rollback();
		$error = "Error:".$e->getMessage();
	}
}


?>

<div class="container mt-4">
	<h4>New Stock Entry</h4>
		<?php if(isset($success)) echo "<div class='alert alert-success'>{$success}</div>"; ?>

		<?php if(isset($error)) echo "<div class='alert alert-danger'>{$error}</div>"; ?>
	<form method="post">
		<div class="form-group">
			<label>Select Products</label>
			<select name="product_id" id="product_id" class="form-control" required>
				<option value="">---select---</option>
				
				<?php
					foreach($products as $p):?>
						<option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['product_name']) ?></option>
					<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<label>Stock Amount</label>
			<input type="number" name="stock_in" id="stock_in" class="form-control" required min="1">
		</div>

		<button type="submit" name="submit_stock" class="btn btn-primary">Add Stock</button>
	</form>
</div>