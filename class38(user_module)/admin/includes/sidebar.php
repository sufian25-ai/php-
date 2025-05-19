<?php

$userType = $_SESSION['user_type'] ?? '';

?>

<div class="col-md-2 sidebar p-0">
	<ul class="nav flex-column nav-pills p-3">
		<li class="nav-item">
			<a class="nav-link" 
				href="index.php?page=dashboard">Dashboard</a>
		</li>
<!-- Admin and Manager Account -->
	<?php if($userType == 'admin' || $userType == 'manager'): ?>
		<li class="nav-item">
			<a class="nav-link"
			 href="index.php?page=categories">Categories</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" 
				href="index.php?page=products">Manage Products</a>
		</li>



		<li class="nav-item">
			<a class="nav-link" 
				href="index.php?page=stock">Manage Stock</a>
		</li>
		

		<li class="nav-item">
			<a class="nav-link"
			 href="index.php?page=customers">Customers</a>
		</li>

	<?php endif; ?>

	<!-- Admin, Manager and Deliveryman -->
	<?php if(in_array($userType, ['admin','manager','deliveryman'])): ?>

		<li class="nav-item">
			<a class="nav-link"  
				href="index.php?page=orders">Orders</a>
		</li>

	<?php endif; ?>

	<!-- Only Admin -->
		<?php if($userType == 'admin'): ?>
		
		<li class="nav-item">
			<a class="nav-link
			"  
				href="index.php?page=users">Users</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="collapse" href="#reportMenu" role="button">
				Reports
			</a>

			<div  class="collapse <?php if(strpos($_GET['page'], 'reports/')!== false) echo 'show'; ?>" id="reportMenu" >
				
				<ul class="nav flex-column ml-3">
					<li class="nav-item">
						<a  class="nav-link <?php if($_GET['page'] == 'reports/sales') echo 'active'?>"  href="index.php?page=reports/sales">Sales Report</a>
					</li>

					<li class="nav-item">
						<a  class="nav-link <?php if($_GET['page'] == 'reports/inventory') echo 'active'?>"  href="index.php?page=reports/inventory">Inventory Report</a>
					</li>

					<li class="nav-item">
						<a  class="nav-link <?php if($_GET['page'] == 'reports/profits') echo 'active'?>"  href="index.php?page=reports/profits">Profit Reports</a>
					</li>
				</ul>
			</div>
		</li>
	<?php endif; ?>
	</ul>
</div>