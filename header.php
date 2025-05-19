<?php
session_start();
require_once 'config/class.user.php';

$auth_user = new USER();

if(isset($_GET['logout']) && $_GET['logout'] == 'true')
{
  $auth_user->logout();
  header('location: index.php');
}


$register_msg = '';
$login_msg = '';
$active_modal = '';

// Registration
if (isset($_POST['signup'])) 
{
    $uname = trim($_POST['username']);
    $email = trim($_POST['email']);
    $upass = trim($_POST['pass']);
    $code = md5(uniqid(rand()));

    $stmt = $auth_user->runQuery("SELECT * FROM tbl_user WHERE userEmail= :email_id");
    $stmt->execute(array(':email_id' => $email));

    if ($stmt->rowCount() > 0) 
    {
        $register_msg = "<div class='alert alert-danger'>This email is already registered.</div>";
        $active_modal = 'registerModal';
    } 
    else 
    {
        if ($auth_user->register($uname, $email, $upass, $code)) 
        {
            $id = $auth_user->lastID();
            $key = base64_encode($id);

            $message = "
                Hello $uname, <br><br>
                Welcome to Cogent Web Portal. <br> To complete your registration, click the link below:<br><br>
                <a href='http://localhost/wdpf-64/class30(frontend-3AuthVerify-Final)/verify.php?id=$key&code=$code'>Click to Activate</a>
                <br><br>Thanks";
            $subject = "Confirm Registration";

            if ($auth_user->sendMail($email, $message, $subject)) 
            {
                $register_msg = "<div class='alert alert-success'>Registration successful! Check your email to verify.</div>";
            } 
            else 
            {
                $register_msg = "<div class='alert alert-warning'>Registration successful but email could not be sent.</div>";
            }
            $active_modal = 'registerModal';
        }
    }
}

// Login
if (isset($_POST['signin'])) 
{
    $email = trim($_POST['email']);
    $upass = trim($_POST['password']);

    if ($auth_user->login($email, $upass)) 
    {
        header('location: index.php');
        exit;
    } 
    else 
    {
        $login_msg = "<div class='alert alert-danger'>Invalid login or inactive account.</div>";
        $active_modal = 'loginModal';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 4.6 CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <!-- Bootstrap 4.6 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">My Shop</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    
    <form class="form-inline my-2 my-lg-0" id="searchForm">
  <input class="form-control mr-sm-2" type="search" id="searchInput" placeholder="Search products..." aria-label="Search">
  <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
  <div id="searchResults" class="position-absolute bg-white mt-1" style="display:none; z-index:1000; width:300px; max-height:400px; overflow-y:auto; border:1px solid #ddd;"></div>
</form>

    <!-- Database Notification start-->
      <ul class="navbar-nav">
        
         <?php if($auth_user->is_logged_in()): ?>

        <li class="nav-item dropdown">          
          <?php
            $auth_userID = $_SESSION['userSession'];
            $notif_stmt = $auth_user->runQuery("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5");
            $notif_stmt->execute([':uid'=>$auth_userID]);
            $notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);

            $count_stmt = $auth_user->runQuery("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = :uid AND is_read = 0");
            $count_stmt->execute([':uid'=>$auth_userID]);
            $unreadCount = $count_stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
          ?>
          <a href="#" class="nav-link dropdown-toggle text-warning notification-bell" id="notifDropdown" data-toggle="dropdown"><i class="fas fa-bell"></i>

              <?php if( $unreadCount > 0): ?>
                <span class="badge badge-danger"><?= $unreadCount ?></span>
              <?php endif; ?>
          </a>

          <div class="dropdown-menu dropdown-menu-right" style="min-width: 300px;">
            <?php

            if(count($notifications) > 0): ?>
              <?php foreach($notifications as $notif): ?>
                <a href="#" class="dropdown-item notification-link" data-id="<?= $notif['id'] ?>">
                    <?= htmlspecialchars(substr($notif['message'],0,40)) ?>...
                    <br><small class="text-muted"><?= $notif['created_at'] ?></small>>
                  </a>
                <?php endforeach; ?>

                <div class="dropdown-divider"></div>
                <a href="#" class="notification-link" data-id="2">Special Offer</a>
                <?php else: ?>
                  <span class="dropdown-item">No new notification</span>
                <?php endif; ?>
          </div>
        </li>

        <div class="modal fade" id="notificationModal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">                
                  <h5 class="modal-title" id="modalTitle">New Notification</h5>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                  <p id="modalDescription">Loading...</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <li class="nav-item">
          <a href="?logout=true" class="nav-link">Logout</a>
        </li>

        <?php else: ?>   

    <li class="nav-item">
      <a href="#" class="nav-link" data-toggle="modal" data-target="#loginModal">Login</a>
    </li>
      <li class="nav-item">
        <a href="#" class="nav-link" data-toggle="modal" data-target="#registerModal">Register</a>
      </li>

    <?php endif; ?>
      </ul>
    <!-- Database Notification end-->   
     
    </ul>
  </div>
</nav>

<?php if (isset($_GET['inactive'])): ?>
  <div class="alert alert-danger text-center alert-dismissible fade show" role="alert">
    This account is not active. Please check your email to activate your account.
  </div>

<?php endif; ?>


  <?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger text-center alert-dismissible fade show" role="alert">
    Invalid Email or Password or Inactive Account!
  </div>

  <?php endif; ?>


<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Login</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <?= $login_msg ?>
          <div class="form-group">
            <input type="email" class="form-control" name="email" placeholder="Email" required>
          </div>
          <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>         
            <div class="text-right mb-3">
              <a href="fpass.php" class="text-primary" style="font-size: 14px;">Forgot Password?</a>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="signin" class="btn btn-primary">Login</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Register</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <?= $register_msg ?>
          <div class="form-group">
            <input type="text" class="form-control" name="username" placeholder="User Name" required>
          </div>
         <div class="form-group">
            <input type="text" class="form-control" name="email" id="email" placeholder=" Enter Email" required>
            <span id="result"></span>
          </div>
          <div class="form-group">
            <input type="password" class="form-control" name="pass" placeholder="Password" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="signup" class="btn btn-success">Register</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Search Modal -->
 

<script type="text/javascript">
    setTimeout(function()
    {

        $('.alert').alert('close');

    },5000);

    <?php if(!empty($active_modal)):?>
        
        $(document).ready(function()
        {

            $('#<?= $active_modal ?>').modal('show');
        });
        <?php endif; ?>
</script>

<script type="text/javascript">
    document.querySelectorAll('.notification-link').forEach(function(item){

      item.addEventListener('click',function(e){

        e.preventDefault();
        const id = this.getAttribute('data-id');

        fetch('notification_view.php?id=' + id).then(response => response.json()).then(data => {
          if(data.success)
          {
            document.getElementById('modalTitle').textContent = "Notification";

            let content = `<p>${data.message}</p>`;
            if(data.image)
            {
              content += `<img src="${data.image}" alt="Product Image" class="img-thumbnail mt-2" style="max-width: 100px;">`;
            }

            document.getElementById('modalDescription').innerHTML = content;
            $('#notificationModal').modal('show');
            this.classList.remove('font-weight-bold');
          }

          else
          {
            alert(data.message);
          }
        }).catch(err =>{

          console.error(err);
          alert("Something went wrong while fetching notification.");
        });
      });
    });
</script>
<script>
$(document).ready(function() {
  // Live search functionality
  $('#searchInput').on('input', function() {
    const searchTerm = $(this).val().trim();
    
    if(searchTerm.length > 2) { // Only search when at least 3 characters are entered
      $.ajax({
        url: 'search.php',
        method: 'POST',
        data: { term: searchTerm },
        success: function(response) {
          if(response.trim() !== '') {
            $('#searchResults').html(response).show();
          } else {
            $('#searchResults').html('<div class="p-2">No results found</div>').show();
          }
        }
      });
    } else {
      $('#searchResults').hide().empty();
    }
  });

  // Hide results when clicking outside
  $(document).on('click', function(e) {
    if(!$(e.target).closest('#searchForm').length) {
      $('#searchResults').hide();
    }
  });
});
</script>
<script type="text/javascript">
		$(document).ready(function(){

			$('#email').keyup(function(){

				var email = $(this).val();
				if(email.length > 3)
				{
					$('#result').html('Checking...');
					$.ajax({

            type: 'POST',
            url: 'email_check.php',
            data: { email: email },

            success: function(data)
            {
              $('#result').html(data);
            }
          });

					return true;
				}

				else
				{
					$('#result').html('');
				}
			});
		});
	</script>

