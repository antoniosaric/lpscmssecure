<?php

//======================================================================
// FIND ALL TEAMS
//======================================================================
	function findAllTeams($filter, $pageinate, $words) {
		global $connection_production;

        $sql = "SELECT DISTINCT team.id AS team_id, image.imageName AS imageName, team.name AS team_name, team.locale AS locale,
			team.description AS team_description, alternate_name.alternateName AS nickname, activity.id AS activity_id,
			activity.activity AS activity, location.city AS city, location.stateProvince AS stateProvince, location.country AS country
            FROM team LEFT JOIN team_image_sm ON team_image_sm.teamId=team.id
            LEFT JOIN image ON image.id=team_image_sm.imageId
            LEFT JOIN team_location_sm ON team_location_sm.teamId=team.id
            LEFT JOIN location ON location.id=team_location_sm.locationId
            LEFT JOIN team_alternate_name_sm ON team_alternate_name_sm.teamId = team.id
            LEFT JOIN alternate_name ON alternate_name.id = team_alternate_name_sm.alternateNameId
            LEFT JOIN franchise_team_sm ON franchise_team_sm.teamId=team.id
            LEFT JOIN franchise ON franchise.id=franchise_team_sm.franchiseId
            LEFT JOIN partition_franchise_sm ON partition_franchise_sm.franchiseId=franchise.id
            LEFT JOIN `partition` AS partition_LP ON partition_LP.id=partition_franchise_sm.partitionId
            LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id
            LEFT JOIN entity_activity_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
            LEFT JOIN entity ON entity.id = entity_activity_sm.entityId
            LEFT JOIN activity ON activity.id=entity_activity_sm.activityId "
			.$filter." LIMIT ?, 20";
		$stmt = $connection_production->prepare($sql);

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
        $select_all_teams_query = $stmt->get_result();
		$stmt->close();

        while ($row = mysqli_fetch_assoc($select_all_teams_query)) {
            $team_id = $row['team_id'];
            $activity = $row['activity'];
            $imageName = $row['imageName'];
            $locale = $row['locale'];
            $team_name = $row['team_name'];

            echo "<tr>";
            echo "<td>".$team_id."</td>";
            echo "<td>".$activity."</td>";
            $imageName_set = !!$row['imageName'] ? 'Yes' : 'No';
            echo "<td>".$imageName_set."</td>";
            echo "<td>".$locale." | ".$team_name."</td>";
            $team_description_set = !!$row['team_description'] ? 'Yes' : 'No';
            echo "<td>".$team_description_set."</td>";
            $location_set = !!$row['city'] && !!$row['country'] ? 'Yes' : 'No';
            echo "<td>".$location_set."</td>";
            echo "<td><a class='btn btn-info' href='categories.php?source=team_update&team_id=".$team_id."'>Edit</a></td>";
            echo "<form method='post'><input type='hidden' name='team_id' value=".$team_id.">";
            echo "<td><input class='btn btn-danger' type='submit' name='delete_team' value='DELETE'></td>";
            // echo "<td><a onClick=\"javascript: return confirm('Are you sure you want to delete?');\" href='categories.php?delete_activity=".$activity_id."&activity_id=".$activity_id."'>DELETE</a></td>";
            echo "</form>";
            echo "</tr>";
        }
	}

	function findAllTeamVideos($team_id, $pageinate) {
		global $connection_production;

		$team_video_list_sql = "SELECT DISTINCT video.id AS video_id, profile_franchise_sm.profileId AS video_profile_id,
			video.reference AS reference, video.title AS title, video.summary AS video_summary, video.videoStatus AS video_status,
			video_source.source AS video_source, profile_franchise_sm_video_sm.id AS p_f_sm_v_sm_id FROM video
			LEFT JOIN video_source ON video_source.id = video.videoSourceId
			LEFT JOIN profile_franchise_sm_video_sm ON profile_franchise_sm_video_sm.videoId = video.id
			LEFT JOIN profile_franchise_sm ON profile_franchise_sm.id = profile_franchise_sm_video_sm.profileFranchiseSmId
			LEFT JOIN franchise ON franchise.id = profile_franchise_sm.franchiseId
			LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId = franchise.id
			WHERE franchise_team_sm.teamId = ?
			ORDER BY video.id DESC
			LIMIT ?, 20";
		$team_video_list_stmt = $connection_production->prepare($team_video_list_sql);
		$team_video_list_stmt->bind_param('ii', $team_id, $pageinate);
		$team_video_list_stmt->execute();
		$team_video_list_result = $team_video_list_stmt->get_result();
		$team_video_list_stmt->close();

		echo "<style>.noverflow {max-height:5vh; overflow-y:auto}</style>";
		while ($row_team_video_list = mysqli_fetch_assoc($team_video_list_result)) {
			$video_id = $row_team_video_list['video_id'];
			$video_profile_id = $row_team_video_list['video_profile_id'];
			$video_title = $row_team_video_list['title'];
			$video_summary = $row_team_video_list['video_summary'];
			$video_status = $row_team_video_list['video_status'];
			$video_source = $row_team_video_list['video_source'];
			$p_f_sm_v_sm_id = $row_team_video_list['p_f_sm_v_sm_id'];

			echo "<tr>";
			echo "<td><a href='categories.php?source=update_video&video_id=".$video_id."'>".$video_id."</a></td>";
			echo "<td><a href='categories.php?source=profile_update_athlete&profile_id=".$video_profile_id."'>".$video_profile_id."</a></td>";
			echo "<td><div class='noverflow'>".$video_title."</div></td>";
			echo "<td><div class='noverflow'>".$video_summary."</div></td>";
			echo "<td>".$video_status."</td>";
			echo "<td>".$video_source."</td>";
			echo "<td>";

			echo "<form method='post'><input type='hidden' name='delete_video_id' value=".$video_id.">";
			echo "<form method='post'><input type='hidden' name='profile_id' value=".$video_profile_id.">";
			echo "<form method='post'><input type='hidden' name='p_f_sm_v_sm_id' value=".$p_f_sm_v_sm_id.">";
			echo "<form method='post'><input type='hidden' name='team_id' value=".$team_id.">";
	?>
			<input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_team_video' value='DELETE'>
	<?php
			echo "</form>";
			echo "</tr>";
		}
	}

	function findAllTeamProfiles($team_id, $activity_id, $pageinate) {
		global $connection_production; ?>
		<form method='post'>
			<input type="hidden" name="profile_id_set" value="<?php echo $team_id; ?>">
			<?php
				$stmt_profile = "SELECT DISTINCT profile.id AS profile_id, participant.firstName AS first_name,
					participant.lastName AS last_name, participant_suffix.suffix AS suffix, profile.profileTypeId AS profile_type,
					profile_franchise_sm.id AS profile_franchise_sm_id FROM profile
					LEFT JOIN profile_franchise_sm ON profile_franchise_sm.profileId=profile.id
					LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
					LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
					LEFT JOIN team ON team.id=franchise_team_sm.teamId
					LEFT JOIN participant ON profile.participantId=participant.id
					LEFT JOIN participant_suffix ON participant_suffix.id=participant.participantSuffixId
					WHERE team.id = ?
					ORDER BY profile.id DESC
					LIMIT ?, 20";
				$prepared_profile = $connection_production->prepare($stmt_profile);
				$prepared_profile->bind_param('ii', $team_id, $pageinate);
				$result_profile = $prepared_profile->execute();
				$result_of_query_profile = $prepared_profile->get_result();
				$prepared_profile->close();

				if (!!$result_profile && $result_of_query_profile->num_rows > 0) {
					while ($row_profile = mysqli_fetch_assoc($result_of_query_profile)) {
						$profile_id = $row_profile['profile_id'];
						$profile_name = $row_profile['first_name'].' '.$row_profile['last_name'];
						$row_profile['suffix'] ? $profile_name .= ' '.$row_profile['suffix'] : null;
						$profile_type = ($row_profile['profile_type'] == 1 || $row_profile['profile_type'] == 0) ? 'athlete' : 'coach';
						$profile_franchise_sm_id = $row_profile['profile_franchise_sm_id'];

						echo "<tr>";
						echo "<td><a href='categories.php?source=profile_update_athlete&profile_id=".$profile_id."'>".$profile_id."</a></td>";
						echo "<td>".$profile_name."</td>";
						echo "<td>".$profile_type."</td>";
						echo "<td>";

						if ($profile_type == 1 || $profile_type == 0) {

							$query_profile_franchise_sm_id = "SELECT DISTINCT activity_role.id AS activity_role_id,
								activity_role.role AS role, profile_franchise_sm_activity_role_sm.id AS p_f_sm_a_r_sm_id FROM activity_role
								LEFT JOIN profile_franchise_sm_activity_role_sm ON profile_franchise_sm_activity_role_sm.activityRoleId=activity_role.id
								WHERE profile_franchise_sm_activity_role_sm.profileFranchiseSmId=".$profile_franchise_sm_id;

							$select_all_profile_franchise_sm_id = mysqli_query($connection_production, $query_profile_franchise_sm_id);

							if (!!$select_all_profile_franchise_sm_id && mysqli_num_rows($select_all_profile_franchise_sm_id) > 0) {
								while ($row_profile_franchise_sm_id = mysqli_fetch_assoc($select_all_profile_franchise_sm_id)) {
									$p_f_sm_a_r_sm_id = $row_profile_franchise_sm_id['p_f_sm_a_r_sm_id'];
									$role_name = $row_profile_franchise_sm_id['role'];

									echo "<form method='post'>";
									echo "<input type='hidden' name='p_f_sm_a_r_sm_id' value=".$p_f_sm_a_r_sm_id.">".$role_name."    ";
									echo "<input type='hidden' name='role_name' value=".$role_name.">";
									echo "<input type='hidden' name='profile_id' value=".$profile_id.">";
									echo "<input type='hidden' name='team_role_id' value=".$team_id.">";
					?>
									<input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='Delete_role' value='Delete'>
					<?php
									echo "</form>";
								}
							}

							echo "<form method='post' action=''>";
							echo "<input type='hidden' name='profile_id' value=".$profile_id.">";
							echo "<input type='hidden' name='team_role_id' value=".$team_id.">";
							echo "<input type='hidden' name='profile_franchise_sm_id' value=".$profile_franchise_sm_id.">";
							echo "<select name='post_role_info'>";
								echo "<option value=''>position...</option>";
								$query_role = "SELECT activity_role.id AS activity_role_id, activity_role.role AS role FROM activity_role
									WHERE activity_role.activityId=".$activity_id;
								$select_all_role = mysqli_query($connection_production, $query_role);

								while ($row_role = mysqli_fetch_assoc($select_all_role)) {
									$activity_role_id = $row_role['activity_role_id'];
									$role = $row_role['role'];
									echo "<option value=".$activity_role_id."|".$role.">".$role."</option>";
								}
							echo "</select>";
							echo "<input class='btn btn-primary' type='submit' name='add_role' value='Add'>";
							echo "</form>";
							echo "</td>";

						} else if ($profile_type == 2) {

							$query_profile_franchise_sm_id = "SELECT DISTINCT specialty.id AS specialty_id, specialty.specialty AS specialty,
								profile_specialty_sm.id AS p_s_sm_id FROM specialty
								LEFT JOIN profile_specialty_sm ON profile_specialty_sm.specialtyId=specialty.id
								WHERE profile_specialty_sm.profileId=".$profile_id;

							$select_all_profile_id = mysqli_query($connection_production, $query_profile_franchise_sm_id);
							$row_profile_id = mysqli_fetch_assoc($select_all_profile_id);
							$p_s_sm_id = $row_profile_id['p_s_sm_id'];
							$specialty = $row_profile_id['specialty'];

							echo "<form method='post' action=''>";
							echo "<input type='hidden' name='p_s_sm_id' value=".$p_s_sm_id.">";
							echo "<input type='hidden' name='profile_id' value=".$profile_id.">";
							echo "<input value='".$specialty."' type='text' class='form-control' name='specialty'>";
							echo "<input class='btn btn-info' type='submit' name='update_profile_team_specialty' value='Update'>";
							echo "</form>";
							echo "</td>";
						}

						echo "<td>";
						$query_profile_team_period_sm = "SELECT DISTINCT profile_franchise_sm.id AS profile_franchise_sm_id,
							franchise.id AS franchise_id, profile_franchise_sm_period_sm.id AS profile_franchise_sm_period_sm_id,
							period.id AS period_id, period.start AS period_start, period.end AS period_end FROM profile
							LEFT JOIN profile_franchise_sm ON profile_franchise_sm.profileId=profile.id
							LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
							LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
							LEFT JOIN team ON team.id=franchise_team_sm.teamId
							LEFT JOIN profile_franchise_sm_period_sm ON profile_franchise_sm_period_sm.profileFranchiseSmId=profile_franchise_sm.id
							LEFT JOIN period ON period.id=profile_franchise_sm_period_sm.periodId
							WHERE profile.id=".$profile_id." AND team.id=".$team_id;

						$select_all_profile_team_period_sm = mysqli_query($connection_production, $query_profile_team_period_sm);

						if (!!$select_all_profile_team_period_sm && mysqli_num_rows($select_all_profile_team_period_sm) > 0) {
							while ($row_profile_team_period_sm = mysqli_fetch_assoc($select_all_profile_team_period_sm)) {

								$profile_franchise_sm_id = $row_profile_team_period_sm["profile_franchise_sm_id"];
								$profile_franchise_sm_period_sm_id = $row_profile_team_period_sm["profile_franchise_sm_period_sm_id"];
								$franchise_id = $row_profile_team_period_sm['franchise_id'];
								$period_id = $row_profile_team_period_sm["period_id"];
								$period_start = $row_profile_team_period_sm["period_start"];
								$period_end = $row_profile_team_period_sm["period_end"];
								$period_time = $period_start." - ".$period_end;

								if (!!$period_id && $period_id != null) {
									echo "<form method='post'>";
									echo "<input type='hidden' name='delete_period_id' value=".$period_id.">".$period_start." - ".$period_end."    ";
									echo "<input type='hidden' name='period_time' value='".$period_time."'>";
									echo "<input type='hidden' name='profile_id' value=".$profile_id.">";
									echo "<input type='hidden' name='profile_franchise_sm_period_sm_id' value=".$profile_franchise_sm_period_sm_id."><input type='hidden' name='team_period_id' value=".$team_id.">";
				?>
									<input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_period_association' value='Delete'>
				<?php
									echo "</form>";
								}
							}
						}

						echo "<form method='post' action=''>";
						echo "<input type='hidden' name='profile_id' value=".$profile_id.">";
						echo "<input type='hidden' name='team_period_id' value=".$team_id.">";
						echo "<input type='hidden' name='profile_franchise_sm_id' value=".$profile_franchise_sm_id.">";
						echo "<select name='post_team_period_start'>";
						echo "<option value=''>start...</option>";

						$years = '';
						$startyear_start = date('Y'); // This year
						$endyear_start = date('Y', mktime(0,0,0,0,0,date('Y')-50)); // Three years ahead
						$startyear_end = date('Y'); // This year
						$endyear_end = date('Y', mktime(0,0,0,0,0,date('Y')-50)); // Three years ahead
						foreach(range($startyear_start, $endyear_start) as $year_start) {
							echo '<option value="'. $year_start . '">' . $year_start . "</option>";
						}

						echo "</select>";
						echo "     ";
						echo "<select name='post_team_period_end'>";
						echo "<option value=''>end...</option>";
						echo "<option value='Present'>Present</option>";
						$years = '';
						$startyear_end = date('Y'); // This year
						$endyear_end = date('Y', mktime(0,0,0,0,0,date('Y')-50)); // Three years ahead
						foreach(range($startyear_end, $endyear_end) as $year_end) {
							echo '<option value="'. $year_end . '">' . $year_end . "</option>";
						}
						echo "</select>";

						echo "<input class='btn btn-primary' type='submit' name='add_team_period' value='Add'>";
						echo "</form>";
						echo "</td>";
						echo "<td>";
						echo "<form method='post'>";
						echo "<input type='hidden' name='query_profile_id' value=".$profile_id.">";
						echo "<input type='hidden' name='team_id_set' value=".$team_id.">";
						echo "<input type='hidden' name='franchise_id' value=".$franchise_id.">";
						echo "<input type='hidden' name='profile_franchise_sm_id' value=".$profile_franchise_sm_id.">";
			?>
						<input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_profile_from_team_association' value='Delete'>
			<?php
						echo "</form>";
						echo "</td>";
						echo "</tr>";
					}
				}
			?>
		</form>
		<?php
	}

//======================================================================
// DELETE TEAM
//======================================================================

	function deleteTeam() {
		global $connection_production;
        if (isset($_POST['delete_team'])) {

        }
	}

//======================================================================
// ADD TEAM
//======================================================================

	function addTeamProfile() {
		global $connection_production;
	    if (isset($_POST['add_team_info'])) {
			$null = NULL;
	        $locale = $_POST['locale'];
	        $name = $_POST['name'];
	        $nickname = $_POST['nickname'];
	        $status = $_POST['status'];
	        $team_description = $_POST['teamdescription'];
            $one = 1;

	        if (($name == "" || empty($name)) || ($locale == "" || empty($locale))) {
	            echo "name, locale and activity fields can not be empty";
	        } else {

				$sql_f = "INSERT INTO franchise (description) VALUES (?)";

				$prepared_f = $connection_production->prepare($sql_f);
				$prepared_f->bind_param("s", $null);
				$result_f=$prepared_f->execute();
				$last_franchise_id = $connection_production->insert_id;
	    		queryCheckProduction($result_f);
	    		createUUID('franchise', $last_franchise_id);
				$prepared_f->close();

				$sql_t = "INSERT INTO team (name, locale, description, status) VALUES (?, ?, ?, ?)";

				$prepared_t = $connection_production->prepare($sql_t);
				$prepared_t->bind_param("ssss", $name, $locale, $team_description, $status);
				$result_t=$prepared_t->execute();
				$last_team_id = $connection_production->insert_id;
	    		queryCheckProduction($result_t);
	    		createUUID('team', $last_team_id);
				$prepared_t->close();

				$sql_f_t = "INSERT INTO franchise_team_sm (teamId, franchiseId) VALUES (?, ?)";

				$prepared_f_t = $connection_production->prepare($sql_f_t);
				$prepared_f_t->bind_param("ii", $last_team_id, $last_franchise_id);
				$result_f_t=$prepared_f_t->execute();
				$prepared_f_t->close();
	    		queryCheckProduction($result_f_t);

                if (!!$nickname) {
                    $sql_a_n = "INSERT INTO alternate_name (alternateName, alternateNameTypeId) VALUES (?, ?)";

                    $prepared_a_n = $connection_production->prepare($sql_a_n);
                    $prepared_a_n->bind_param("si", $nickname, $one);
                    $result_a_n=$prepared_a_n->execute();
                    $last_nickname_id = $connection_production->insert_id;
                    createUUID('alternate_name', $last_nickname_id);
                    $prepared_a_n->close();
                    queryCheckProduction($result_a_n);

                    $sql_t_a_n = "INSERT INTO team_alternate_name_sm (teamId, alternateNameId) VALUES (?, ?)";

                    $prepared_t_a_n = $connection_production->prepare($sql_t_a_n);
                    $prepared_t_a_n->bind_param("ii", $last_team_id, $last_nickname_id);
                    $result_t_a_n=$prepared_t_a_n->execute();
                    $prepared_t_a_n->close();
                    queryCheckProduction($result_t_a_n);
                }

	        	$update_string = 'team locale and name: '.$name.' '.$locale.', status: '.$status.' , description: '.$team_description;
                if (!!$nickname) {
					$update_string .= ', nickname: '.$nickname;
				}
	        	insertChange($_SESSION['account_id'], 'team', 'add team', $last_team_id, $update_string);
            	header("location: categories.php?source=team_update&team_id=".$last_team_id."#profiles");
            }
	    }
	}

//======================================================================
// UPDATE TEAM INFO
//======================================================================

	function updateTeamInfo() {
		global $connection_production;
        if (isset($_POST['update_team'])) {
            $team_id = $_POST['team_id'];
            $team_description = $_POST['team_description'];
            $locale = $_POST['locale'];
            $name = $_POST['name'];
            $status = $_POST['team_status'];
            $alternate_name_id = $_POST['alternate_name_id'];
            $nickname = $_POST['nickname'];
            $one = 1;

            if (($name == "" || empty($name)) || ($locale == "" || empty($locale))) {
                echo "name, locale and activity fields can not be empty";
            } else {
                $stmt = "UPDATE team SET name=?, locale=?, description=?, status=? WHERE id=?";
                $prepared = $connection_production->prepare($stmt);
                $prepared->bind_param('ssssi', $name, $locale, $team_description, $status, $team_id);

                $result=$prepared->execute();
                $prepared->close();
                queryCheckProduction($result);

                if ($alternate_name_id != NULL) {
                    $sqlnickname = "UPDATE alternate_name SET alternateName=? WHERE id=?";

                    $stmtnickname = $connection_production->prepare($sqlnickname);
                    $stmtnickname->bind_param("si", $nickname, $alternate_name_id);
                    $stmtnickname->execute();
                    $stmtnickname->close();
                } else {
                    $sql_a_n = "INSERT INTO alternate_name (alternateName, alternateNameTypeId) VALUES (?, ?)";

                    $prepared_a_n = $connection_production->prepare($sql_a_n);
                    $prepared_a_n->bind_param("si", $nickname, $one);
                    $result_a_n=$prepared_a_n->execute();
                    $last_nickname_id = $connection_production->insert_id;
                    createUUID('alternate_name', $last_nickname_id);
                    $prepared_a_n->close();
                    queryCheckProduction($result_a_n);

                    $sql_t_a_n = "INSERT INTO team_alternate_name_sm (teamId, alternateNameId) VALUES (?, ?)";

                    $prepared_t_a_n = $connection_production->prepare($sql_t_a_n);
                    $prepared_t_a_n->bind_param("ii", $team_id, $last_nickname_id);
                    $result_t_a_n=$prepared_t_a_n->execute();
                    $prepared_t_a_n->close();
                    queryCheckProduction($result_t_a_n);
                }
                $update_string = 'team locale and name: '.$_POST['locale'].' '.$_POST['name'].' , description: '.$_POST['team_description'].' , status: '.$status. ' , nickname: '.$nickname;
    	        insertChange($_SESSION['account_id'], 'team', 'update team', $team_id, $update_string);
                header("location: categories.php?source=team_update&team_id=".$team_id);
            }
        }
	}

//======================================================================
// ADD LEAGUE AND PARTITION
//======================================================================

    function addLeaguePartition() {
        global $connection_production;
        if (isset($_POST['add_team_entity_partition'])) {
            $team_id = $_POST['team_id'];
            $team_partition_id = $_POST['team_partition'];
            $league_id = $_POST['league'];

            $sql_partition_franchise_sm = "SELECT DISTINCT franchise.id AS franchise_id FROM team
				LEFT JOIN franchise_team_sm ON franchise_team_sm.teamId=team.id
				LEFT JOIN franchise ON franchise.id=franchise_team_sm.franchiseId
				WHERE team.id=?";
            $prepared = $connection_production->prepare($sql_partition_franchise_sm);
            $prepared->bind_param('i', $team_id);
            $result = $prepared->execute();
            $result_of_query = $prepared->get_result();

            if (!!$result && $result_of_query->num_rows == 1) {
                $row = $result_of_query->fetch_assoc();
                $franchise_id = $row['franchise_id'];

                $sql_partition_franchise = "INSERT INTO partition_franchise_sm(partitionId, franchiseId) VALUES (?, ?)";

                $prepared_partition_franchise = $connection_production->prepare($sql_partition_franchise);
                $prepared_partition_franchise->bind_param("ii", $team_partition_id, $franchise_id);
                $result_partition_franchise=$prepared_partition_franchise->execute();
                $prepared_partition_franchise->close();

                if (!$result_partition_franchise) {
                    die('QUERY FAILED' . mysqli_error($connection_production));
                } else {
                    $update_string = 'team id: '.$team_id.' , partition id: '.$team_partition_id;
                    insertChange($_SESSION['account_id'], 'team', 'add - partition', $team_id, $update_string);
                    header("location: categories.php?source=team_update&team_id=".$team_id."#add_league_partition");
                }
            } else {
                echo "<h3 style='color:red;'>Franchise not found</h3>";
            }
        }
    }

//======================================================================
// DELETE LEAGUE AND PARTITION
//======================================================================

    function deleteLeaguePartition() {
        global $connection_production;
        if (isset($_POST['delete_team_entity_partition'])) {
            $team_id = $_POST['team_id'];
            $entity_id = $_POST['entity_id'];
            $entity_name = $_POST['entity_name'];
            $partition_id = $_POST['partition_id'];
            $partition_name = $_POST['partition_name'];
            $activity_id = $_POST['activity_id'];
            $activity = $_POST['activity'];
            $franchise_id = $_POST['franchise_id'];
            $partition_franchise_sm_id = $_POST['partition_franchise_sm_id'];

            $sql_delete_partition_franchise = "DELETE FROM partition_franchise_sm WHERE id=?";

            $prepared_delete_partition_franchise = $connection_production->prepare($sql_delete_partition_franchise);
            $prepared_delete_partition_franchise->bind_param("i", $partition_franchise_sm_id);
            $result_delete_partition_franchise=$prepared_delete_partition_franchise->execute();
            $prepared_delete_partition_franchise->close();

            if (!$result_delete_partition_franchise) {
                die('QUERY FAILED' . mysqli_error($connection_production));
            } else {
                $update_string = 'team id: '.$team_id.' , deleted partition id: '.$team_partition_id.' , partition: '.$partition_name;
                insertChange($_SESSION['account_id'], 'team', 'delete - partition', $team_id, $update_string);
                header("location: categories.php?source=team_update&team_id=".$team_id."#league_partition");
            }
        }
    }

//======================================================================
// ADD ATHLETE POSITION TO TEAM
//======================================================================
    function addTeamAthletePosition() {
        global $connection_production;
        if (isset($_POST['add_role'])) {
            if (!!$_POST['post_role_info']) {
                $role_not_found = true;
                $result_role_submit = $_POST['post_role_info'];
                $result_explode_role_submit = explode('|', $result_role_submit);
                $post_role_id = $result_explode_role_submit[0];
                $post_role_name = $result_explode_role_submit[1];
                $team_role_id = $_POST['team_role_id'];
                $post_profile_id_set = $_POST['profile_id_set'];
                $profile_franchise_sm_id = $_POST['profile_franchise_sm_id'];

                $sql_search_add_role = "SELECT DISTINCT team.id AS team_id, profile.id AS profile_id,
					profile_franchise_sm_activity_role_sm.id AS p_f_sm_a_r_sm_i, profile_franchise_sm_activity_role_sm.activityRoleId AS role_id
					FROM profile LEFT JOIN profile_franchise_sm ON profile_franchise_sm.profileId=profile.id
					LEFT JOIN profile_franchise_sm_activity_role_sm ON profile_franchise_sm_activity_role_sm.profileFranchiseSmId=profile_franchise_sm.id
					LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
					LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
					LEFT JOIN team ON team.id=franchise_team_sm.teamId
					WHERE profile.id=? AND team.id=?";
                $stmt_search_add_role = $connection_production->prepare($sql_search_add_role);
                $stmt_search_add_role->bind_param("ii", $post_profile_id_set, $team_role_id);
                $stmt_search_add_role->execute();
                $result_search_add_role = $stmt_search_add_role->get_result();
                $stmt_search_add_role->close();

                while ($row_search_add_role = $result_search_add_role->fetch_assoc()) {
                    if (strtolower($row_search_add_role['role_id']) == strtolower($post_role_id)) {
                        $role_not_found = false;
                        break;
                    }
                }
                if (!!$role_not_found) {

                    $sql_p_f_sm_a_r_sm = "INSERT INTO profile_franchise_sm_activity_role_sm (activityRoleId, profileFranchiseSmId) VALUES (?, ?)";
                    $prepared_p_f_sm_a_r_sm = $connection_production->prepare($sql_p_f_sm_a_r_sm);
                    $prepared_p_f_sm_a_r_sm->bind_param("ii", $post_role_id, $profile_franchise_sm_id);
                    $result_p_f_sm_a_r_sm=$prepared_p_f_sm_a_r_sm->execute();
                    $last_id = $connection_production->insert_id;
                    $prepared_p_f_sm_a_r_sm->close();

                    $update_string = 'profile: '.$_POST['profile_id_set'].' add role id: '.$post_role_id.', role name: '.$post_role_name.' to team: '.$team_role_id.', added profile frnchise activity role join table id: '.$last_id;

                    insertChange($_SESSION['account_id'], 'profile', 'add role', $post_profile_id_set, $update_string);
                    header("location: categories.php?source=team_update&team_id=".$post_team_id_set."#profiles");
                } else {
                   echo "<h3 style='color:red;'>role: ".$post_role_name." - already exists on team id: ".$team_role_id."</h3>";
                }
            } else {
                echo "<h3 style='color:red;'>You need to select a role</h3>";
            }
        }
    }

//======================================================================
// DELETE ATHLETE POSITION FROM TEAM
//======================================================================
    function deleteTeamAthletePosition() {
        global $connection_production;
        if (isset($_POST['delete_role'])) {
            if (!!$_POST['p_f_sm_a_r_sm_id']) {
	            $team_role_id = $_POST['team_role_id'];
	            $post_profile_id_set = $_POST['profile_id_set'];
	            $p_f_sm_a_r_sm_id = $_POST['p_f_sm_a_r_sm_id'];
	            $role_name = $_POST['role_name'];

	            $sql_delete_p_f_sm_a_r_sm = "DELETE FROM profile_franchise_sm_activity_role_sm WHERE id=?";
	            $stmt_delete_p_f_sm_a_r_sm = $connection_production->prepare($sql_delete_p_f_sm_a_r_sm);
	            $stmt_delete_p_f_sm_a_r_sm->bind_param("i", $p_f_sm_a_r_sm_id);
	            $stmt_delete_p_f_sm_a_r_sm->execute();
	            $stmt_delete_p_f_sm_a_r_sm->close();

	            $update_string = 'profile: '.$_POST['profile_id_set'].' deleted role : '. $role_name.', from team id: '.$team_role_id.', deleted profile franchse activity role join table id: '.$p_f_sm_a_r_sm_id;
	            insertChange($_SESSION['account_id'], 'profile', 'deleted role', $post_profile_id_set, $update_string);
	            header("location: categories.php?source=team_update&team_id=".$post_team_id_set."#profiles");
            } else {
                echo "<h3 style='color:red;'>You need to select a role</h3>";
            }
        }
    }

//======================================================================
// ADD ATHLETE PERIOD TO TEAM
//======================================================================
    function addTeamAthletePeriod() {
        global $connection_production;
        if (isset($_POST['add_team_period'])) {
            if (!!$_POST['post_team_period_start'] && !!$_POST['post_team_period_end']) {
                $post_team_period_start = $_POST['post_team_period_start'];
                $post_team_period_end = $_POST['post_team_period_end'];
                $team_period_id = $_POST['team_period_id'];
                $post_profile_id_set = $_POST['profile_id_set'];
                $profile_franchise_sm_id = $_POST['profile_franchise_sm_id'];

                $stmt_add_team_period = "INSERT INTO period (start, end) VALUES (?, ?)";
                $prepared_add_team_period = $connection_production->prepare($stmt_add_team_period);
                $prepared_add_team_period->bind_param("ss", $post_team_period_start, $post_team_period_end);
                $result_add_team_period=$prepared_add_team_period->execute();
                $period_id = $connection_production->insert_id;
                $prepared_add_team_period->close();

                $sql_add_profile_franchise_sm_period_sm = "INSERT INTO profile_franchise_sm_period_sm (periodId, profileFranchiseSmId) VALUES (?, ?)";
                $prepared_add_profile_franchise_sm_period_sm = $connection_production->prepare($sql_add_profile_franchise_sm_period_sm);
                $prepared_add_profile_franchise_sm_period_sm->bind_param("ii", $period_id, $profile_franchise_sm_id);
                $result_add_profile_franchise_sm_period_sm=$prepared_add_profile_franchise_sm_period_sm->execute();
                $p_f_sm_p_sm_id = $connection_production->insert_id;
                $prepared_add_profile_franchise_sm_period_sm->close();

                $update_string = 'profile: '.$_POST['profile_id_set'].' add period : '. $post_team_period_start.' - '.$post_team_period_end.', for team id: '.$team_period_id.', added profile franchise period join table id: '.$p_f_sm_p_sm_id;
                insertChange($_SESSION['account_id'], 'profile', 'add period', $post_profile_id_set, $update_string);
                header("location: categories.php?source=team_update&team_id=".$post_team_id_set."#profiles");
            } else {
                echo "<h3 style='color:red;'>You need to select a period</h3>";
            }
        }
    }

//======================================================================
// DELETE ATHLETE PERIOD FROM TEAM
//======================================================================

    function deleteTeamAthletePeriod() {
        global $connection_production;
        if (isset($_POST['delete_period_association'])) {
            if (!!$_POST['delete_period_id']) {
                $team_period_id = $_POST['team_period_id'];
                $post_profile_id_set = $_POST['profile_id_set'];
                $delete_period_id = $_POST['delete_period_id'];
                $delete_profile_franchise_sm_period_sm_id = $_POST['profile_franchise_sm_period_sm_id'];
                $period_time = $_POST['period_time'];

                $stmt_delete_p_f_sm_a_r_sm = "DELETE FROM profile_franchise_sm_period_sm WHERE id=?";
                $stmt_delete_p_f_sm_a_r_sm = $connection_production->prepare($stmt_delete_p_f_sm_a_r_sm);
                $stmt_delete_p_f_sm_a_r_sm->bind_param("i", $delete_profile_franchise_sm_period_sm_id);
                $stmt_delete_p_f_sm_a_r_sm->execute();
                $stmt_delete_p_f_sm_a_r_sm->close();

                $stmt_delete_period = "DELETE FROM period WHERE id=?";
                $stmt_delete_period = $connection_production->prepare($stmt_delete_period);
                $stmt_delete_period->bind_param("i", $delete_period_id);
                $stmt_delete_period->execute();
                $stmt_delete_period->close();

                $update_string = 'profile: '.$_POST['profile_id_set'].' deleted period : '.$period_time.', from team id: '.$team_period_id.', deleted profile franchse activity period join table id: '.$delete_profile_franchise_sm_period_sm_id;
                insertChange($_SESSION['account_id'], 'profile', 'deleted period', $post_profile_id_set, $update_string);
                header("location: categories.php?source=team_update&team_id=".$post_team_id_set."#profiles");
            } else {
                echo "<h3 style='color:red;'>You need to select a role</h3>";
            }
        }
    }

//======================================================================
// ADD ATHLETE PROFILE TO TEAM
//======================================================================

    function addProfileToTeam() {
        global $connection_production;
        if (isset($_POST['add_profile_to_team'])) {
            if (!!$_POST['profile_id']) {
                $post_team_id_set = $_POST['team_id_set'];
                $post_profile_id = $_POST['profile_id'];

                $sql_search_team_found = "SELECT DISTINCT profile.id AS profile_id, franchise.id AS franchise_id FROM profile
					LEFT JOIN profile_franchise_sm ON profile_franchise_sm.profileId=profile.id
					LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
					LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
					LEFT JOIN team ON team.id=franchise_team_sm.teamId
					WHERE profile.id=? AND team.id=?";
                $stmt_search_team_found = $connection_production->prepare($sql_search_team_found);
                $stmt_search_team_found->bind_param("ii", $post_profile_id, $post_team_id_set);
                $stmt_search_team_found->execute();
                $result_search_team_found = $stmt_search_team_found->get_result();
                $row_search_team_found = $result_search_team_found->fetch_assoc();
                $stmt_search_team_found->close();

                if ($result_search_team_found->num_rows == 0 || (!$row_search_team_found['profile_id'] || $row_search_team_found['profile_id'] == NULL) && (!$row_search_team_found['franchise_id'] || $row_search_team_found['franchise_id'] == NULL)) {

                    $sql_search_team_franchise = "SELECT DISTINCT team.id AS team_id, team.name AS team_name,
						franchise.id AS franchise_id FROM team
						LEFT JOIN franchise_team_sm ON team.id=franchise_team_sm.teamId
						LEFT JOIN franchise ON franchise.id=franchise_team_sm.franchiseId
						WHERE team.id=?";
                    $prepared_search_team_franchise = $connection_production->prepare($sql_search_team_franchise);
                    $prepared_search_team_franchise->bind_param("i", $post_team_id_set);
                    $result_search_team_franchise = $prepared_search_team_franchise->execute();
                    $result_search_team_franchise = $prepared_search_team_franchise->get_result();
                    $prepared_search_team_franchise->close();

                    if ($result_search_team_franchise->num_rows == 0) {
                         echo "<h3 style='color:red;'>franchise association to team not found</h3>";
                    } else if ($result_search_team_franchise->num_rows == 1) {
                        $row_search_team_franchise = $result_search_team_franchise->fetch_assoc();

                        $sql_add_profile_franchise_sm = "INSERT INTO profile_franchise_sm (franchiseId, profileId) VALUES (?, ?)";

                        $prepared_add_profile_franchise_sm = $connection_production->prepare($sql_add_profile_franchise_sm);
                        $prepared_add_profile_franchise_sm->bind_param("ii", $row_search_team_franchise['franchise_id'], $post_profile_id);
                        $result_add_profile_franchise_sm=$prepared_add_profile_franchise_sm->execute();
                        $last_id = $connection_production->insert_id;
                        $prepared_add_profile_franchise_sm->close();

                        $update_string = 'team: '.$post_team_id_set.' added profile: '.$post_profile_id.' to team, and added profile franchise/team association id: '.$last_id;
                        insertChange($_SESSION['account_id'], 'team', 'add profile - team', $post_team_id_set, $update_string);
                        header("location: categories.php?source=team_update&team_id=".$post_team_id_set."#add_profile");

                    } else if ($result_search_team_franchise->num_rows > 1) {
                        echo "<h3 style='color:red;'>too many associations with franchise id: ";
                        while ($row_search_team = $result_search_team_franchise->fetch_assoc()) {
                            echo $row_search_team_franchise['franchise_id']."<br>";
                        }
                    }
                } else if ($result_search_team_found->num_rows > 0) {
                    echo "<h3 style='color:red;'>team id: ".$post_profile_id." already associated with profile</h3>";
                }
            } else {
                echo "<h3 style='color:red;'>please select a profile</h3>";
            }
        }
    }

//======================================================================
// DELETE ATHLETE PROFILE FROM TEAM
//======================================================================

    function deleteAthleteFromTeamProfile() {
        global $connection_production;
        if (isset($_POST['delete_profile_from_team_association'])) {
            if (!!$_POST['query_profile_id']) {
                $profile_franchise_sm_id = $_POST['profile_franchise_sm_id'];
                $post_team_id_set = $_POST['team_id_set'];
                $delete_profile_id = $_POST['query_profile_id'];

                $sql_team_video = "SELECT DISTINCT video.id AS video_id, profile_franchise_sm_video_sm.id AS profile_franchise_sm_video_sm_id FROM video
                	LEFT JOIN profile_franchise_sm_video_sm ON video.id=profile_franchise_sm_video_sm.videoId
                	LEFT JOIN profile_franchise_sm ON profile_franchise_sm_video_sm.profileFranchiseSmId=profile_franchise_sm.id
                	LEFT JOIN profile ON profile_franchise_sm.profileId=profile.id
                	LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
                	LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
                	LEFT JOIN team ON team.id=franchise_team_sm.teamId
                	WHERE profile.id=? AND team.id=?";
                $stmt = $connection_production->prepare($sql_team_video);
                $stmt->bind_param("ii", $delete_profile_id, $post_team_id_set);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row_delete_video = $result->fetch_assoc()) {
                        $video_id = $row_delete_video["video_id"];
                        $profile_franchise_sm_video_sm_id = $row_delete_video["profile_franchise_sm_video_sm_id"];

                        if (!!$profile_franchise_sm_video_sm_id && $profile_franchise_sm_video_sm_id != NULL) {
                            $stmt2 = $connection_production->prepare("delete from `profile_franchise_sm_video_sm` WHERE id = ?");
                            $stmt2->bind_param("i", $profile_franchise_sm_video_sm_id);
                            $stmt2->execute();
                            $stmt2->close();
                        }
                        if (!!$video_id && $video_id != NULL) {
                            $stmt2 = $connection_production->prepare("delete from `video` WHERE id = ?");
                            $stmt2->bind_param("i", $video_id);
                            $stmt2->execute();
                            $stmt2->close();
                        }
                        if ((!!$profile_franchise_sm_video_sm_id && $profile_franchise_sm_video_sm_id != NULL)  ||  (!!$video_id && $video_id != NULL)) {

                            $update_string = 'team: '.$_POST['team_id_set'].' deleted profile team - video id: '.$video_id.', from profile id: '.$delete_profile_id.', deleted profile franchse video join table id: '.$profile_franchise_sm_video_sm_id;
                            insertChange($_SESSION['account_id'], 'team', 'delete profile team - video', $post_team_id_set, $update_string);
                        }
                    }
                }
                $sql_team_period = "SELECT DISTINCT profile_franchise_sm.id AS profile_franchise_sm_id,
					profile_franchise_sm_period_sm.id AS profile_franchise_sm_period_sm_id, period.id AS period_id
                	FROM profile LEFT JOIN profile_franchise_sm ON profile_franchise_sm.profileId=profile.id
                	LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
                	LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
                	LEFT JOIN team ON team.id=franchise_team_sm.teamId
                	LEFT JOIN profile_franchise_sm_period_sm ON profile_franchise_sm_period_sm.profileFranchiseSmId=profile_franchise_sm.id
                	LEFT JOIN period ON period.id=profile_franchise_sm_period_sm.periodId
                	WHERE profile.id=? AND team.id=?";
                $stmt = $connection_production->prepare($sql_team_period);
                $stmt->bind_param("ii", $delete_profile_id, $post_team_id_set);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row_delete_team_period = $result->fetch_assoc()) {

                        $delete_profile_franchise_sm_period_sm_id = $row_delete_team_period["profile_franchise_sm_period_sm_id"];
                        $delete_period_id = $row_delete_team_period["period_id"];

                        if (!!$delete_profile_franchise_sm_period_sm_id && $delete_profile_franchise_sm_period_sm_id != NULL) {
                            $stmt_delete_p_f_sm_a_r_sm = "DELETE FROM profile_franchise_sm_period_sm WHERE id=?";
                            $stmt_delete_p_f_sm_a_r_sm = $connection_production->prepare($stmt_delete_p_f_sm_a_r_sm);
                            $stmt_delete_p_f_sm_a_r_sm->bind_param("i", $delete_profile_franchise_sm_period_sm_id);
                            $stmt_delete_p_f_sm_a_r_sm->execute();
                            $stmt_delete_p_f_sm_a_r_sm->close();
                        }
                        if (!!$delete_period_id && $delete_period_id != NULL) {
                            $stmt_delete_period = "DELETE FROM period WHERE id=?";
                            $stmt_delete_period = $connection_production->prepare($stmt_delete_period);
                            $stmt_delete_period->bind_param("i", $delete_period_id);
                            $stmt_delete_period->execute();
                            $stmt_delete_period->close();
                        }
                        if ((!!$delete_profile_franchise_sm_period_sm_id && $delete_profile_franchise_sm_period_sm_id != NULL)
							|| (!!$delete_period_id && $delete_period_id != NULL)) {

                            $update_string = 'team: '.$_POST['team_id_set'].' deleted profile team - period id: '.$delete_period_id.', from profile id: '.$delete_profile_id.', deleted profile franchse activity period join table id: '.$delete_profile_franchise_sm_period_sm_id;
                            insertChange($_SESSION['account_id'], 'team', 'delete profile team - period', $post_team_id_set, $update_string);
                        }
                    }
                }
                $sql_team_role = "SELECT DISTINCT activity_role.role AS role_name, profile_franchise_sm_activity_role_sm.id AS p_f_sm_a_r_sm_id
                    FROM activity_role
                    LEFT JOIN profile_franchise_sm_activity_role_sm ON profile_franchise_sm_activity_role_sm.activityRoleId=activity_role.id
                    LEFT JOIN profile_franchise_sm ON profile_franchise_sm.id=profile_franchise_sm_activity_role_sm.profileFranchiseSmId
                    LEFT JOIN profile ON profile.id=profile_franchise_sm.profileId
                    LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
                    LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
                    LEFT JOIN team ON team.id=franchise_team_sm.teamId
                    WHERE profile.id=? AND team.id=?";
                $stmt = $connection_production->prepare($sql_team_role);
                $stmt->bind_param("ii", $delete_profile_id, $post_team_id_set);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row_delete_team_role = $result->fetch_assoc()) {
                        $p_f_sm_a_r_sm_id = $row_delete_team_role["p_f_sm_a_r_sm_id"];
                        $role_name = $row_delete_team_role["role_name"];

                        if (!!$p_f_sm_a_r_sm_id && $p_f_sm_a_r_sm_id != NULL) {
                            $sql_delete_p_f_sm_a_r_sm = "DELETE FROM profile_franchise_sm_activity_role_sm WHERE id=?";
                            $stmt_delete_p_f_sm_a_r_sm = $connection_production->prepare($sql_delete_p_f_sm_a_r_sm);
                            $stmt_delete_p_f_sm_a_r_sm->bind_param("i", $p_f_sm_a_r_sm_id);
                            $stmt_delete_p_f_sm_a_r_sm->execute();
                            $stmt_delete_p_f_sm_a_r_sm->close();

                            $update_string = 'team: '.$_POST['team_id_set'].' deleted profile team - role : '. $role_name.', from profile id: '.$delete_profile_id.', deleted profile franchse activity role join table id: '.$p_f_sm_a_r_sm_id;
                            insertChange($_SESSION['account_id'], 'team', 'delete profile team - role', $post_team_id_set, $update_string);
                        }
                    }
                }
                $sql_delete_p_f_sm = "DELETE FROM profile_franchise_sm WHERE id=?";
                $stmt_delete_p_f_sm = $connection_production->prepare($sql_delete_p_f_sm);
                $stmt_delete_p_f_sm->bind_param("i", $profile_franchise_sm_id);
                $stmt_delete_p_f_sm->execute();
                $stmt_delete_p_f_sm->close();

                $update_string = 'team: '.$_POST['team_id_set'].' deleted profile team association : '.$profile_franchise_sm_id.', from profile id: '.$delete_profile_id;
                insertChange($_SESSION['account_id'], 'team', 'delete team association', $post_team_id_set, $update_string);
                header("location: categories.php?source=team_update&team_id=".$post_team_id_set."#profiles");
            } else {
                echo "<h3 style='color:red;'>something went wrong with deleting activity</h3>";
            }
        }
    }

	function addVideoToTeam() {
		global $connection_production;
		if (isset($_POST['add_team_video'])) {
			$team_id = $_POST['team_id'];
			$video_id = $_POST['video_id'];
			$profile_id = $_POST['profile_id'];

			$search_video_sql = "SELECT * FROM video WHERE id=?";
			$search_video_stmt = $connection_production->prepare($search_video_sql);
			$search_video_stmt->bind_param("i", $video_id);
			$search_video_stmt->execute();
			$row_search_video = $search_video_stmt->get_result()->fetch_assoc();
			$search_video_stmt->close();

			$search_profile_sql = "SELECT * FROM profile WHERE id=?";
			$search_profile_stmt = $connection_production->prepare($search_profile_sql);
			$search_profile_stmt->bind_param("i", $profile_id);
			$search_profile_stmt->execute();
			$row_search_profile = $search_profile_stmt->get_result()->fetch_assoc();
			$search_profile_stmt->close();

			if (!!$row_search_video['id']) {
				if (!$row_search_video['profileAdminId']) {
					if (!!$row_search_profile['id']) {

						$search_p_f_sm_sql = "SELECT profile_franchise_sm.id AS p_f_sm_id FROM profile_franchise_sm
							WHERE profileId=? AND franchiseId=?";
						$search_p_f_sm_stmt = $connection_production->prepare($search_p_f_sm_sql);
						$search_p_f_sm_stmt->bind_param("ii", $profile_id, $team_id);
						$search_p_f_sm_stmt->execute();
						$row_search_p_f_sm = $search_p_f_sm_stmt->get_result()->fetch_assoc();
						$search_p_f_sm_stmt->close();

						if (!$row_search_p_f_sm['p_f_sm_id']) {
							$profile_team_insert_sql = "INSERT INTO profile_franchise_sm (profileId, franchiseId) VALUES (?, ?)";
							$profile_team_insert_stmt = $connection_production->prepare($profile_team_insert_sql);
							$profile_team_insert_stmt->bind_param("ii", $profile_id, $team_id);
							if ($profile_team_insert_stmt->execute()) {
								$p_f_sm_id = $connection_production->insert_id;
							}
							$profile_team_insert_stmt->close();
						} else {
							$p_f_sm_id = $row_search_p_f_sm['p_f_sm_id'];
						}

						if (isset($p_f_sm_id)) {

							$p_f_sm_v_search_sql = "SELECT * FROM profile_franchise_sm_video_sm
								WHERE profileFranchiseSmId=? AND videoId=?";
							$p_f_sm_v_search_stmt = $connection_production->prepare($p_f_sm_v_search_sql);
							$p_f_sm_v_search_stmt->bind_param("ii", $p_f_sm_id, $video_id);
							$p_f_sm_v_search_stmt->execute();
							$row_p_f_sm_v_search = $p_f_sm_v_search_stmt->get_result()->fetch_assoc();
							$p_f_sm_v_search_stmt->close();

							if (!$row_p_f_sm_v_search['id']) {

								$p_f_sm_v_insert_sql = "INSERT INTO profile_franchise_sm_video_sm (profileFranchiseSmId, videoId) VALUES (?, ?)";
								$p_f_sm_v_insert_stmt = $connection_production->prepare($p_f_sm_v_insert_sql);
								$p_f_sm_v_insert_stmt->bind_param("ii", $p_f_sm_id, $video_id);

								$set_main_profile_sql = "UPDATE video SET profileAdminId=? WHERE video.id=?";
								$set_main_profile_stmt = $connection_production->prepare($set_main_profile_sql);
								$set_main_profile_stmt->bind_param("ii", $profile_id, $video_id);

								if (($set_main_profile_stmt->execute()) && ($p_f_sm_v_insert_stmt->execute())) {
									$new_p_f_sm_v_id = $connection_production->insert_id;
									$update_string = "team id: ".$profile_id." :: assign team video id: ".$video_id." through profile id: ".$profile_id.", p_f_sm_id ID: ".$p_f_sm_id.", added p_f_sm_v_sm ID: ".$new_p_f_sm_v_id;
									insertChange($_SESSION['account_id'], 'team', 'assign team video', $team_id, $update_string);
									header("location: categories.php?source=team_update&team_id=".$team_id."#add_video");
								} else {
									echo "<h3 style='color:red'>Something went wrong</h3>";
								}
								$p_f_sm_v_insert_stmt->close();
								$set_main_profile_stmt->close();
							} else {
								echo "<h3 style='color:red'>Video ID: ".$video_id." - already has p_f_sm_v_sm ID: ".$row_p_f_sm_v_search['id']."</h3>";
							}
						} else {
							echo "<h3 style='color:red'>Something went wrong</h3>";
						}
					} else {
						echo "<h3 style='color:red'>Profile ID: ".$profile_id." - Not found</h3>";
					}
				} else {
					echo "<h3 style='color:red'>Video ID: ".$video_id." - already has main profile ID: ".$row_search_video['profileAdminId']."</h3>";
				}
			} else {
				echo "<h3 style='color:red'>Video ID: ".$video_id." - Not found</h3>";
			}
		}
	}


	function deleteTeamVideoAssignedToTeam() {
		global $connection_production;
		if (isset($_POST['delete_team_video'])) {
			if (!empty($_POST['delete_video_id'])) {
				$p_f_sm_v_sm_id = $_POST['p_f_sm_v_sm_id'];
				$team_id = $_POST['team_id'];
				$delete_video_id = $_POST['delete_video_id'];
				$profile_id = $_POST['profile_id'];

				$stmt1 = $connection_production->prepare("delete from `profile_franchise_sm_video_sm` WHERE id = ?");
				$stmt1->bind_param("i", $p_f_sm_v_sm_id);

				$stmt2 = $connection_production->prepare("delete from `video` WHERE id = ?");
				$stmt2->bind_param("i", $delete_video_id);

				$update_string = 'deleted team-video id: '.$delete_video_id." - profile id: ".$profile_id.", team id: ".$team_id." :: ";
				$update_string .= "deleted p_f_sm_v_sm id: ".$p_f_sm_v_sm_id.", ";

				if ((deleteEventsHelper($delete_video_id, $update_string) != -1)
						&& ($stmt1->execute()) && ($stmt2->execute())
						&& (unassignAllProfileLinks($delete_video_id, $update_string) != -1)) {

					insertChange($_SESSION['account_id'], 'team', 'delete video (team)', $team_id, $update_string);
					header("location: categories.php?source=team_update&team_id=".$team_id."#videos");
				} else {
					echo "<h3 style='color:red;'>Something went wrong</h3>";
				}
				$stmt1->close();
				$stmt2->close();
			} else {
				echo "<h3 style='color:red;'>please select a video</h3>";
			}
		}
	}
?>
