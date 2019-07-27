<?php 

	$db_production['db_host_production'] = "xxxxx";
	$db_production['db_user_production'] = "xxxxx";
	$db_production['db_pass_production'] = "xxxxx";
	$db_production['db_name_production'] = "xxxxx";


	foreach($db_production as $key => $value ){
		define(strtoupper($key), $value);
	}
	$connection_production = mysqli_connect(DB_HOST_PRODUCTION,DB_USER_PRODUCTION,DB_PASS_PRODUCTION,DB_NAME_PRODUCTION);
	mysqli_set_charset($connection_production, "UTF8");
	if($connection_production){
	}

?>