<?php

//======================================================================
// FIND ALL PARTITIONS
//======================================================================

	function findAllPartitions($filter, $pageinate, $words) {
		global $connection_production;

        $query_partition = "SELECT DISTINCT partition_LP.id AS partition_id, partition_LP.partition AS partition_name,
			partition_LP.description AS partition_description, partition_LP.partitionTypeId AS type_id
			FROM `partition` AS partition_LP
			LEFT JOIN partition_type ON partition_type.id=partition_LP.partitionTypeId "
			.$filter." LIMIT ?, 20";
		$stmt = $connection_production->prepare($query_partition);

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
		}
		$bind_parameters[0] = $bind_parameters[0]."i";
		array_push($bind_parameters, $pageinate);
		call_user_func_array(array($stmt, "bind_param"), refValues($bind_parameters));
		$stmt->execute();
		$select_all_partition_query = $stmt->get_result();
		$stmt->close();

		echo "<style>.noverflow {max-height:5vh; overflow-y:auto}</style>";
        while ($row = mysqli_fetch_assoc($select_all_partition_query)) {
        	if (substr($row['partition_description'],0,25) != 'No partition association ') {
            	$partition_id = $row['partition_id'];
            	$partition_name = $row['partition_name'];
            	$partition_description = $row['partition_description'];
            	$type_id = $row['type_id'];

            	echo "<tr>";
            	echo "<td>".$partition_id."</td>";
            	echo "<td>".$partition_name."</td>";
            	echo "<td><div class='noverflow'>".$partition_description."</div></td>";
            	echo "<td>".$type_id."</td>";
            	echo "<td><a class='btn btn-info' href='categories.php?source=update_partition&partition_id=".$partition_id."'>Edit</a></td>";
            	echo "<form method='post'><input type='hidden' name='partition_id' value=".$partition_id.">";
            	echo "<td><input class='btn btn-danger' type='submit' name='delete_partition' value='Delete'></td>";
            	// echo "<td><a onClick=\"javascript: return confirm('Are you sure you want to delete?');\" href='categories.php?delete_partition=".$partition_id."&partition_id=".$partition_id."'>DELETE</a></td>";
            	echo "</form>";
            	echo "</tr>";
        	}
        }
	}

//======================================================================
// DELETE LEAGUE
//======================================================================

    function deletePartition() {

        global $connection_production;

        if (isset($_POST['delete_league'])) {

            echo 'working';
        }
    }

//======================================================================
// UPDATE LEAGUE INFO
//======================================================================

    function updatePartitionInfo() {
        global $connection_production;
        if (isset($_POST['update_partition_info'])) {
            $null = NULL;
            $partition_id_set = $_POST['partition_id_set'];
            $partition_name = $_POST['partition_name'];
            $partition_alternate_name = $_POST['partition_alternate_name'];
            $partition_type = (int)$_POST['partition_type'];
            $partition_description = $_POST['partition_description'];
            $partition_alternate_name_id = $_POST['partition_alternate_name_id'];
            $partition_alternate_name_sm_id = $_POST['partition_alternate_name_sm_id'];
            $nine = 9;

            if (($partition_name == "" || empty($partition_name)) || ($partition_type == "" || empty($partition_type))) {
                echo "<h3 style='color:red;'>name and activity fields can not be empty</h3>";
            } else {

                $sql_partition = "UPDATE `partition` SET `partition`=?, description=?, partitionTypeId=? WHERE id=?";

                $prepared_partition = $connection_production->prepare($sql_partition);
                $prepared_partition->bind_param("ssii", $partition_name, $partition_description, $partition_type, $partition_id_set);
                $result_partition=$prepared_partition->execute();
                queryCheckProduction($result_partition);
                $prepared_partition->close();

                $update_string = 'partition name: '.$partition_name.' , description: '.$partition_description.' , partition type: '.$partition_type;

                if (!!$partition_alternate_name && ($partition_alternate_name_id == "" || empty($partition_alternate_name_id))) {

                    $sql_a_n = "INSERT INTO alternate_name (alternateName, alternateNameTypeId) VALUES (?, ?)";

                    $prepared_a_n = $connection_production->prepare($sql_a_n);
                    $prepared_a_n->bind_param("si", $partition_alternate_name, $nine);
                    $result_a_n=$prepared_a_n->execute();
                    $last_partition_alternate_name_id = $connection_production->insert_id;
                    createUUID('alternate_name', $last_partition_alternate_name_id);
                    queryCheckProduction($result_a_n);
                    $prepared_a_n->close();

                    $sql_e_a_n = "INSERT INTO partition_alternate_name_sm (partitionId, alternateNameId) VALUES (?, ?)";

                    $prepared_e_a_n = $connection_production->prepare($sql_e_a_n);
                    $prepared_e_a_n->bind_param("ii", $partition_id_set, $last_partition_alternate_name_id);
                    $result_e_a_n=$prepared_e_a_n->execute();
                    $last_partition_alternate_name_sm_id = $connection_production->insert_id;
                    queryCheckProduction($result_e_a_n);
                    $prepared_e_a_n->close();

                    $update_string .= ', added alternate name: '.$partition_alternate_name.' , and partition_alternate_name_sm id: '.$last_partition_alternate_name_sm_id;

                } else if (!!$partition_alternate_name && ($partition_alternate_name_id != "" || !empty($partition_alternate_name_id))) {

                    $sql_a_n = "UPDATE alternate_name SET alternateName=? WHERE id=?";

                    $prepared_a_n = $connection_production->prepare($sql_a_n);
                    $prepared_a_n->bind_param("si", $partition_alternate_name, $partition_alternate_name_id);
                    $result_a_n=$prepared_a_n->execute();
                    queryCheckProduction($result_a_n);
                    $prepared_a_n->close();

                    $update_string .= ', updated alternate name: '.$partition_alternate_name;
                } else if ((!$partition_alternate_name || $partition_alternate_name == "" || empty($partition_alternate_name)) && ($partition_alternate_name_id != "" || !empty($partition_alternate_name_id))) {

                    $sql_a_n = "DELETE FROM alternate_name WHERE id=?";

                    $prepared_a_n = $connection_production->prepare($sql_a_n);
                    $prepared_a_n->bind_param("i", $partition_alternate_name_id);
                    $result_a_n=$prepared_a_n->execute();
                    queryCheckProduction($result_a_n);
                    $prepared_a_n->close();

                    $sql_e_a_n = "DELETE FROM partition_alternate_name_sm WHERE id=?";

                    $prepared_e_a_n = $connection_production->prepare($sql_e_a_n);
                    $prepared_e_a_n->bind_param("i", $partition_alternate_name_sm_id);
                    $result_e_a_n=$prepared_e_a_n->execute();
                    queryCheckProduction($result_e_a_n);
                    $prepared_e_a_n->close();

                    $update_string .= ', deleted alternate name id:'.$partition_alternate_name_id.' , and partition_alternate_name_sm id: '.$partition_alternate_name_sm_id ;
                }
                header("location: categories.php?source=update_partition&partition_id=".$partition_id_set);
                insertChange($_SESSION['account_id'], 'partition', 'update partition', $partition_id_set, $update_string);
            }
        }
    }

//======================================================================
// ADD LEAGUE INFO
//======================================================================

    function addPartitionInfo() {
        global $connection_production;
        if (isset($_POST['add_partition_info'])) {
            $partition_name = $_POST['partition_name'];
            $partition_alternate_name = $_POST['partition_alternate_name'];
            $partition_type = (int)$_POST['partition_type'];
            $partition_description = $_POST['partition_description'];
            $nine = 9;

            if (($partition_name == "" || empty($partition_name)) || ($partition_type == "" || empty($partition_type))) {
                echo "<h3 style='color:red;'>name and activity fields can not be empty</h3>";
            } else {

                $sql_partition = "INSERT INTO `partition` (`partition`, description, partitionTypeId) VALUES (?, ?, ?)";

                $prepared_partition = $connection_production->prepare($sql_partition);
                $prepared_partition->bind_param("ssi", $partition_name, $partition_description, $partition_type);
                $result_partition=$prepared_partition->execute();
                $last_partition_id = $connection_production->insert_id;
                queryCheckProduction($result_partition);
                createUUID('`partition`', $last_partition_id);
                $prepared_partition->close();

                if (!!$partition_alternate_name) {

                    $sql_a_n = "INSERT INTO alternate_name (alternateName, alternateNameTypeId) VALUES (?, ?)";

                    $prepared_a_n = $connection_production->prepare($sql_a_n);
                    $prepared_a_n->bind_param("si", $partition_alternate_name, $nine);
                    $result_a_n=$prepared_a_n->execute();
                    $last_partition_alternate_name_id = $connection_production->insert_id;
                    createUUID('alternate_name', $last_partition_alternate_name_id);
                    $prepared_a_n->close();
                    queryCheckProduction($result_a_n);

                    $sql_t_a_n = "INSERT INTO partition_alternate_name_sm (partitionId, alternateNameId) VALUES (?, ?)";

                    $prepared_t_a_n = $connection_production->prepare($sql_t_a_n);
                    $prepared_t_a_n->bind_param("ii", $last_partition_id, $last_partition_alternate_name_id);
                    $result_t_a_n=$prepared_t_a_n->execute();
                    $prepared_t_a_n->close();
                    queryCheckProduction($result_t_a_n);
                }

                $update_string = 'partition name: '.$partition_name.', type: '.$partition_type.' , description: '.$partition_description;
                if (!!$partition_alternate_name) {
					$update_string .= ', alternate name: '.$partition_alternate_name;
				}
                insertChange($_SESSION['account_id'], 'partition', 'add partition', $last_partition_id, $update_string);
                header("location: categories.php?source=update_partition&partition_id=".$last_partition_id);
            }
        }
    }

//======================================================================
// ADD LEAGUE PARTITION
//======================================================================

    function addEntityToPartition() {
        global $connection_production;
        if (isset($_POST['add_entity_partition'])) {
            $post_partition_id_set = $_POST['partition_id_set'];
            $entity_id = $_POST['entity_id'];
            $partition_found = false;

            $get_partition_sql = "SELECT DISTINCT entity_activity_sm_partition_sm.partitionId AS p_id FROM entity_activity_sm_partition_sm
				LEFT JOIN entity_activity_sm ON entity_activity_sm_partition_sm.entityActivitySmId = entity_activity_sm.id
				LEFT JOIN entity ON entity.id = entity_activity_sm.entityId
				WHERE entity.id=?";

            $get_partition_stmt = $connection_production->prepare($get_partition_sql);
            $get_partition_stmt->bind_param("i", $entity_id);
            $get_partition_stmt->execute();
            $get_partition_result = $get_partition_stmt->get_result();
            $get_partition_stmt->close();

            if ($get_partition_result->num_rows > 0) {
                while ($row_partition = $get_partition_result->fetch_assoc()) {
                    if ($row_partition['p_id'] == $post_partition_id_set) {
                        $partition_found = true;
                        break;
                    }
                }
            } else {
                $partition_found = false;
            }

            if ($partition_found == false) {

                $get_entity_activity_sql = "SELECT DISTINCT entity_activity_sm.id AS e_a_sm_id FROM entity
					LEFT JOIN entity_activity_sm ON entity_activity_sm.id = entity.id
					WHERE entity.id=?";

                $get_entity_activity_stmt = $connection_production->prepare($get_entity_activity_sql);
                $get_entity_activity_stmt->bind_param("i", $entity_id);
                $get_entity_activity_stmt->execute();
                $get_entity_activity_result = $get_entity_activity_stmt->get_result();
                $row_entity_activity_result = $get_entity_activity_result->fetch_assoc();
                $get_entity_activity_stmt->close();
                $e_a_sm_id = $row_entity_activity_result['e_a_sm_id'];

                $sql_entity_activity_sm_partition_sm = "INSERT INTO entity_activity_sm_partition_sm(partitionId, entityActivitySmId) VALUES (?,?)";

                $stmt_entity_activity_sm_partition_sm = $connection_production->prepare($sql_entity_activity_sm_partition_sm);
                $stmt_entity_activity_sm_partition_sm->bind_param("ii", $post_partition_id_set, $e_a_sm_id);
                $stmt_entity_activity_sm_partition_sm->execute();
                $last_entity_activity_sm_partition_sm = $connection_production->insert_id;
                $stmt_entity_activity_sm_partition_sm->close();

                $update_string = 'partition: '.$post_partition_id_set.' added entity activity parition association id: '.$last_entity_activity_sm_partition_sm.', added entity id: '.$entity_id;
                insertChange($_SESSION['account_id'], 'partition', 'add entity', $post_partition_id_set, $update_string);
                header("location: categories.php?source=update_partition&partition_id=".$post_partition_id_set."#add_partition_entity");
            } else {
                echo "<h3 style='color:red;'>entity id: ".$entity_id." - already associated</h3>";
            }
        }
    }

//======================================================================
// DELETE LEAGUE PARTITION
//======================================================================

    function deleteEntityFromPartition() {
        global $connection_production;
        if (isset($_POST['delete_entity_partition'])) {
            $post_partition_id_set = $_POST['partition_id_set'];
            $e_a_sm_p_sm_id = $_POST['e_a_sm_p_sm_id'];
            $entity_id = $_POST['entity_id'];

            $sql_delete_e_a_sm_p_sm = "DELETE FROM entity_activity_sm_partition_sm WHERE id=?";

            $stmt_delete_e_a_sm_p_sm = $connection_production->prepare($sql_delete_e_a_sm_p_sm);
            $stmt_delete_e_a_sm_p_sm->bind_param("i", $e_a_sm_p_sm_id);
            $stmt_delete_e_a_sm_p_sm->execute();
            $last_delete_e_a_sm_p_sm = $connection_production->insert_id;
            $stmt_delete_e_a_sm_p_sm->close();

            $update_string = 'partition: '.$post_partition_id_set.' deleted entity parition association id: '.$e_a_sm_p_sm_id.', removed entity id: '.$entity_id;
            insertChange($_SESSION['account_id'], 'partition', 'delete entity', $post_partition_id_set, $update_string);
            header("location: categories.php?source=update_partition&partition_id=".$post_partition_id_set."#partition_entity");
        }
    }

            // $stmt_leagues_team = "SELECT DISTINCT team.id AS team_id FROM team
            //     LEFT JOIN franchise_team_sm ON franchise_team_sm.teamId=team.id
            //     LEFT JOIN franchise ON franchise.id=franchise_team_sm.franchiseId
            //     LEFT JOIN partition_franchise_sm ON partition_franchise_sm.franchiseId=franchise.id
            //     LEFT JOIN `partition` AS partition_LP ON partition_LP.id=partition_franchise_sm.partitionId
            //     LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id
            //     LEFT JOIN entity_activity_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
            //     LEFT JOIN entity ON entity.id = entity_activity_sm.entityId
            //      WHERE entity.id=?";


//======================================================================
// DELETE TEAM PARTITION
//======================================================================

    function deleteTeamFromPartition() {
        global $connection_production;
        if (isset($_POST['delete_team_partition'])) {
            $post_partition_id_set = $_POST['partition_id_set'];
            $team_id = $_POST['team_id'];
            $p_f_sm_id = $_POST['p_f_sm_id'];

            $sql_delete_p_f_sm_id = "DELETE FROM partition_franchise_sm WHERE id=?";

            $stmt_delete_p_f_sm_id = $connection_production->prepare($sql_delete_p_f_sm_id);
            $stmt_delete_p_f_sm_id->bind_param("i", $p_f_sm_id);
            $stmt_delete_p_f_sm_id->execute();
            $last_delete_p_f_sm_id = $connection_production->insert_id;
            $stmt_delete_p_f_sm_id->close();

            $update_string = 'partition: '.$post_partition_id_set.' deleted team parition association id: '.$p_f_sm_id.', removed team id: '.$team_id;
            insertChange($_SESSION['account_id'], 'partition', 'delete team', $post_partition_id_set, $update_string);
            header("location: categories.php?source=update_partition&partition_id=".$post_partition_id_set."#partition_teams");
        }
    }

//======================================================================
// DELETE TEAM PARTITION
//======================================================================

    function addTeamToPartition() {
        global $connection_production;
        if (isset($_POST['add_team_partition'])) {
            $post_partition_id_set = $_POST['partition_id_set'];
            $team_id = $_POST['team_id'];

            if ($team_id == "" || empty($team_id) || !$team_id) {
                echo "<h3 style='color:red;'>a team id needs to be entered</h3>";
            } else {
                $stmt_team_franshise_search = "SELECT DISTINCT franchise.id AS franchise_id, franchise_team_sm.teamId AS team_id FROM franchise
                    LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
                    WHERE franchise_team_sm.teamId=?";
                $prepared_team_franshise_search = $connection_production->prepare($stmt_team_franshise_search);
                $prepared_team_franshise_search->bind_param('i', $team_id);
                $prepared_team_franshise_search->execute();
                $result_team_franshise_search = $prepared_team_franshise_search->get_result();
                $prepared_team_franshise_search->close();

                if ($result_team_franshise_search->num_rows > 0) {
                    $row_team_franshise_search = $result_team_franshise_search->fetch_assoc();
                    if ($result_team_franshise_search->num_rows == 1) {
                        if (!!$row_team_franshise_search['franchise_id']) {
                            $franchise_id = $row_team_franshise_search['franchise_id'];

                            $sql_partition_franchise = "INSERT INTO partition_franchise_sm(partitionId, franchiseId) VALUES (?,?)";

                            $stmt_partition_franchise = $connection_production->prepare($sql_partition_franchise);
                            $stmt_partition_franchise->bind_param("ii", $post_partition_id_set, $franchise_id);
                            $stmt_partition_franchise->execute();
                            $last_partition_franchise_id = $connection_production->insert_id;
                            $stmt_partition_franchise->close();

                            $update_string = 'partition: '.$post_partition_id_set.' added team partition association id: '.$last_partition_franchise_id.', added team id: '.$team_id;
                            insertChange($_SESSION['account_id'], 'partition', 'add team to partition', $post_partition_id_set, $update_string);
                            header("location: categories.php?source=update_partition&partition_id=".$post_partition_id_set."#add_partition_team");
                        }
                    } else if ($result_team_franshise_search->num_rows > 1) {
                        echo "<h3 style='color:red;'>more than one franchise associated with team</h3>";
                    }
                } else {
                    echo "<h3 style='color:red;'>team not found or doesn't have franchise association</h3>";
                }
            }
        }
    }
?>
