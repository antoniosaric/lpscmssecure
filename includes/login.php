<?php include "db.php" ?>
<?php include "passwordHash.php" ?>
<?php session_start(); ?>

<?php 


	if(isset($_POST['login'])){
		$email = $_POST['email'];
		$password = $_POST['password'];

		$email = mysqli_real_escape_string($connection, $email);
		$password = mysqli_real_escape_string($connection, $password);

	} 

	$query = "SELECT *, accounts.id AS account_id FROM accounts LEFT JOIN access ON access.id=accounts.access_id WHERE email='".$email."'";
	$select_account_query = mysqli_query($connection, $query);

	if(!$select_account_query){
		die("QUERY FAILED".mysqli_error($connection));
	}

	if($select_account_query->num_rows > 0){
		$row = mysqli_fetch_array($select_account_query);
		$db_id = $row['account_id'];
		$db_email = $row['email'];
		$db_password = $row['password'];
		$db_full_name = $row['first_name']." ".$row['last_name'];
		$db_access = $row['access'];

		$query_login_status = "UPDATE accounts SET session='online' WHERE id=".$db_id;

		if( $email == $db_email && validate_pw($password, $db_password) && $db_access == 'admin'){
			$update_login_status = mysqli_query($connection, $query_login_status);

			$_SESSION['account_id'] = $db_id;
			$_SESSION['email'] = $db_email;
			$_SESSION['full_name'] = $db_full_name;
			$_SESSION['access'] = $db_access;
			//create a cryptographically secure token.
			$userToken = bin2hex(openssl_random_pseudo_bytes(24));
			 
			//assign the token to a session variable.
			$_SESSION['user_token'] = $userToken;

			header("Location: ../admin");
		}else if($email == $db_email && validate_pw($password, $db_password) && $db_access == 'restricted'){
			$update_login_status = mysqli_query($connection, $query_login_status);

			$_SESSION['account_id'] = $db_id;
			$_SESSION['email'] = $db_email;
			$_SESSION['full_name'] = $db_full_name;
			$_SESSION['access'] = $db_access;
			//create a cryptographically secure token.
			$userToken = bin2hex(openssl_random_pseudo_bytes(24));
			 
			//assign the token to a session variable.
			$_SESSION['user_token'] = $userToken;

			header("Location: ../index.php");
		}else{
			header("Location: ../splash.php");
		}

	}else{
		header("Location: ../splash.php");
	}

?>