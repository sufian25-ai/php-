<?php

//require __DIR__. '/../dbConfig.php';
//require __DIR__. '/../class.user.php';


$USER = new USER();
$errmsg = '';
$successmsg = '';

//Fetch Categories

$cat_stmt = $USER->runQuery("SELECT * FROM categories");
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['btnsave']))
{
	$productname = $_POST['product_name'];
	$description = $_POST['description'];
	$productstock = $_POST['product_stock'];
	$price = $_POST['price'];
	$category_id = $_POST['category_id'];
	$has_attributes = isset($_POST['has_attributes']) ? 1 : 0;
	$sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : '';
	$colors = isset($_POST['colors']) ? $_POST['colors'] : '';

	//Image uploads

	$imgfile = $_FILES['product_image']['name'];
	$tmp_dir = $_FILES['product_image']['tmp_name'];
	$imgsize = $_FILES['product_image']['size'];

	if(empty($productname) || empty($description) || empty($imgfile) || empty($productstock))
	{
		$errmsg = "All fields are required!";
	}

	else
	{
		$upload_dir = "pages/uploads/";
		$imgext = strtolower(pathinfo($imgfile, PATHINFO_EXTENSION));
		$valid_extensions = ['jpg','jpeg','png','gif'];
		$productpic = rand(1000, 1000000000).".".$imgext;

		if(in_array($imgext, $valid_extensions) && $imgsize < 5000000)
		{
			move_uploaded_file($tmp_dir, $upload_dir.$productpic);
		}

		else
		{
			$errmsg = "Invalid image file or size";
		}
	}

	if(empty($errmsg))
	{
		$stmt = $USER->runQuery("INSERT INTO products (product_name,description,product_image,price,stock_amount,has_attributes,category_id) VALUES (:pname,:pdesc,:ppic,:pprice,:pstock,:hasattr,:cat_id)");

		$stmt->bindParam(':pname',$productname);
		$stmt->bindParam(':pdesc',$description);
		$stmt->bindParam(':ppic',$productpic);
		$stmt->bindParam(':pprice',$price);
		$stmt->bindParam(':pstock',$productstock);
		$stmt->bindParam(':hasattr',$has_attributes);
		$stmt->bindParam(':cat_id',$category_id);

		if($stmt->execute())
		{
			$lastProductId = $USER->lastID();

			if($has_attributes)
			{
				$attr_stmt = $USER->runQuery("INSERT INTO attributes (product_id,sizes,colors) VALUES (:pid,:sizes,:colors)");

				$attr_stmt->bindParam(':pid',$lastProductId);
				$attr_stmt->bindParam(':sizes',$sizes);
				$attr_stmt->bindParam(':colors',$colors);
				$attr_stmt->execute();
			}

			//Fetch all registered users

			$user_stmt = $USER->runQuery("SELECT userEmail FROM tbl_user WHERE is_active = 1");

			$user_stmt->execute();
			$users = $user_stmt->fetchAll(PDO::FETCH_ASSOC);

			foreach($users as $user)
			{
				$email = $user['userEmail'];

				//Email Template

				$subject = "New Product Available: ".$productname;
				$message = "

							<html>
								<head>
									<title>
										New Product Notification
									</title>
								</head>
								<body>
									<h2>New Product Added!</h2>
									<p><b>Product Name:</b>{$productname}</p>
									<p><b>Details:</b>{$description}</p>
									<p><b>Price:</b>{$price}</p>
									<p>
										<img src='http://localhost/wdpf-64/class32(new_product_email-notification)/admin/pages/uploads/{$productpic}' alt='{$productpic}' style='width:150px;'>
									</p>
									<p>visit our store to see more</p>
								</body>
							</html>";

							//Database Notification

							foreach ($users as $user) 

							{
								//Get user id

								$user_info = $USER->runQuery("SELECT id FROM tbl_user WHERE userEmail = :email");

								$user_info->execute([':email'=>$email]);
								$user_data = $user_info->fetch(PDO::FETCH_ASSOC);
								$user_id = $user_data['id'];

								//Insert Notification

								$notif_stmt = $USER->runQuery("INSERT INTO notifications (user_id, product_id, message) VALUES (:uid, :pid, :msg)");

								$notif_stmt->execute([

										':uid'=>$user_id,
										':pid'=>$lastProductId,
										':msg'=>"New product added: ".$productname
								]);
							}

							$USER->sendMail($email,$message,$subject);


			}

			$successmsg = "New Product Inserted Successfully & Email Notified";
			header("refresh: 5; index.php?page=products");
		}

		else
		{
			$errmsg = "Error while inserting";
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Add New Products</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha512-Dop/vW3iOtayerlYAqCgkVr2aTr2ErwwTYOvRFUpzl2VhCMJyjQF0Q9TjUXIo6JhuM/3i0vVEt2e/7QQmnHQqw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<div class="container mt-5">
	<h3 class="mb-4">Add Products</h3>

	<?php if(!empty($errmsg)) echo "<div class='alert alert-danger'>$errmsg</div>";?>

	<?php if(!empty($successmsg)) echo "<div class='alert alert-success'>$successmsg</div>";?>

	<form method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label>Product Name:</label>
			<input type="text" name="product_name" class="form-control" required>
		</div>

		<div class="form-group">
			<label>Description:</label>
			<textarea name="description" class="form-control" rows="3" required></textarea>
		</div>

		<div class="form-group">
			<label>Product Image:</label>
			<input type="file" name="product_image" class="form-control" required>
		</div>

		<div class="form-group">
			<label>Price:</label>
			<input type="number" name="price" class="form-control" required>
		</div>

		<div class="form-group">
			<label>Stock Amount:</label>
			<input type="number" name="product_stock" class="form-control" required>
		</div>

		<div class="form-group">
			<label>Category:</label>
			<select name="category_id" class="form-control" required>
				<option value="">Select Category</option>

				<?php

					foreach ($categories as $cat) : ?>
						<option value="<?= $cat['id'];?>"><?= htmlspecialchars($cat['category_name']) ?></option>
					<?php endforeach; ?>
			</select>
		</div>

		<div class="form-check mb-3">
			<input type="checkbox" name="has_attributes" class="form-check-input" id="hasAttributes" onchange="toggleAttributes()">
			<label class="form-check-label">Has Attributes?</label>
		</div>

		<div id="attributeSection" style="display: none;">
			<div class="form-group">
				<label>Sizes:</label>
				<label class="checkbox-inline mr-2"><input type="checkbox" name="sizes[]" value="L">L</label>
				<label class="checkbox-inline mr-2"><input type="checkbox" name="sizes[]" value="XL">XL</label>
				<label class="checkbox-inline mr-2"><input type="checkbox" name="sizes[]" value="XXL">XXL</label>
			</div>

			<div class="form-group">
				<label>Colors:</label>
				<input type="color"  class="color-input">
				<button type="button" class="btn btn-sm btn-secondary" onclick="addColor()">Add Color</button>
				<div id="colorList" class="mt-2"></div>
				<input type="hidden" name="colors" id="colors">
			</div>
		</div>
		<button type="submit" name="btnsave" class="btn btn-success">Save</button>
	</form>
</div>
</body>
</html>

<script type="text/javascript">
	
	let selectedColors = [];

	function toggleAttributes()
	{
		const attrSection = document.getElementById('attributeSection');
		attrSection.style.display = document.getElementById('hasAttributes').checked ? 'block' : 'none';
	}

	function addColor()
	{
		const colorInput = document.querySelector('.color-input');
		const color = colorInput.value;

		if(!selectedColors.includes(color))
		{
			selectedColors.push(color);
			updateColorList();
		}
	}

	function updateColorList()
	{
		const colorList = document.getElementById('colorList');
		const colorInput = document.getElementById('colors');
		colorList.innerHTML = '';

		selectedColors.forEach((color, index) => {

			const colorBox = document.createElement('div');
			colorBox.style.display = 'inline-block';
			colorBox.style.backgroundColor = color;
			colorBox.style.width = '30px';
			colorBox.style.height = '30px';
			colorBox.style.marginRight = '5px';
			colorBox.style.border = '1px solid #000';
			colorBox.title = color;
			colorBox.onclick = () => {

				selectedColors.splice(index, 1);
				updateColorList();
			};

			colorList.appendChild(colorBox);

		});

		colorInput.value = selectedColors.join(',')

	}
</script>