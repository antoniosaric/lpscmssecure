<?php 
	include "db.php";
	session_start();

	$query_login_status = "UPDATE accounts SET session='offline' WHERE id=".$_SESSION['account_id'];
	$update_login_status = mysqli_query($connection, $query_login_status);
	setcookie(session_name(), '', 100);
	$_SESSION['account_id'] = null;
	$_SESSION['email'] = null;
	$_SESSION['full_name'] = null;
	$_SESSION['access'] = null;
	$_SESSION['user_token'] = null;
	session_unset();
	session_destroy();
	$_SESSION = array();

	header("Location: ../splash.php");

?>