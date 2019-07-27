<?php

//======================================================================
// FIND ALL ACTIVITIES
//======================================================================

	function findAllActivities($filter, $words) {
		global $connection_production;

        $query = "SELECT DISTINCT *, activity.id AS activity_id, activity.activity AS activity FROM activity
			LEFT JOIN activity_image_sm ON activity_image_sm.activityId = activity.id
			LEFT JOIN image ON image.id=activity_image_sm.imageId "
			.$filter;
		$stmt = $connection_production->prepare($query);

		$bind_parameters = array();
		$bind_parameters[0] = "";
		if (!empty($words)) {
			foreach ($words as $key) {
				$bind_parameters[0] = $bind_parameters[0]."ssi";
				$format_param = '%'.$key.'%';
				array_push($bind_parameters, $format_param);
				array_push($bind_parameters, $format_param);
				array_push($bind_parameters, $key);
			}
			call_user_func_array(array($stmt, "bind_param"), refValues($bind_parameters));
		}
		$stmt->execute();
        $select_all_activity_query = $stmt->get_result();
		$stmt->close();

		echo "<style>.noverflow {max-height:5vh; overflow-y:auto}</style>";
        while ($row = mysqli_fetch_assoc($select_all_activity_query)) {
            $activity_id = $row['activity_id'];
            $activity = $row['activity'];
            $imageName = $row['imageName'];
            $activity_description = $row['description'];

            echo "<tr>";
            echo "<td>".$activity_id."</td>";
            echo "<td>".$activity."</td>";
            echo "<td>".$imageName."</td>";
            echo "<td><div class='noverflow'>".$activity_description."</div></td>";
            echo "<td><a class='btn btn-info' href='categories.php?source=sports_update&activity_id=".$activity_id."'>Edit</a></td>";

			echo "<td><form method='post'>";
			echo "<input type='hidden' name='activity_id' value=".$activity_id.">";
			echo "<input onClick=\"return confirm('Are you sure you want to do that?');\" class='btn btn-danger' type='submit' name='delete_activity' value='DELETE' />";
			echo "</form></td>";
			echo "</tr>";
        }
	}

//======================================================================
// DELETE ACTIVITY
//======================================================================

	function deleteActivity() {
		global $connection_production;
        if (isset($_POST['delete_activity'])) {
			$activity_id = $_POST['activity_id'];

			$get_activity_sql = "SELECT activity.id AS activity_id, image.id AS image_id, activity_image_sm.id AS a_i_sm_id,
				activity.activity AS activity FROM activity
				LEFT JOIN activity_image_sm ON activity_image_sm.activityId=activity.id
				LEFT JOIN image ON image.id=activity_image_sm.imageId
				WHERE activity.id=?";
			$get_activity_stmt = $connection_production->prepare($get_activity_sql);
			$get_activity_stmt->bind_param("i", $activity_id);
			$get_activity_stmt->execute();
			$row_activity = $get_activity_stmt->get_result()->fetch_assoc();
			$get_activity_stmt->close();

			if (!!$row_activity['activity_id']) {
				$image_id = $row_activity['image_id'];
				$a_i_sm_id = $row_activity['a_i_sm_id'];
				$activity = $row_activity['activity'];

				$update_string = "delete activity id: ".$activity_id." (".trim($activity).") :: ";

				if (removeEntitiesHelper($activity_id, $update_string) != -1) {
					if (removeProfilesHelper($activity_id, $update_string) != -1) {

						$del_activity_sql = "DELETE FROM activity WHERE id=?";
						$del_activity_stmt = $connection_production->prepare($del_activity_sql);
						$del_activity_stmt->bind_param("i", $activity_id);
						if ($del_activity_stmt->execute()) {

							$del_image_sql = "DELETE FROM image WHERE id=?";
							$del_image_stmt = $connection_production->prepare($del_image_sql);
							$del_image_stmt->bind_param("i", $image_id);
							if ($del_image_stmt->execute()) {

								$del_img_assoc_sql = "DELETE FROM activity_image_sm WHERE id=?";
								$del_img_assoc_stmt = $connection_production->prepare($del_img_assoc_sql);
								$del_img_assoc_stmt->bind_param("i", $a_i_sm_id);
								if ($del_img_assoc_stmt->execute()) {
									$update_string .= ", deleted image_id: ".$image_id.", deleted a_i_sm id: ".$a_i_sm_id;
									insertChange($_SESSION['account_id'], 'activity', 'delete', $activity_id, $update_string);
									header("location: categories.php?source=activity");
								} else {
									echo "<h3 style='color:red'>Something went wrong deleting activity_image_sm association</h3>";
								}
								$del_img_assoc_stmt->close();
							} else {
								echo "<h3 style='color:red'>Something went wrong deleting image</h3>";
							}
							$del_image_stmt->close();
						} else {
							echo "<h3 style='color:red'>Something went wrong deleting activity</h3>";
						}
						$del_activity_stmt->close();
					} else {
						echo "<h3 style='color:red'>Something went wrong removing profiles</h3>";
					}
				} else {
					echo "<h3 style='color:red'>Something went wrong removing entities</h3>";
				}
			} else {
				echo "<h3 style='color:red'>Could not find activity</h3>";
			}
        }
	}
	function removeEntitiesHelper($activity_id, &$update_string) {
		global $connection_production;

		$get_entities_sql = "SELECT entity.id AS entity_id, entity.name AS entity_name,
			entity_activity_sm.id AS e_a_sm_id FROM entity
			LEFT JOIN entity_activity_sm ON entity_activity_sm.entityId=entity.id
			WHERE entity_activity_sm.activityId=?";
		$get_entities_stmt = $connection_production->prepare($get_entities_sql);
		$get_entities_stmt->bind_param("i", $activity_id);
		$get_entities_stmt->execute();
		$get_entities_result = $get_entities_stmt->get_result();
		$get_entities_stmt->close();

		if ($get_entities_result->num_rows > 0) {
			$deleted_entities = array();
			$deleted_associations = array();

			while ($row_entity = $get_entities_result->fetch_assoc()) {
				$entity_id = $row_entity['entity_id'];
				$entity_name = $row_entity['entity_name'];
				$e_a_sm_id = $row_entity['e_a_sm_id'];

				if (checkNoEntityAssoc($entity_name)) {

					$del_entity_sql = "DELETE FROM entity WHERE id=?";
					$del_entity_stmt = $connection_production->prepare($del_entity_sql);
					$del_entity_stmt->bind_param("i", $entity_id);
					if ($del_entity_stmt->execute()) {
						$del_entity_stmt->close();
						array_push($deleted_entities, $entity_id);
					} else {
						$del_entity_stmt->close();
						return -1;
					}
				}
				$del_entity_assoc_sql = "DELETE FROM entity_activity_sm WHERE id=?";
				$del_entity_assoc_stmt = $connection_production->prepare($del_entity_assoc_sql);
				$del_entity_assoc_stmt->bind_param("i", $e_a_sm_id);
				if ($del_entity_assoc_stmt->execute()) {
					$del_entity_assoc_stmt->close();
					array_push($deleted_associations, $e_a_sm_id);
				} else {
					$del_entity_assoc_stmt->close();
					return -1;
				}
			}
			$update_string .= "deleted e_a_sm id(s): ".implode('/', $deleted_associations);
			if (!empty($deleted_entities)) {
				$update_string .= ", deleted entity id(s): ".implode('/', $deleted_entities);
			}
			return 1;
		} else {
			$update_string .= "no entities to be removed from activity";
			return 0;
		}
	}
	function removeProfilesHelper($activity_id, &$update_string) {
		global $connection_production;

		$get_profiles_sql = "SELECT * FROM profile_activity_sm WHERE activityId=?";
		$get_profiles_stmt = $connection_production->prepare($get_profiles_sql);
		$get_profiles_stmt->bind_param("i", $activity_id);
		$get_profiles_stmt->execute();
		$get_profiles_result = $get_profiles_stmt->get_result();
		$get_profiles_stmt->close();

		if ($get_profiles_result->num_rows > 0) {

			$remove_profiles_sql = "DELETE FROM profile_activity_sm WHERE activityId=?";
			$remove_profiles_stmt = $connection_production->prepare($remove_profiles_sql);
			$remove_profiles_stmt->bind_param("i", $activity_id);
			if ($remove_profiles_stmt->execute()) {
				$remove_profiles_stmt->close();
				$deleted_associations = array();

				while ($row_p_a_sm = $get_profiles_result->fetch_assoc()) {
					array_push($deleted_associations, $row_p_a_sm['id']);
				}
				$update_string .= ", deleted p_a_sm id(s): ".implode('/', $deleted_associations);
				return 1;
			} else {
				$remove_profiles_stmt->close();
				return -1;
			}
		} else {
			$update_string .= ", no associated profiles to delete";
			return 0;
		}
	}

//======================================================================
// INSERT ACTIVITY
//======================================================================

	function insertActivity() {
		global $connection_production;

	    if (isset($_POST['create_activity'])) {
	        $activity_name = $_POST['activity_name'];
	        $activity_description = $_POST['activity_description'];
	        $activity_image = $_POST['activity_image'];

			if (!checkNoEntityAssoc($activity_name)) {

				$insert_activity_sql = "INSERT INTO activity (activity, description) VALUES (?, ?)";
				$insert_activity_stmt = $connection_production->prepare($insert_activity_sql);
				$insert_activity_stmt->bind_param("ss", $activity_name, $activity_description);
				if ($insert_activity_stmt->execute()) {
					$new_activity_id = $connection_production->insert_id;

					$insert_image_sql = "INSERT INTO image (imageName) VALUES (?)";
					$insert_image_stmt = $connection_production->prepare($insert_image_sql);
					$insert_image_stmt->bind_param("s", $activity_image);
					if ($insert_image_stmt->execute()) {
						$new_image_id = $connection_production->insert_id;

						$insert_assoc_sql = "INSERT INTO activity_image_sm (imageId, activityId) VALUES (?, ?)";
						$insert_assoc_stmt = $connection_production->prepare($insert_assoc_sql);
						$insert_assoc_stmt->bind_param("ii", $new_image_id, $new_activity_id);
						if ($insert_assoc_stmt->execute()) {
							$new_assoc_id = $connection_production->insert_id;

							if (updateNoEntityAssoc($new_activity_id, $activity_name)) {

								$update_string = 'new activity id: '.$new_activity_id.' :: name: '.$activity_name.', new image id: '.$new_image_id.', image name: '.$activity_image.', new a_i_sm_id: '.$new_assoc_id.', created no-entity-association';
								insertChange($_SESSION['account_id'], 'activity', 'add activity', $new_activity_id, $update_string);
								header("location: categories.php?source=sports_update&activity_id=".$new_activity_id);
							} else {
								echo "<h3 style='color:red'>Something went wrong creating no-entity-association</h3>";
							}
						} else {
							echo "<h3 style='color:red'>Something went inserting activity-image association</h3>";
						}
						$insert_assoc_stmt->close();
					} else {
						echo "<h3 style='color:red'>Something went wrong inserting image</h3>";
					}
					$insert_image_stmt->close();
				} else {
					echo "<h3 style='color:red'>Something went wrong inserting activity</h3>";
				}
				$insert_activity_stmt->close();
			} else {
				echo "<h3 style='color:red'>Activity name cannot have the 3 words 'no', 'entity', and 'association'</h3>";
			}
	    }
	}

//======================================================================
// UPDATE ACTIVITY
//======================================================================

	function updateActivity() {
		global $connection_production;
		if (isset($_POST['update_activity'])) {
			$activity_id = $_POST['activity_id'];
			$activity = $_POST['activity'];
			$activity_description = $_POST['activity_description'];
			$image_name = $_POST['image_name'];
			$image_id = $_POST['image_id'];

			$prev_activity_sql = "SELECT * FROM activity WHERE id=?";
			$prev_activity_stmt = $connection_production->prepare($prev_activity_sql);
			$prev_activity_stmt->bind_param("i", $activity_id);
			$prev_activity_stmt->execute();
			$row_prev_activity = $prev_activity_stmt->get_result()->fetch_assoc();
			$prev_activity_stmt->close();

			if (!!$row_prev_activity['id']) {
				if (!checkNoEntityAssoc($activity)) {
					$prev_activity = $row_prev_activity['activity'];
					$prev_desc = $row_prev_activity['description'];

					$update_activity_sql = "UPDATE activity SET activity=?, description=? WHERE id=?";
					$update_activity_stmt = $connection_production->prepare($update_activity_sql);
					$update_activity_stmt->bind_param("ssi", $activity, $activity_description, $activity_id);

					$update_image_sql = "UPDATE image SET imageName=? WHERE id=?";
					$update_image_stmt = $connection_production->prepare($update_image_sql);
					$update_image_stmt->bind_param("si", $image_name, $image_id);

					if ($update_activity_stmt->execute()) {
						if ($update_image_stmt->execute()) {
							$update_string = 'activity id: '.$activity_id.' :: update name: '.$activity.', image: '.$image_name;

							$noEntityUpdateError = false;
							if (strcasecmp($prev_activity, $activity) != 0) {
								if (updateNoEntityAssoc($activity_id, $activity)) {
									$update_string .= ", updated no-entity-association";
								} else {
									$noEntityUpdateError = true;
								}
							}
							if (!$noEntityUpdateError) {
								insertChange($_SESSION['account_id'], 'activity', 'update', $activity_id, $update_string);
								header("location: categories.php?source=sports_update&activity_id=".$activity_id);
							} else {
								echo "<h3 style='color:red'>Something went wrong updating no-entity-association</h3>";
							}
						} else {
							echo "<h3 style='color:red'>Something went wrong updating image</h3>";
						}
					} else {
						echo "<h3 style='color:red'>Something went wrong updating activity</h3>";
					}
					$update_activity_stmt->close();
					$update_image_stmt->close();
				} else {
					echo "<h3 style='color:red'>Activity name cannot have the 3 words 'no', 'entity', and 'association'</h3>";
				}
			} else {
				echo "<h3 style='color:red'>Could not find activity</h3>";
			}
		}
	}

	#Updates the name and description of an activity's no-entity-association
	#If no-entity-association is missing, creates and links a new one
	function updateNoEntityAssoc($activity_id, $activity_name) {
		global $connection_production;

		$activity_name = trim(strtolower($activity_name));
		$new_entity_name = "no ".$activity_name." entity association";
		$new_entity_desc = "No entity association with ".$activity_name;

		$get_entities_sql = "SELECT DISTINCT entity.id AS entity_id, entity.name AS entity_name,
			entity.description AS entity_description, entity_activity_sm.id AS e_a_sm_id FROM entity
			LEFT JOIN entity_activity_sm ON entity_activity_sm.entityId=entity.id
			WHERE entity_activity_sm.activityId=?";
		$get_entities_stmt = $connection_production->prepare($get_entities_sql);
		$get_entities_stmt->bind_param("i", $activity_id);
		$get_entities_stmt->execute();
		$result_get_entities = $get_entities_stmt->get_result();
		$get_entities_stmt->close();

		$noAssocNotFound = false;
		if ($result_get_entities->num_rows > 0) {
			while ($row_entity = $result_get_entities->fetch_assoc()) {
				$entity_id = $row_entity['entity_id'];
				$old_entity_name = strtolower($row_entity['entity_name']);

				if (checkNoEntityAssoc($old_entity_name)) {

					$update_entity_sql = "UPDATE entity SET name=?, description=? WHERE id=?";
					$update_entity_stmt = $connection_production->prepare($update_entity_sql);
					$update_entity_stmt->bind_param("ssi", $new_entity_name, $new_entity_desc, $entity_id);

					if ($update_entity_stmt->execute()) {
						$update_entity_stmt->close();
						return true;
					} else {
						$update_entity_stmt->close();
						return false;
					}
				}
			}
			$noAssocNotFound = true;
		} else {
			$noAssocNotFound = true;
		}
		if ($noAssocNotFound) {

			$new_entity_sql = "INSERT INTO entity (name, description) VALUES (?, ?)";
			$new_entity_stmt = $connection_production->prepare($new_entity_sql);
			$new_entity_stmt->bind_param("ss", $new_entity_name, $new_entity_desc);

			if ($new_entity_stmt->execute()) {
				$new_entity_id = $connection_production->insert_id;
				$new_entity_stmt->close();

				$assoc_entity_sql = "INSERT INTO entity_activity_sm (entityId, activityId) VALUES (?, ?)";
				$assoc_entity_stmt = $connection_production->prepare($assoc_entity_sql);
				$assoc_entity_stmt->bind_param("ii", $new_entity_id, $activity_id);

				if ($assoc_entity_stmt->execute()) {
					$assoc_entity_stmt->close();
					return true;
				} else {
					$assoc_entity_stmt->close();
					return false;
				}
			} else {
				$new_entity_stmt->close();
				return false;
			}
		}
		return false;
	}

//======================================================================
// ADD PROFILE
//======================================================================

	function addProfileToActivity() {
		global $connection_production;
		if (isset($_POST['add_profile'])) {
			$activity_id = $_POST['activity_id'];
			$profile_id = $_POST['profile_id'];

			$find_profile_sql = "SELECT * FROM profile WHERE id=?";
			$find_profile_stmt = $connection_production->prepare($find_profile_sql);
			$find_profile_stmt->bind_param("i", $profile_id);
			$find_profile_stmt->execute();
			$row_find_profile = $find_profile_stmt->get_result()->fetch_assoc();
			$find_profile_stmt->close();

			if (!!$row_find_profile['id']) {

				$search_assoc_sql = "SELECT * FROM profile_activity_sm WHERE profileId=? AND activityId=?";
				$search_assoc_stmt = $connection_production->prepare($search_assoc_sql);
				$search_assoc_stmt->bind_param("ii", $profile_id, $activity_id);
				$search_assoc_stmt->execute();
				$row_search_assoc = $search_assoc_stmt->get_result()->fetch_assoc();
				$search_assoc_stmt->close();

				if (!$row_search_assoc['id']) {

					$insert_assoc_sql = "INSERT INTO profile_activity_sm (profileId, activityId) VALUES (?, ?)";
					$insert_assoc_stmt = $connection_production->prepare($insert_assoc_sql);
					$insert_assoc_stmt->bind_param("ii", $profile_id, $activity_id);
					if ($insert_assoc_stmt->execute()) {

						$new_assoc_id = $connection_production->insert_id;
						$update_string = "activity id: ".$activity_id." :: add profile id: ".$profile_id.", added p_a_sm_id: ".$new_assoc_id;
						insertChange($_SESSION['account_id'], 'activity', 'add profile', $activity_id, $update_string);
						header("location: categories.php?source=sports_update&activity_id=".$activity_id."#add_profile");
					} else {
						echo "<h3 style='color:red'>Something went wrong</h3>";
					}
					$insert_assoc_stmt->close();
				} else {
					echo "<h3 style='color:red'>Profile ID: ".$profile_id." - Already added</h3>";
				}
			} else {
				echo "<h3 style='color:red'>Profile ID: ".$profile_id." - Not found</h3>";
			}
		}
	}

//======================================================================
// REMOVE PROFILE
//======================================================================

	function removeProfileFromActivity() {
		global $connection_production;
		if (isset($_POST['remove_profile'])) {
			$p_a_sm_id = $_POST['p_a_sm_id'];
			$profile_id = $_POST['profile_id'];
			$activity_id = $_POST['activity_id'];

			$check_assoc_sql = "SELECT * FROM profile_activity_sm WHERE id=?";
			$check_assoc_stmt = $connection_production->prepare($check_assoc_sql);
			$check_assoc_stmt->bind_param("i", $p_a_sm_id);
			$check_assoc_stmt->execute();
			$row_check_assoc = $check_assoc_stmt->get_result()->fetch_assoc();
			$check_assoc_stmt->close();

			if (!!$row_check_assoc['id']) {

				$delete_assoc_sql = "DELETE FROM profile_activity_sm WHERE id=?";
				$delete_assoc_stmt = $connection_production->prepare($delete_assoc_sql);
				$delete_assoc_stmt->bind_param("i", $p_a_sm_id);
				if ($delete_assoc_stmt->execute()) {

					$update_string = "activity id: ".$activity_id." :: remove profile id: ".$profile_id.", deleted p_a_sm_id: ".$p_a_sm_id;
					insertChange($_SESSION['account_id'], 'activity', 'remove profile', $activity_id, $update_string);
					header("location: categories.php?source=sports_update&activity_id=".$activity_id."#profiles");
				} else {
					echo "<h3 style='color:red'>Something went wrong</h3>";
				}
				$delete_assoc_stmt->close();
			} else {
				echo "<h3 style='color:red'>p_a_sm_id: ".$p_a_sm_id." - not found</h3>";
			}
		}
	}

//======================================================================
// ADD ENTITY
//======================================================================

	function addEntityToActivity() {
		global $connection_production;
		if (isset($_POST['add_entity'])) {
			$activity_id = $_POST['activity_id'];
			$entity_id = $_POST['entity_id'];

			$find_entity_sql = "SELECT * FROM entity WHERE id=?";
			$find_entity_stmt = $connection_production->prepare($find_entity_sql);
			$find_entity_stmt->bind_param("i", $entity_id);
			$find_entity_stmt->execute();
			$row_find_entity = $find_entity_stmt->get_result()->fetch_assoc();
			$find_entity_stmt->close();

			if (!!$row_find_entity['id']) {
				if (!checkNoEntityAssoc($row_find_entity['name'])) {

					$search_assoc_sql = "SELECT * FROM entity_activity_sm WHERE entityId=? AND activityId=?";
					$search_assoc_stmt = $connection_production->prepare($search_assoc_sql);
					$search_assoc_stmt->bind_param("ii", $entity_id, $activity_id);
					$search_assoc_stmt->execute();
					$row_search_assoc = $search_assoc_stmt->get_result()->fetch_assoc();
					$search_assoc_stmt->close();

					if (!$row_search_assoc['id']) {

						$insert_assoc_sql = "INSERT INTO entity_activity_sm (entityId, activityId) VALUES (?, ?)";
						$insert_assoc_stmt = $connection_production->prepare($insert_assoc_sql);
						$insert_assoc_stmt->bind_param("ii", $entity_id, $activity_id);
						if ($insert_assoc_stmt->execute()) {

							$new_assoc_id = $connection_production->insert_id;
							$update_string = "activity id: ".$activity_id." :: add entity (league) id: ".$entity_id.", added e_a_sm_id: ".$new_assoc_id;
							insertChange($_SESSION['account_id'], 'activity', 'add entity (league)', $activity_id, $update_string);
							header("location: categories.php?source=sports_update&activity_id=".$activity_id."#add_league");
						} else {
							echo "<h3 style='color:red'>Something went wrong</h3>";
						}
						$insert_assoc_stmt->close();
					} else {
						echo "<h3 style='color:red'>League ID: ".$entity_id." - Already added</h3>";
					}
				} else {
					echo "<h3 style='color:red'>You cannot add a non entity association</h3>";
				}
			} else {
				echo "<h3 style='color:red'>League ID: ".$entity_id." - Not found</h3>";
			}
		}
	}

//======================================================================
// REMOVE ENTITY
//======================================================================

	function removeEntityFromActivity() {
		global $connection_production;
		if (isset($_POST['remove_entity'])) {
			$e_a_sm_id = $_POST['e_a_sm_id'];
			$entity_id = $_POST['entity_id'];
			$activity_id = $_POST['activity_id'];

			$check_assoc_sql = "SELECT entity_activity_sm.id AS id, entity.name AS name FROM entity_activity_sm
				LEFT JOIN entity ON entity.id=entity_activity_sm.entityId
				WHERE entity_activity_sm.id=?";
			$check_assoc_stmt = $connection_production->prepare($check_assoc_sql);
			$check_assoc_stmt->bind_param("i", $e_a_sm_id);
			$check_assoc_stmt->execute();
			$row_check_assoc = $check_assoc_stmt->get_result()->fetch_assoc();
			$check_assoc_stmt->close();

			if (!!$row_check_assoc['id']) {
				if (!checkNoEntityAssoc($row_check_assoc['name'])) {

					$delete_assoc_sql = "DELETE FROM entity_activity_sm WHERE id=?";
					$delete_assoc_stmt = $connection_production->prepare($delete_assoc_sql);
					$delete_assoc_stmt->bind_param("i", $e_a_sm_id);
					if ($delete_assoc_stmt->execute()) {

						$update_string = "activity id: ".$activity_id." :: remove entity (league) id: ".$entity_id.", deleted e_a_sm_id: ".$e_a_sm_id;
						insertChange($_SESSION['account_id'], 'activity', 'remove entity (league)', $activity_id, $update_string);
						header("location: categories.php?source=sports_update&activity_id=".$activity_id."#leagues");
					} else {
						echo "<h3 style='color:red'>Something went wrong</h3>";
					}
					$delete_assoc_stmt->close();
				} else {
					echo "<h3 style='color:red'>You cannot delete a non entity association</h3>";
				}
			} else {
				echo "<h3 style='color:red'>League ID: ".$entity_id." - Not found</h3>";
			}
		}
	}
	function checkNoEntityAssoc($entity_name) {
		if ((stripos($entity_name, 'no') !== false)
			&& (stripos($entity_name, 'entity') !== false)
			&& (stripos($entity_name, 'association') !== false)) {

			return true;
		} else {
			return false;
		}
	}
?>
