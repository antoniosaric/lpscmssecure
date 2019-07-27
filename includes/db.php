<?php ob_start() ?>
<?php 

	$db['db_host'] = "xxxxx";
	$db['db_user'] = "xxxxx";
	$db['db_pass'] = "xxxxx";
	$db['db_name'] = "xxxxx";


	foreach($db as $key => $value ){
		define(strtoupper($key), $value);
	}
	$connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	if($connection){
	}

?>