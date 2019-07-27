<?php

//======================================================================
// FIND ALL ENTITIES
//======================================================================
	function findAllEntities($filter, $words) {
		global $connection_production;

        $query = "SELECT entity.id AS entity_id, entity.name AS entity_name, image.id AS image_id,
			image.imageName AS imageName, entity_image_sm.id AS entity_image_sm_id,
			entity.description AS description FROM entity
			LEFT JOIN entity_image_sm ON entity_image_sm.entityId = entity.id
			LEFT JOIN image ON entity_image_sm.imageId = image.id ";
		$query .= $filter;
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
        $select_all_entity_query = $stmt->get_result();
		$stmt->close();

		echo "<style>.noverflow {max-height:5vh; overflow-y:auto}</style>";
        while ($row = mysqli_fetch_assoc($select_all_entity_query)) {
            if (substr($row['description'],0,22) != 'No entity association ') {
                $entity_id = $row['entity_id'];
                $entity_name = $row['entity_name'];
                $imageName = $row['imageName'];
                $entity_description = $row['description'];

                echo "<tr>";
                echo "<td>".$entity_id."</td>";
                echo "<td>".$entity_name."</td>";
                echo "<td>".$imageName."</td>";
                echo "<td><div class='noverflow'>".$entity_description."</div></td>";
                echo "<td><a class='btn btn-info' href='categories.php?source=league_update&entity_id=".$entity_id."'>Edit</a></td>";
                echo "<form method='post'><input type='hidden' name='entity_id' value=".$entity_id.">";
                echo "<td><input class='btn btn-danger' type='submit' name='delete_entity' value='DELETE'></td>";
                // echo "<td><a onClick=\"javascript: return confirm('Are you sure you want to delete?');\" href='categories.php?delete_entity=".$entity_id."&entity_id=".$entity_id."'>DELETE</a></td>";
                echo "</form>";
                echo "</tr>";
            }
        }
	}


//======================================================================
// DELETE LEAGUE
//======================================================================
    function deleteLeague() {

        global $connection_production;

        if (isset($_POST['delete_league'])) {

            echo 'working';
        }
    }

//======================================================================
// UPDATE LEAGUE INFO
//======================================================================
    function updateEntityInfo() {
        global $connection_production;

        if (isset($_POST['update_league_info'])) {
            $null = NULL;
            $entity_id_set = $_POST['entity_id_set'];
            $entity_name = $_POST['entity_name'];
            $entity_alternate_name = $_POST['entity_alternate_name'];
            $activity_id = $_POST['activity'];
            $entity_description = $_POST['entity_description'];
            $entity_alternate_name_id = $_POST['entity_alternate_name_id'];
            $entity_activity_sm_id = $_POST['entity_activity_sm_id'];
            $entity_alternate_name_sm_id = $_POST['entity_alternate_name_sm_id'];
            $nine = 9;

            if (($entity_name == "" || empty($entity_name)) || ($activity_id == "" || empty($activity_id))) {
                echo "<h3 style='color:red;'>name and activity fields can not be empty</h3>";
            } else {

                $get_partition_sql = "SELECT DISTINCT entity_activity_sm_partition_sm.partitionId AS p_id,
					entity_activity_sm.activityId AS activity_id FROM entity_activity_sm_partition_sm
					LEFT JOIN entity_activity_sm ON entity_activity_sm_partition_sm.entityActivitySmId = entity_activity_sm.id
					LEFT JOIN entity ON entity.id = entity_activity_sm.entityId
					WHERE entity.id=?";

                $get_partition_stmt = $connection_production->prepare($get_partition_sql);
                $get_partition_stmt->bind_param("i", $entity_id_set);
                $get_partition_stmt->execute();
                $get_partition_result = $get_partition_stmt->get_result();
                $row_partition = $get_partition_result->fetch_assoc();
                $get_partition_stmt->close();

                if ($row_partition['activity_id'] != $activity_id && $get_partition_result->num_rows > 0) {
                    echo "<h3 style='color:red;'>you cannot change activity because you have partitions assigned to entity</h3>";
                } else {
                    $sql_entity = "UPDATE entity SET name=?, description=? WHERE id=?";

                    $prepared_entity = $connection_production->prepare($sql_entity);
                    $prepared_entity->bind_param("ssi", $entity_name, $entity_description, $entity_id_set);
                    $result_entity=$prepared_entity->execute();
                    queryCheckProduction($result_entity);
                    $prepared_entity->close();

                    $update_string = 'league name: '.$entity_name;

                    $sql_e_a = "UPDATE entity_activity_sm SET activityId=? WHERE entityId=?";

                    $prepared_e_a = $connection_production->prepare($sql_e_a);
                    $prepared_e_a->bind_param("ii", $activity_id, $entity_id_set);
                    $result_e_a=$prepared_e_a->execute();
                    $prepared_e_a->close();
                    queryCheckProduction($result_e_a);

                    $update_string .=', activity: '.$activity_id.' , description: '.$entity_description;

                    if (!!$entity_alternate_name && ($entity_alternate_name_id == "" || empty($entity_alternate_name_id))) {

                        $sql_a_n = "INSERT INTO alternate_name (alternateName, alternateNameTypeId) VALUES (?, ?)";

                        $prepared_a_n = $connection_production->prepare($sql_a_n);
                        $prepared_a_n->bind_param("si", $entity_alternate_name, $nine);
                        $result_a_n=$prepared_a_n->execute();
                        $last_entity_alternate_name_id = $connection_production->insert_id;
                        createUUID('alternate_name', $last_entity_alternate_name_id);
                        queryCheckProduction($result_a_n);
                        $prepared_a_n->close();

                        $sql_e_a_n = "INSERT INTO entity_alternate_name_sm (entityId, alternateNameId) VALUES (?, ?)";

                        $prepared_e_a_n = $connection_production->prepare($sql_e_a_n);
                        $prepared_e_a_n->bind_param("ii", $entity_id_set, $last_entity_alternate_name_id);
                        $result_e_a_n=$prepared_e_a_n->execute();
                        $last_entity_alternate_name_sm_id = $connection_production->insert_id;
                        queryCheckProduction($result_e_a_n);
                        $prepared_e_a_n->close();

                        $update_string .= ', added alternate name: '.$entity_alternate_name.' , and entity_alternate_name_sm id: '.$last_entity_alternate_name_sm_id;

                    } else if (!!$entity_alternate_name && ($entity_alternate_name_id != "" || !empty($entity_alternate_name_id))) {

                        $sql_a_n = "UPDATE alternate_name SET alternateName=? WHERE id=?";

                        $prepared_a_n = $connection_production->prepare($sql_a_n);
                        $prepared_a_n->bind_param("si", $entity_alternate_name, $entity_alternate_name_id);
                        $result_a_n=$prepared_a_n->execute();
                        queryCheckProduction($result_a_n);
                        $prepared_a_n->close();

                        $update_string .= ', updated alternate name: '.$entity_alternate_name;
                    } else if ((!$entity_alternate_name || $entity_alternate_name == "" || empty($entity_alternate_name)) && ($entity_alternate_name_id != "" || !empty($entity_alternate_name_id))) {

                        $sql_a_n = "DELETE FROM alternate_name WHERE id=?";

                        $prepared_a_n = $connection_production->prepare($sql_a_n);
                        $prepared_a_n->bind_param("i", $entity_alternate_name_id);
                        $result_a_n=$prepared_a_n->execute();
                        queryCheckProduction($result_a_n);
                        $prepared_a_n->close();

                        $sql_e_a_n = "DELETE FROM entity_alternate_name_sm WHERE id=?";

                        $prepared_e_a_n = $connection_production->prepare($sql_e_a_n);
                        $prepared_e_a_n->bind_param("i", $entity_alternate_name_sm_id);
                        $result_e_a_n=$prepared_e_a_n->execute();
                        queryCheckProduction($result_e_a_n);
                        $prepared_e_a_n->close();

                        $update_string .= ', deleted alternate name id:'.$entity_alternate_name_id.' , and entity_alternate_name_sm id: '.$entity_alternate_name_sm_id ;
                    }
                    header("location: categories.php?source=league_update&entity_id=".$entity_id_set);
                    insertChange($_SESSION['account_id'], 'entity', 'update league', $entity_id_set, $update_string);
                }
            }
        }
    }

//======================================================================
// ADD LEAGUE INFO
//======================================================================
    function addLeagueInfo() {
        global $connection_production;
        if (isset($_POST['add_league_info'])) {
            $null = NULL;
            $entity_name = $_POST['entity_name'];
            $entity_alternate_name = $_POST['entity_alternate_name'];
            $activity_id = $_POST['activity'];
            $entity_description = $_POST['entity_description'];
            $nine = 9;

            if (($entity_name == "" || empty($entity_name)) || ($activity_id == "" || empty($activity_id))) {
                echo "<h3 style='color:red;'>name and activity fields can not be empty</h3>";
            } else {

                $sql_entity = "INSERT INTO entity (name, description) VALUES (?, ?)";

                $prepared_entity = $connection_production->prepare($sql_entity);
                $prepared_entity->bind_param("ss", $entity_name, $entity_description);
                $result_entity=$prepared_entity->execute();
                $last_entity_id = $connection_production->insert_id;
                queryCheckProduction($result_entity);
                createUUID('entity', $last_entity_id);
                $prepared_entity->close();

                $sql_f_t = "INSERT INTO entity_activity_sm (entityId, activityId) VALUES (?, ?)";

                $prepared_f_t = $connection_production->prepare($sql_f_t);
                $prepared_f_t->bind_param("ii", $last_entity_id, $activity_id);
                $result_f_t=$prepared_f_t->execute();
                $prepared_f_t->close();
                queryCheckProduction($result_f_t);

                if (!!$entity_alternate_name) {

                    $sql_a_n = "INSERT INTO alternate_name (alternateName, alternateNameTypeId) VALUES (?, ?)";

                    $prepared_a_n = $connection_production->prepare($sql_a_n);
                    $prepared_a_n->bind_param("si", $entity_alternate_name, $nine);
                    $result_a_n=$prepared_a_n->execute();
                    $last_entity_alternate_name_id = $connection_production->insert_id;
                    createUUID('alternate_name', $last_entity_alternate_name_id);
                    $prepared_a_n->close();
                    queryCheckProduction($result_a_n);

                    $sql_t_a_n = "INSERT INTO entity_alternate_name_sm (entityId, alternateNameId) VALUES (?, ?)";

                    $prepared_t_a_n = $connection_production->prepare($sql_t_a_n);
                    $prepared_t_a_n->bind_param("ii", $last_entity_id, $last_entity_alternate_name_id);
                    $result_t_a_n=$prepared_t_a_n->execute();
                    $prepared_t_a_n->close();
                    queryCheckProduction($result_t_a_n);
                }
                $update_string = 'league name: '.$entity_name.', activity: '.$activity_id.' , description: '.$entity_description;
                if (!!$entity_alternate_name) { $update_string .= ', alternate name: '.$entity_alternate_name; }

                insertChange($_SESSION['account_id'], 'entity', 'add league', $last_entity_id, $update_string);
                header("location: categories.php?source=league_update&league_id=".$last_entity_id);
            }
        }
    }

//======================================================================
// ADD LEAGUE PARTITION
//======================================================================
    function addPartitionToLeague() {
        global $connection_production;
        if (isset($_POST['add_partition_league'])) {
            $post_entity_id_set = $_POST['entity_id_set'];
            $partition_id = $_POST['partition_id'];
            $partition_found = false;

            $get_partition_sql = "SELECT DISTINCT entity_activity_sm_partition_sm.partitionId AS p_id FROM entity_activity_sm_partition_sm
				LEFT JOIN entity_activity_sm ON entity_activity_sm_partition_sm.entityActivitySmId = entity_activity_sm.id
				LEFT JOIN entity ON entity.id = entity_activity_sm.entityId
				WHERE entity.id=?";

            // entity LEFT JOIN entity_activity_sm ON entity_activity_sm.id = entity.id LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.entityActivitySmId = entity_activity_sm.id LEFT JOIN `partition` AS lp_partition ON lp_partition.id = entity_activity_sm_partition_sm.partitionId LEFT JOIN partition_type ON partition_type.id=lp_partition.partitionTypeId WHERE entity.id=? ORDER BY entity_activity_sm_partition_sm.id ASC";
            $get_partition_stmt = $connection_production->prepare($get_partition_sql);
            $get_partition_stmt->bind_param("i", $post_entity_id_set);
            $get_partition_stmt->execute();
            $get_partition_result = $get_partition_stmt->get_result();
            $get_partition_stmt->close();

            if ($get_partition_result->num_rows > 0) {
                while ($row_partition = $get_partition_result->fetch_assoc()) {
                    if ($row_partition['p_id'] == $partition_id) {
                        $partition_found = true;
                        break;
                    }
                }
            } else {
                $partition_found = false;
            }

            if ($partition_found == false) {
                $get_entity_activity_sql = "SELECT DISTINCT entity_activity_sm.id AS e_a_sm_id FROM entity LEFT JOIN entity_activity_sm ON entity_activity_sm.id = entity.id WHERE entity.id=?";
                $get_entity_activity_stmt = $connection_production->prepare($get_entity_activity_sql);
                $get_entity_activity_stmt->bind_param("i", $post_entity_id_set);
                $get_entity_activity_stmt->execute();
                $get_entity_activity_result = $get_entity_activity_stmt->get_result();
                $row_entity_activity_result = $get_entity_activity_result->fetch_assoc();
                $get_entity_activity_stmt->close();
                $e_a_sm_id = $row_entity_activity_result['e_a_sm_id'];

                $sql_entity_activity_sm_partition_sm = "INSERT INTO entity_activity_sm_partition_sm(partitionId, entityActivitySmId) VALUES (?,?)";

                $stmt_entity_activity_sm_partition_sm = $connection_production->prepare($sql_entity_activity_sm_partition_sm);
                $stmt_entity_activity_sm_partition_sm->bind_param("ii", $partition_id, $e_a_sm_id);
                $stmt_entity_activity_sm_partition_sm->execute();
                $last_entity_activity_sm_partition_sm = $connection_production->insert_id;
                $stmt_entity_activity_sm_partition_sm->close();

                $update_string = 'league: '.$_POST['entity_id_set'].' added entity activity parition association id: '.$last_entity_activity_sm_partition_sm.', added partition id: '.$partition_id;
                insertChange($_SESSION['account_id'], 'entity', 'add partition', $post_entity_id_set, $update_string);
                header("location: categories.php?source=league_update&entity_id=".$post_entity_id_set."#add_partition");
            } else {
                echo "<h3 style='color:red;'>partition id: ".$partition_id." - already associated</h3>";
            }
        }
    }

//======================================================================
// DELETE LEAGUE PARTITION
//======================================================================
    function deletePartitionFromLeague() {
        global $connection_production;
        if (isset($_POST['delete_partition_league'])) {
            $post_entity_id_set = $_POST['entity_id_set'];
            $e_a_sm_p_sm_id = $_POST['e_a_sm_p_sm_id'];
            $p_id = $_POST['p_id'];

            $sql_delete_e_a_sm_p_sm = "DELETE FROM entity_activity_sm_partition_sm WHERE id=?";

            $stmt_delete_e_a_sm_p_sm = $connection_production->prepare($sql_delete_e_a_sm_p_sm);
            $stmt_delete_e_a_sm_p_sm->bind_param("i", $e_a_sm_p_sm_id);
            $stmt_delete_e_a_sm_p_sm->execute();
            $last_delete_e_a_sm_p_sm = $connection_production->insert_id;
            $stmt_delete_e_a_sm_p_sm->close();

            $update_string = 'league: '.$_POST['entity_id_set'].' deleted entity activity parition association id: '.$e_a_sm_p_sm_id.', removed partition id: '.$p_id;
            insertChange($_SESSION['account_id'], 'entity', 'delete partition', $post_entity_id_set, $update_string);
            header("location: categories.php?source=league_update&entity_id=".$post_entity_id_set."#partitions");
        }
    }
?>
