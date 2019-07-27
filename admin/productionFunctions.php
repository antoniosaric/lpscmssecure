<?php

//======================================================================
// CREATE UUID
//======================================================================

	function createUUID($table, $id){
		echo $table;
		global $connection_production;

		$sqlparticipantupdateUUID = "UPDATE ".$table." SET uuid = UNHEX(REPLACE(UUID(),'-','')) WHERE id =".$id;
        $resultuuid2 = $connection_production->query($sqlparticipantupdateUUID);

        if(!$resultuuid2){
			die("QUERY FAILED " . mysqli_error($connection_production));
		}
	}

//======================================================================
// QUERY CHECK PRODUCTION
//======================================================================

	function queryCheckProduction($result){
		global $connection_production;

		if(!$result){
			die("QUERY FAILED " . mysqli_error($connection_production));
		}
	}

//======================================================================
// TABLE COUNT
//======================================================================

	function tableCount($target){
		global $connection_production;
		$new_target = mysqli_real_escape_string($connection_production, $target);
		$stmt = "SELECT * FROM ".$new_target;
		$select_all_count = mysqli_query($connection_production, $stmt);
		queryCheck($select_all_count);
		return mysqli_num_rows($select_all_count);
	}

//======================================================================
// RETURN PARTITIONS FOR NON CURATED PROFILES
//======================================================================

function retunPartition($sport) {
	$return = new stdClass();
	if($sport == 1){
	  $return->partitionId = 43;
	  // $entity_activity_smId = 6;
	  $return->resultentity_activity_sm_partition_sm = 34;
	}else if($sport == 2){
	  $return->partitionId = 44;
	  // $entity_activity_smId = 7;
	  $return->resultentity_activity_sm_partition_sm = 35;
	}else if($sport == 3){
	  $return->partitionId = 45;
	  // $entity_activity_smId = 8;
	  $return->resultentity_activity_sm_partition_sm = 36;
	}else if($sport == 4){
	  $return->partitionId = 46;
	  // $entity_activity_smId = 9;
	  $return->resultentity_activity_sm_partition_sm = 37;
	}else if($sport == 5){
	  $return->partitionId = 47;
	  // $entity_activity_smId = 10;
	  $return->resultentity_activity_sm_partition_sm = 38;
	}else if($sport == 6){
	  $return->partitionId = 48;
	  // $entity_activity_smId = 11;
	  $return->resultentity_activity_sm_partition_sm = 39;
	}else if($sport == 7){
	  $return->partitionId = 49;
	  // $entity_activity_smId = 12;
	  $return->resultentity_activity_sm_partition_sm = 40;
	}else if($sport == 8){
	  $return->partitionId = 50;
	  // $entity_activity_smId = 13;
	  $return->resultentity_activity_sm_partition_sm = 41;
	}else if($sport == 9){
	  $return->partitionId = 51;
	  // $entity_activity_smId = 14;
	  $return->resultentity_activity_sm_partition_sm = 42;
	}else if($sport == 10){
	  $return->partitionId = 52;
	  // $entity_activity_smId = 15;
	  $return->resultentity_activity_sm_partition_sm = 43;
	}else if($sport == 11){
	  $return->partitionId = 53;
	  // $entity_activity_smId = 16;
	  $return->resultentity_activity_sm_partition_sm = 44;
	}else if($sport == 12){
	  $return->partitionId = 54;
	  // $entity_activity_smId = 17;
	  $return->resultentity_activity_sm_partition_sm = 45;
	}else if($sport == 13){
	  $return->partitionId = 55;
	  // $entity_activity_smId = 18;
	  $return->resultentity_activity_sm_partition_sm = 46;
	}else if($sport == 14){
	  $return->partitionId = 56;
	  // $entity_activity_smId = 19;
	  $return->resultentity_activity_sm_partition_sm = 47;
	}else if($sport == 15){
	  $return->partitionId = 57;
	  // $entity_activity_smId = 20;
	  $return->resultentity_activity_sm_partition_sm = 48;
	}else{
	  $return->partitionId = 0;
	  // $entity_activity_smId = 0;
	  $return->resultentity_activity_sm_partition_sm = 0;
	}
	return $return;
}

//======================================================================
// LOCATION FUNCTIONS
//======================================================================

	function addLocation($association) {
		global $connection_production;
		if (isset($_POST['add_location'])
			&& (strcasecmp($association, "profile") == 0 || strcasecmp($association, "entity") == 0 || strcasecmp($association, "team") == 0)) {

			$post_id_set = $_POST[$association.'_id_set'];
			$post_location_city = strtolower(trim($_POST['add_location_city']));
			$post_location_state_province = strtolower(trim($_POST['add_location_state_province']));
			$post_location_country = strtolower(trim($_POST['add_location_country']));
			$location_not_found = true;

			if ((strlen($post_location_city) > 1) && (strlen($post_location_country) > 1)) {

				$sql_state = "SELECT DISTINCT state_province.state AS state, state_province.abbreviation AS abbreviation
					FROM state_province
					WHERE lower(state_province.state)=? OR lower(state_province.abbreviation)=?";

				$stmtstate = $connection_production->prepare($sql_state);
				$stmtstate->bind_param("ss", $post_location_state_province, $post_location_state_province);
				$stmtstate->execute();
				$resultstate = $stmtstate->get_result();
				$rowstate = $resultstate->fetch_assoc();
				if ($resultstate->num_rows > 0) {
					$state_province_region_code = $rowstate['abbreviation'];
					$state_province_region = $rowstate['state'];
				} else {
					$state_province_region_code = 'NULL';
				}

				$sql_country = "SELECT DISTINCT country.country AS country, country.code AS countrycode FROM country
					WHERE lower(country.country)=? OR lower(country.code)=?";
				if ($post_location_country == 'usa') {
					$post_location_country = 'us';
				}
				$stmtcountry = $connection_production->prepare($sql_country);
				$stmtcountry->bind_param("ss", $post_location_country, $post_location_country);
				$stmtcountry->execute();
				$resultcountry = $stmtcountry->get_result();
				$rowcountry = $resultcountry->fetch_assoc();

				if ($resultcountry->num_rows > 0) {
					$countryCode = $rowcountry['countrycode'];
					$post_location_country = $rowcountry['country'];
				} else {
					$countryCode = 'NULL';
				}

				$assoc_location_table = $association."_location_sm";

				$search_location = "SELECT DISTINCT LOWER(location.city) AS city, LOWER(location.stateProvince) AS state,
					LOWER(location.country) AS country, ".$assoc_location_table.".id AS ".$assoc_location_table.",
					".$assoc_location_table.".locationId AS ".$assoc_location_table."_id FROM ".$association
					." INNER JOIN ".$assoc_location_table." ON ".$assoc_location_table.".".$association."Id=".$association.".id
					INNER JOIN location ON location.id=".$assoc_location_table.".locationId
					WHERE ".$association.".id=?";

				$stmt_search_location = $connection_production->prepare($search_location);
				$stmt_search_location->bind_param("i", $post_id_set);
				$stmt_search_location->execute();
				$result_search_location = $stmt_search_location->get_result();
				$stmt_search_location->close();

				while ($row_search_location = $result_search_location->fetch_assoc()) {
					if (strtolower($row_search_location['city']) == strtolower($post_location_city)
						&& (strtolower($row_search_location['state']) == strtolower($post_location_state_province)
							|| strtolower($state_province_region_code) == strtolower($post_location_state_province))
						&& (strtolower($row_search_location['country']) == strtolower($post_location_country)
							|| strtolower($countryCode) == strtolower($post_location_country))) {
						$location_not_found = false;
						break;
					}
				}
				if (!!$location_not_found) {

					$null = 'NULL';
					//insert location info
					$sql_location = "INSERT INTO location (city, stateProvince, stateProvinceCode, county,
						region, country, countryCode, postalCode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

					$stmt_location = $connection_production->prepare($sql_location);
					$stmt_location->bind_param("ssssssss", $post_location_city, $state_province_region, $state_province_region_code, $null, $null, $post_location_country, $countryCode, $null);
					$stmt_location->execute();
					$last_id = $connection_production->insert_id;
					$stmt_location->close();

					createUUID('location', $last_id);

					//insert participant location link
					$sql_assoc_location_sm = "INSERT INTO ".$assoc_location_table." (locationId, ".$association."Id) VALUES (?, ?)";

					$stmt_assoc_location_sm = $connection_production->prepare($sql_assoc_location_sm);
					$stmt_assoc_location_sm->bind_param("ii", $last_id, $post_id_set);
					$stmt_assoc_location_sm->execute();
					$stmt_assoc_location_sm->close();
					$last_id_assoc_location_sm = $connection_production->insert_id;

					$update_string = $association.': '.$post_id_set.' , add location: '.$post_location_city." ".$post_location_state_province." ".$post_location_country.', and added '.$association.' location association id: '.$last_id_assoc_location_sm;
					insertChange($_SESSION['account_id'], $association, 'add location', $post_id_set, $update_string);
					switch ($association) {
						case "profile":
							header("location: categories.php?source=profile_update_athlete&profile_id=".$post_id_set."#add_location");
							break;
						case "team":
							header("location: categories.php?source=team_update&team_id=".$post_id_set."#add_location");
							break;
						case "entity":
							header("location: categories.php?source=league_update&entity_id=".$post_id_set."#add_location");
							break;
					}
				} else {
					echo "<h3 style='color:red;'>location: ".$post_location_city." ".$post_location_state_province." ".$post_location_country." - already exists on ".$association." id: ".$post_id_set."</h3>";
				}
			} else {
				echo "<h3 style='color:red;'>City and Country cannot be blank</h3>";
			}
		}
	}
	function updateLocation($association) {
		global $connection_production;
		if (isset($_POST['update_location'])
			&& (strcasecmp($association, "profile") == 0 || strcasecmp($association, "entity") == 0 || strcasecmp($association, "team") == 0)) {

			$post_id_set = $_POST[$association.'_id_set'];
			$post_location_city = strtolower(trim($_POST['update_location_city']));
			$post_location_state_province = strtolower(trim($_POST['update_location_state_province']));
			$post_location_country = strtolower(trim($_POST['update_location_country']));
			$post_update_location_id = $_POST['update_location_id'];
			$location_not_found = true;

			if (strlen($post_location_city) > 1 && strlen($post_location_country) > 1) {
				$sql_state = "SELECT DISTINCT state_province.state AS state, state_province.abbreviation AS abbreviation FROM state_province
					WHERE lower(state_province.state)=? OR lower(state_province.abbreviation)=?";
				$stmtstate = $connection_production->prepare($sql_state);
				$stmtstate->bind_param("ss", $post_location_state_province, $post_location_state_province);
				$stmtstate->execute();
				$resultstate = $stmtstate->get_result();
				$rowstate = $resultstate->fetch_assoc();
				if ($resultstate->num_rows > 0) {
				  $state_province_region_code = $rowstate['abbreviation'];
				  $state_province_region = $rowstate['state'];
				} else {
				  $state_province_region_code = 'NULL';
				}

				$sql_country = "SELECT DISTINCT country.country AS country, country.code AS countrycode FROM country
					WHERE lower(country.country)=? OR lower(country.code)=?";
				if ($post_location_country == 'usa') {
				  $post_location_country = 'us';
				}
				$stmtcountry = $connection_production->prepare($sql_country);
				$stmtcountry->bind_param("ss", $post_location_country, $post_location_country);
				$stmtcountry->execute();
				$resultcountry = $stmtcountry->get_result();
				$rowcountry = $resultcountry->fetch_assoc();

				if ($resultcountry->num_rows > 0) {
				  $countryCode = $rowcountry['countrycode'];
				  $post_location_country = $rowcountry['country'];
				} else {
				  $countryCode = 'NULL';
				}

				$assoc_location_table = $association."_location_sm";

				$search_update_location = "SELECT DISTINCT location.id AS location_id, LOWER(location.city) AS city,
					LOWER(location.stateProvince) AS state, LOWER(location.country) AS country, ".$assoc_location_table.".id AS ".$assoc_location_table.",
					".$assoc_location_table.".locationId AS ".$assoc_location_table."_id, LOWER(location.countryCode) AS country_code,
					location.stateProvinceCode AS state_province_code FROM ".$association
					." INNER JOIN ".$assoc_location_table." ON ".$assoc_location_table.".".$association."Id=".$association.".id
					INNER JOIN location ON location.id=".$assoc_location_table.".locationId
					WHERE ".$association.".id=?";

				$stmt_search_update_location = $connection_production->prepare($search_update_location);
				$stmt_search_update_location->bind_param("i", $post_id_set);
				$stmt_search_update_location->execute();
				$result_search_update_location = $stmt_search_update_location->get_result();
				$stmt_search_update_location->close();

				while ($row_search_update_location = $result_search_update_location->fetch_assoc()) {
					if (strtolower($row_search_update_location['city']) == strtolower($post_location_city)
						&& (strtolower($row_search_update_location['state']) == strtolower($post_location_state_province)
							|| strtolower($row_search_update_location['state_province_code']) == strtolower($post_location_state_province))
						&& (strtolower($row_search_update_location['country']) == strtolower($post_location_country)
							|| strtolower($row_search_update_location['country_code']) == strtolower($post_location_country))) {
						$location_not_found = false;
						break;
					}
				}
				if (!!$location_not_found) {

					$null = 'NULL';
					//insert location info
					$sql_location = "UPDATE location SET city=?, stateProvince=?, stateProvinceCode=?, county=?,
						region=?, country=?, countryCode=?, postalCode=? WHERE id=?";

					$stmt_location = $connection_production->prepare($sql_location);
					$stmt_location->bind_param("ssssssssi", $post_location_city, $state_province_region, $state_province_region_code, $null, $null, $post_location_country, $countryCode, $null, $post_update_location_id);
					$stmt_location->execute();
					$stmt_location->close();

					$update_string = $association.': '.$post_id_set.' , update location id: '.$post_update_location_id.' , new location info: '.$post_location_city." ".$post_location_state_province." ".$post_location_country;
					insertChange($_SESSION['account_id'], $association, 'update location', $post_id_set, $update_string);
					switch ($association) {
						case "profile":
							header("location: categories.php?source=profile_update_athlete&profile_id=".$post_id_set."#location");
							break;
						case "team":
							header("location: categories.php?source=team_update&team_id=".$post_id_set."#location");
							break;
						case "entity":
							header("location: categories.php?source=league_update&entity_id=".$post_id_set."#location");
							break;
					}
				} else {
					echo "<h3 style='color:red;'>location: ".$post_location_city." ".$post_location_state_province." ".$post_location_country." - already exists on ".$association." id: ".$post_id_set."</h3>";
				}
			} else {
				echo "<h3 style='color:red;'>City and Country cannot be blank</h3>";
			}
		}
	}
	function deleteLocation($association) {
        global $connection_production;
        if (isset($_POST['delete_location'])
			&& (strcasecmp($association, "profile") == 0 || strcasecmp($association, "entity") == 0 || strcasecmp($association, "team") == 0)) {

            $post_id_set = $_POST[$association.'_id_set'];
            $profile_location_sm_id = $_POST['delete_'.$association.'_location_sm_id'];
            $post_location_id = $_POST['delete_location_id'];

            $sql_search_delete_location = "SELECT DISTINCT location.id AS location_id, location.city AS city,
				location.stateProvince AS stateProvince, location.country AS country FROM location
				WHERE location.id=?";
            $prepared_delete_location = $connection_production->prepare($sql_search_delete_location);
            $prepared_delete_location->bind_param('i', $post_location_id);
            $result_location = $prepared_delete_location->execute();
            $result_of_delete_location = $prepared_delete_location->get_result();
            $row_delete_location = $result_of_delete_location->fetch_assoc();

            $sql_delete_location = "DELETE FROM location WHERE id=?";
            $stmt_delete_location = $connection_production->prepare($sql_delete_location);
            $stmt_delete_location->bind_param("i", $post_location_id);
            if ($stmt_delete_location->execute()) {

				$assoc_location_table = $association."_location_sm";

				$sql_delete_assoc_location_sm = "DELETE FROM ".$assoc_location_table." WHERE id=?";
				$stmt_delete_assoc_location_sm = $connection_production->prepare($sql_delete_assoc_location_sm);
				$stmt_delete_assoc_location_sm->bind_param("i", $profile_location_sm_id);
				if ($stmt_delete_assoc_location_sm->execute()) {

					$update_string = $association.': '.$post_id_set.' deleted location : '. $row_delete_location['city'].' '. $row_delete_location['stateProvince'].' '. $row_delete_location['country'].', and location id: '.$post_id_set.', deleted '.$assoc_location_table.' id: '.$profile_location_sm_id;
					insertChange($_SESSION['account_id'], $association, 'deleted location', $post_id_set, $update_string);
					switch ($association) {
						case "profile":
							header("location: categories.php?source=profile_update_athlete&profile_id=".$post_id_set."#location");
							break;
						case "team":
							header("location: categories.php?source=team_update&team_id=".$post_id_set."#location");
							break;
						case "entity":
							header("location: categories.php?source=league_update&entity_id=".$post_id_set."#location");
							break;
					}
				} else {
					echo "<h3 style='color:red;'>Something went wrong deleting from ".$assoc_location_table." table</h3>";
				}
				$stmt_delete_assoc_location_sm->close();
			} else {
				echo "<h3 style='color:red;'>Something went wrong deleting from location table</h3>";
			}
            $stmt_delete_location->close();
        }
    }

//======================================================================
// IMAGE FUNCTIONS
//======================================================================

	function addImage($association) {
		global $connection_production;
		if (isset($_POST['add_image'])
			&& (strcasecmp($association, "profile") == 0 || strcasecmp($association, "entity") == 0 || strcasecmp($association, "team") == 0
				|| strcasecmp($association, "partition") == 0)) {

			$post_id_set = $_POST[$association.'_id_set'];
			$post_image_name = $_POST['image_name'];
			$post_image_id = $_POST['image_id'];
			$assoc_img_table = $association."_image_sm";

			if ($post_image_id) {

				$search_image_id = "SELECT image.id AS image_id FROM image
					LEFT JOIN ".$assoc_img_table." ON ".$assoc_img_table.".imageId = image.id ";
				if (strcasecmp($association, "partition") != 0) {
					$search_image_id .= "LEFT JOIN ".$association." ON ".$association.".id=".$assoc_img_table.".".$association."Id
						WHERE image.id=? AND ".$association.".id=?";
				} else {
					$search_image_id .= "LEFT JOIN `partition` AS partition_LP ON partition_LP.id=partition_image_sm.partitionId
						WHERE image.id=? AND partition_LP.id=?";
				}
				echo "<h1>".$search_image_id."</h1>";
				$search_image_id = $connection_production->prepare($search_image_id);
				$search_image_id->bind_param("ii", $post_image_id, $post_id_set);
				$search_image_id->execute();
				$result_search_image_id = $search_image_id->get_result();
				$row_search_image_id = $result_search_image_id->fetch_assoc();
				$search_image_id->close();

				$found_image_id = "SELECT image.id AS image_id FROM image WHERE id=?";
				$found_image_id = $connection_production->prepare($found_image_id);
				$found_image_id->bind_param("i", $post_image_id);
				$found_image_id->execute();
				$result_found_image_id = $found_image_id->get_result();
				$row_found_image_id = $result_found_image_id->fetch_assoc();
				$found_image_id->close();

				if (!$row_search_image_id['image_id']) {
					if (!!$row_found_image_id['image_id']) {
						$sql_image_name = "INSERT INTO ".$assoc_img_table." (imageId, ".$association."Id) VALUES (?,?)";

						$stmt_image_name = $connection_production->prepare($sql_image_name);
						$stmt_image_name->bind_param("ii", $post_image_id, $post_id_set);
						$stmt_image_name->execute();
						$stmt_image_name->close();
						$last_id = $connection_production->insert_id;

						$update_string = $association.': '.$post_id_set.' , add image by id: '.$post_image_id.', added '.$assoc_img_table.' id: '.$last_id;
						insertChange($_SESSION['account_id'], $association, 'add image', $post_id_set, $update_string);
						switch ($association) {
							case "profile":
								header("location: categories.php?source=profile_update_athlete&profile_id=".$post_id_set."#add_image");
								break;
							case "team":
								header("location: categories.php?source=team_update&team_id=".$post_id_set."#add_image");
								break;
							case "entity":
								header("location: categories.php?source=league_update&entity_id=".$post_id_set."#add_image");
								break;
							case "partition":
								header("location: categories.php?source=update_partition&partition_id=".$post_id_set."#add_image");
								break;
						}
					} else {
						echo "<h3 style='color:red;'>id: ".$post_image_id." - Not found </h3>";
					}
				} else {
					echo "<h3 style='color:red;'>id: ".$row_search_image_id['image_id']." - already has the id: ".$post_image_id."</h3>";
				}

			} else if (!!$post_image_name) {

				if (strlen($post_image_name) > 4) {

					$search_image_name = "SELECT imageName, id FROM image WHERE imageName=?";
					$search_image_name = $connection_production->prepare($search_image_name);
					$search_image_name->bind_param("s", $post_image_name);
					$search_image_name->execute();
					$result_search_image_name = $search_image_name->get_result();
					$row_search_image_name = $result_search_image_name->fetch_assoc();
					$search_image_name->close();

					if (!$row_search_image_name['id']) {
						$one = 1;
						$sql_image_name = "INSERT INTO image (imageName, imageTypeId) VALUES (?,?)";

						$stmt_image_name = $connection_production->prepare($sql_image_name);
						$stmt_image_name->bind_param("si", $post_image_name, $one);
						$stmt_image_name->execute();
						$stmt_image_name->close();
						$last_id = $connection_production->insert_id;

						createUUID('image', $last_id);

						$sql_image_name_sm = "INSERT INTO ".$assoc_img_table." (imageId, ".$association."Id) VALUES (?,?)";

						$stmt_image_name_sm = $connection_production->prepare($sql_image_name_sm);
						$stmt_image_name_sm->bind_param("ii", $last_id, $post_id_set);
						$stmt_image_name_sm->execute();
						$stmt_image_name_sm->close();
						$last_id_assoc_image_sm = $connection_production->insert_id;

						$update_string = $association.': '.$post_id_set.' , add image : '.$post_image_name.', and added '.$assoc_img_table.' id: '.$last_id_assoc_image_sm;
						insertChange($_SESSION['account_id'], $association, 'add image', $post_id_set, $update_string);
						switch ($association) {
							case "profile":
								header("location: categories.php?source=profile_update_athlete&profile_id=".$post_id_set."#add_image");
								break;
							case "team":
								header("location: categories.php?source=team_update&team_id=".$post_id_set."#add_image");
								break;
							case "entity":
								header("location: categories.php?source=league_update&entity_id=".$post_id_set."#add_image");
								break;
							case "partition":
								header("location: categories.php?source=update_partition&partition_id=".$post_id_set."#add_image");
								break;
						}
					} else {
						echo "<h3 style='color:red;'>id: ".$row_search_image_name['id']." - already has the name: ".$row_search_image_name['imageName']."</h3>";
					}
				} else {
					echo "<h3 style='color:red;'>image name needs to be longer</h3>";
				}
			} else {
				echo "<h3 style='color:red;'>at least one file needs to be entered</h3>";
			}
		}
	}
	function updateImage($association) {
		global $connection_production;
		if (isset($_POST['update_image'])
			&& (strcasecmp($association, "profile") == 0 || strcasecmp($association, "entity") == 0 || strcasecmp($association, "team") == 0
				|| strcasecmp($association, "partition") == 0)) {

			$post_id_set = $_POST[$association.'_id_set'];
			$post_image_name = $_POST['image_name'];
			$post_image_id = $_POST['image_id'];

			$search_image_name = "SELECT imageName, id FROM image WHERE imageName=?";
			$search_image_name = $connection_production->prepare($search_image_name);
			$search_image_name->bind_param("s", $post_image_name);
			$search_image_name->execute();
			$result_search_image_name = $search_image_name->get_result();
			$row_search_image_name = $result_search_image_name->fetch_assoc();
			$search_image_name->close();

			if (!$row_search_image_name['id']) {
				$sql_update_image = "UPDATE image SET imageName=? WHERE id=?";

				$stmt_update_image = $connection_production->prepare($sql_update_image);
				$stmt_update_image->bind_param("si", $post_image_name, $post_image_id);
				$stmt_update_image->execute();
				$stmt_update_image->close();

				$update_string = $association.': '.$post_id_set.' update image name to: '.$post_image_name;
				insertChange($_SESSION['account_id'], $association, 'update image', $post_id_set, $update_string);
				switch ($association) {
					case "profile":
						header("location: categories.php?source=profile_update_athlete&profile_id=".$post_id_set."#image");
						break;
					case "team":
						header("location: categories.php?source=team_update&team_id=".$post_id_set."#image");
						break;
					case "entity":
						header("location: categories.php?source=league_update&entity_id=".$post_id_set."#image");
						break;
					case "partition":
						header("location: categories.php?source=update_partition&partition_id=".$post_id_set."#image");
						break;
				}
			} else {
				echo "<h3 style='color:red;'>id: ".$row_search_image_name['id']." - already has the name: ".$row_search_image_name['imageName']."</h3>";
			}
		}
	}
	function deleteImage($association) {
		global $connection_production;
		if (isset($_POST['delete_image'])
			&& (strcasecmp($association, "profile") == 0 || strcasecmp($association, "entity") == 0 || strcasecmp($association, "team") == 0
				|| strcasecmp($association, "partition") == 0)) {

			$post_id_set = $_POST[$association.'_id_set'];
			$post_assoc_image_sm_id = $_POST['delete_'.$association.'_image_sm_id'];
			$post_image_id = $_POST['delete_image_id'];
			$assoc_image_table = $association."_image_sm";

			$sql_delete_image = "DELETE FROM image WHERE id=?";

			$stmt_delete_image = $connection_production->prepare($sql_delete_image);
			$stmt_delete_image->bind_param("i", $post_image_id);
			$stmt_delete_image->execute();
			$stmt_delete_image->close();

			$sql_delete_assoc_image_sm = "DELETE FROM ".$assoc_image_table." WHERE id=?";

			$stmt_delete_assoc_image_sm = $connection_production->prepare($sql_delete_assoc_image_sm);
			$stmt_delete_assoc_image_sm->bind_param("i", $post_assoc_image_sm_id);
			$stmt_delete_assoc_image_sm->execute();
			$stmt_delete_assoc_image_sm->close();

			$update_string = $association.': '.$post_id_set.' deleted image association id: '.$post_image_id.', deleted '.$assoc_image_table.' id: '.$post_assoc_image_sm_id;
			insertChange($_SESSION['account_id'], $association, 'deleted image', $post_id_set, $update_string);
			switch ($association) {
				case "profile":
					header("location: categories.php?source=profile_update_athlete&profile_id=".$post_id_set."#image");
					break;
				case "team":
					header("location: categories.php?source=team_update&team_id=".$post_id_set."#image");
					break;
				case "entity":
					header("location: categories.php?source=league_update&entity_id=".$post_id_set."#image");
					break;
				case "partition":
					header("location: categories.php?source=update_partition&partition_id=".$post_id_set."#image");
					break;
			}
		}
	}
	function showImagePreviews($img_names, $folder) {
		if (!empty($img_names)) {
			$activity_bool = strcasecmp(trim($folder), "activity") == 0;
			if ($activity_bool) $folder = "activity-black";
			$loop_count = $activity_bool ? 2 : 1;

			echo "<div class='row'>";

			for ($i = 0; $i < $loop_count; $i++) {
				if ($i == 1) $folder = "activity-blue";

				foreach ($img_names as $img_preview_name) {
?>
					<div class="form-group col-xs-3" style="max-width:350px; word-wrap:break-word;">
<?php
					if ($activity_bool) {
						echo "<label>".ucfirst($folder)." preview</label>";
					} else {
						echo "<label for='".$img_preview_name."'>".$img_preview_name." preview</label>";
					}
					$target_dir = realpath(__DIR__."/../../assets/images/".$folder);
					if ($target_dir !== false) {
						$target_file = $target_dir."/".$img_preview_name;
						if (file_exists($target_file)) {
							echo "<img src='../../assets/images/".$folder."/".$img_preview_name."?".rand()."' style='max-width:300px; max-height:300px;' name='".$img_preview_name."'/>";
						} else {
							echo "<h3 style='color:red;'>".$target_file." - Not found. No preview.</h3>";
						}
					} else {
						echo "<h3 style='color:red;'>assets/images/".$folder."/ - Directory not found</h3>";
					}
					echo "</div>";
				}
			}
			echo "</div>";
		}
	}
?>
