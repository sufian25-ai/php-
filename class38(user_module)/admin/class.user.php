<?php
//error_reporting(0);

require_once 'dbConfig.php';

class USER
{
	private $conn;

	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
	}

	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}

	public function lastID()
	{
		$stmt = $this->conn->lastInsertId();
		return $stmt;
	}

	public function register($uname, $email,$upass,$code)
	{
		try 
		{
			$password = md5($upass);
			$stmt = $this->conn->prepare("INSERT INTO tbl_user(userName,userEmail,uerPass,tokenCode)VALUES(:user_name,:user_mail,:user_pass,:active_code)");

			$stmt->bindParam(':user_name',$uname);
			$stmt->bindParam(':user_mail',$email);
			$stmt->bindParam(':user_pass',$password);
			$stmt->bindParam(':active_code',$code);

			$stmt->execute();
			return $stmt;
		} 
		
		catch (PDOException $ex) 
		{
			echo $ex->getMessage();
		}
	}

	public function login($email,$upass)
	{
		try 
		{
			$stmt = $this->conn->prepare("SELECT * FROM tbl_user WHERE userEmail = :email_id");
			$stmt->execute(array('email_id'=>$email));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);

			if($stmt->rowCount() == 1)
			{
				if($userRow['status'] == 'active')
				{
					if($userRow['uerPass'] == md5($upass))
					{
						$_SESSION['userSession'] = $userRow['id'];
						return true;
					}

					else
					{
						header('location: index.php?error');
						exit;
					}
				}
				else
				{
					header('location: index.php?inactive');
					exit;
				}
			}

			else
			{
				header('location: index.php?error');
				exit;
			}
		} 
		catch (PDOException $ex) 
		{
			echo $ex->getMessage();
		}
	}

	public function admninlogin($email, $password)
	{
		try 
		{
			$stmt = $this->conn->prepare("SELECT * FROM tbl_user WHERE userEmail = :email OR userName = :email LIMIT 1");
			$stmt->execute([':email'=>$email]);
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);

			if($stmt->rowCount() > 0)
			{
				if($userRow['status'] !== 'active')
				{
					header('location: login.php?blocked');
					exit;
				}

				if(password_verify($password, $userRow['uerPass']))
				{
					$_SESSION['userSession'] = $userRow['id'];
					$_SESSION['user_type'] = $userRow['user_type'];
					return true;
				}
			}

			return false;
		}

		catch (PDOException $e) 
			{
			echo "DB Error:".$e->getMessage();
			return false;
			}			
	} 
		
		

	public function is_logged_in()
	{
		if($_SESSION['userSession'])
		{
			return true;
		}
	}

	public function redirect($url)
	{
		header('location: $url');
	}

	public function logout()
	{
		session_destroy();
		$_SESSION['userSession'] = false;
	}

	public function adminlogout()
	{
		session_unset();
		session_destroy();
		return true;

	}

	public function sendMail($email, $message, $subject)
	{
		require_once 'mailer/PHPMailer.php';
		require_once 'mailer/SMTP.php';

		$mail = new PHPMailer\PHPMailer\PHPMailer();
		//$mail->SMTPDebug = 3;
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'araman666@gmail.com';
		$mail->Password = 'upbbwchcqvwkzfzf';
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;
		$mail->setFrom('araman666@gmail.com','Cogent');
		$mail->addAddress($email);
		//$mail->addReplyTo('araman666@gmail.com','Cogent');
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $message;

		if(!$mail->send())
		{
			$_SESSION['mailError'] = $mail->ErrorInfo;
			return false;
		}

		else
		{
			return true;
		}

	}

	public function getConnection()
	{
		return $this->conn;
	}

	public function toggleBlockUser($userId)
	{
		try 
		{
			$stmt = $this->conn->prepare("SELECT * FROM tbl_user WHERE id = :id");
			$stmt->execute([':id'=>$userId]);
			$user = $stmt->fetch(PDO::FETCH_ASSOC);

			if($user)
			{
				$newStatus = ($user['status'] == 'active') ? 'inactive' : 'active';
				$update = $this->conn->prepare("UPDATE tbl_user SET status = :status WHERE id = :id");
				$update->execute([':status'=>$newStatus, ':id'=>$userId]);

				//Send email

				$subject = "Account Status Changes";
				$message = "Hello ".$user['userName']. ",<br><br>";

				if($newStatus == 'inactive')
				{
					$message .= "Your account has been <b>blocked</b> by admin. Please contact to the relevent administrator";
				}

				else
				{
					$message .= "Your account has been <b>restored</b>, now you can login.";
				}

				$message .= "<br><br>Thank you.";

				$this->sendMail($user['userEmail'],$message,$subject);
			}
		} 
		catch (PDOException $e) 
		{
			echo $e->getMessage();
		}
	}
}


?>