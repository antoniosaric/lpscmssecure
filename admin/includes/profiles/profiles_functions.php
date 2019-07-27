<?php

//======================================================================
// FIND ALL PROFILES
//======================================================================

	function findAllProfiles($filter, $pageinate, $words) {
		global $connection_production;

        $query = "SELECT DISTINCT profile.id AS profileId, participant.firstName, participant.middle,
			participant.lastName, participant.birthdate AS birthdate, participant.gender AS gender, profile.summary AS summary,
			profile.acclaim AS acclaim, image.imageName AS imageName, participant_suffix.suffix AS suffix,
			participant_suffix.id AS suffixId, alternate_name.alternateName AS nickname, profile.mainProfileType AS mainProfileType,
			profile.profileTypeId AS profileTypeId, profile.status AS status, specialty.specialty AS specialty FROM participant
			LEFT JOIN profile ON profile.participantId = participant.id
			LEFT JOIN profile_image_sm ON profile.id=profile_image_sm.profileId
			LEFT JOIN image ON profile_image_sm.imageId=image.id AND image.imageTypeId=1
			LEFT JOIN participant_suffix ON participant.participantSuffixId=participant_suffix.id
			LEFT JOIN profile_alternate_name_sm ON profile_alternate_name_sm.profileId=profile.id
			LEFT JOIN alternate_name ON profile_alternate_name_sm.alternateNameId = alternate_name.id
			LEFT JOIN profile_specialty_sm ON profile_specialty_sm.profileId = profile.id
			LEFT JOIN specialty ON specialty.id = profile_specialty_sm.specialtyId "
			.$filter." LIMIT ?, 20";
		$stmt = $connection_production->prepare($query);

		$bind_parameters = array();
		$bind_parameters[0] = "";
		if (!empty($words)) {
			foreach ($words as $key) {
				$bind_parameters[0] = $bind_parameters[0]."sssi";
				$format_param = '%'.$key.'%';
				array_push($bind_parameters, $format_param);
				array_push($bind_parameters, $format_param);
				array_push($bind_parameters, $format_param);
				array_push($bind_parameters, $key);
			}
		}
		$bind_parameters[0] = $bind_parameters[0]."i";
		array_push($bind_parameters, $pageinate);
		call_user_func_array(array($stmt, "bind_param"), refValues($bind_parameters));
		$stmt->execute();
        $select_all_profiles_query = $stmt->get_result();
		$stmt->close();

        while ($row = mysqli_fetch_assoc($select_all_profiles_query)) {

            $query_location = "SELECT DISTINCT location.city AS city, location.country AS country FROM profile
				LEFT JOIN profile_location_sm ON profile_location_sm.profileId=profile.id
				LEFT JOIN location ON profile_location_sm.locationId=location.id
				WHERE profile.id=".$row['profileId'];
            $select_all_profiles_location_query = mysqli_query($connection_production, $query_location);
            $location_set = 'no';
            while ($row_location = mysqli_fetch_assoc($select_all_profiles_location_query)) {
                $location_set = !!$row_location['city'] && !!$row_location['country'] ? 'Yes' : 'No';
            }

            echo "<tr>";
            echo "<td>".$row['profileId']."</td>";
            echo "<td>".$row['firstName']."</td>";
            echo "<td>".$row['middle']."</td>";
            echo "<td>".$row['lastName']."</td>";
			if ($row['profileTypeId'] == 2) {
				echo "<td>".$row['specialty']."</td>";
			} else {
				echo "<td>".$row['nickname']."</td>";
			}
            echo "<td>".$row['suffix']."</td>";
            $summary_set = !!$row['summary'] ? 'Yes' : 'No';
            echo "<td>".$summary_set."</td>";
            $acclaim_set = !!$row['acclaim'] ? 'Yes' : 'No';
            echo "<td>".$acclaim_set."</td>";
            echo "<td>".$location_set."</td>";
            $image_set = !!$row['imageName'] ? 'Yes' : 'No';
            echo "<td>".$image_set."</td>";
            if ($row['profileTypeId'] == 0) {
                $pofileType = 'athlete';
            } else if ($row['profileTypeId'] == 1) {
                $pofileType = 'athlete';
            } else if ($row['profileTypeId'] == 2) {
                $pofileType = 'coach';
            }
            echo "<td>".$pofileType."</td>";
            echo "<td><a class='btn btn-info' href='categories.php?source=profile_update_athlete&profile_id=".$row['profileId']."'>Edit</a></td>";
            echo "<form method='post'><input type='hidden' name='profile_id' value=".$row['profileId'].">";
        ?>
            <td><input onClick="return confirm('Are you sure you want to do that?');"  class='btn btn-danger' type='submit' name='delete_profile' value='DELETE'></td>
        <?php
            echo "</form>";
            echo "</tr>";
        }
	}

//======================================================================
// DELETE PROFILE
//======================================================================

	function deleteProfile() {
		global $connection_production;
        if (isset($_POST['delete_profile'])) {

        	echo 'working';
	        // $update_string = 'profile: '.$_GET['profile'].', description: deleted'.$_GET['profile'];

	        // insertChange($_SESSION['account_id'], 'profile', 'delete', $profile_id, $update_string);
            // $query = "DELETE FROM profile WHERE id=".$_GET['delete'];
            // $delete_query = mysqli_query($connection, $query);
            // header("location: categories.php?source=profile");
        }
	}

//======================================================================
// UPDATE ATHLETE INFO
//======================================================================

    function updateAthleteInfo() {
        global $connection_production;
        if (isset($_POST['update_athlete_info'])) {

            $post_profile_id_set = $_POST['profile_id_set'];
            $post_first_name = $_POST['firstName'];
            $post_last_name = $_POST['lastName'];
            $post_middle_name = $_POST['middle'];
            $post_nickname = isset($_POST['nickname']) ? $_POST['nickname'] : "";
			$post_specialty = isset($_POST['specialty']) ? $_POST['specialty'] : "";
            $post_suffix = $_POST['suffix'];
            $post_status = $_POST['status'];
            $post_summary = $_POST['summary'];
            $post_acclaim = $_POST['acclaim'];
            $post_birthdate = $_POST['birthdate'];
            $post_gender = $_POST['gender'];
            $post_profileTypeId = $_POST['profileTypeId'];

            if (!!$post_first_name) {

                $alreadyRegisteredSql = "SELECT DISTINCT *, profile.id AS profile_id, participant.id AS participant_id,
    				participant.firstName AS first_name, participant.middle AS middle_name, participant.lastName AS last_name,
    				participant.birthdate AS birthdate, participant.gender AS gender, profile.summary AS summary,
    				profile.acclaim AS acclaim, participant_suffix.suffix AS suffix, participant_suffix.id as suffixId,
    				alternate_name.alternateName AS nickname, alternate_name.id AS alternate_name_id, specialty.id AS specialty_id,
    				profile.mainProfileType AS mainProfileType, profile.profileTypeId AS profileTypeId, profile.status AS status,
    				specialty.specialty AS specialty FROM participant
    				LEFT JOIN profile ON profile.participantId = participant.id
    				LEFT JOIN participant_suffix ON participant.participantSuffixId=participant_suffix.id
    				LEFT JOIN profile_alternate_name_sm ON profile_alternate_name_sm.profileId=profile.id
    				LEFT JOIN alternate_name ON profile_alternate_name_sm.alternateNameId = alternate_name.id
    				LEFT JOIN profile_specialty_sm ON profile_specialty_sm.profileId = profile.id
    				LEFT JOIN specialty ON specialty.id = profile_specialty_sm.specialtyId
    				WHERE profile.id=?";

                $stmt = $connection_production->prepare($alreadyRegisteredSql);
                $stmt->bind_param("i", $_POST['profile_id_set']);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();

                if ($result->num_rows > 0) {
                    $alternate_name_id = $row["alternate_name_id"];
    				$specialty_id = $row['specialty_id'];
                    $participant_id = $row["participant_id"];

                    $sqlparticipant = "UPDATE participant SET firstName=?, middle=?, lastName=?,
    					participantSuffixId=?, birthdate=?, gender=? WHERE id=?";

                    $stmtparticipant = $connection_production->prepare($sqlparticipant);
                    $stmtparticipant->bind_param("ssssssi", $post_first_name, $post_middle_name, $post_last_name, $post_suffix, $post_birthdate, $post_gender, $participant_id);
                    $stmtparticipantresult = $stmtparticipant->execute();
                    $stmtparticipant->close();

                    $sqlprofile = "UPDATE profile SET summary=?, acclaim=?, profileTypeId=?, status=? WHERE id=?";

                    $stmtprofile = $connection_production->prepare($sqlprofile);
                    $stmtprofile->bind_param("ssisi", $post_summary, $post_acclaim, $post_profileTypeId, $post_status, $post_profile_id_set);
                    $stmtprofile->execute();


                    if ($post_status == 'disabled' || $post_status == 'incomplete') {
                        $set_video_status_to = 'incomplete';
                    } else if ($post_status == 'complete') {
                        $set_video_status_to = 'complete';
                    }

                //***************      SQL TEAM VIDEOS      *********************

                    $sqlvideosteam = "SELECT DISTINCT video.id AS video_id, team.id AS team_id, team.name AS team_name,
    					video.reference AS reference, video.title AS title, video.summary AS video_summary, profile.id AS video_profile_id,
    					video.videoStatus AS video_status, video.thumbString AS thumb_string, video.videoSourceId AS video_Source_id,
    					video_source.source AS video_source, profile_franchise_sm_video_sm.id AS p_f_sm_v_sm_id FROM video
                        LEFT JOIN video_source ON video_source.id = video.videoSourceId
                        LEFT JOIN profile_franchise_sm_video_sm ON video.id=profile_franchise_sm_video_sm.videoId
                        LEFT JOIN profile_franchise_sm ON profile_franchise_sm_video_sm.profileFranchiseSmId=profile_franchise_sm.id
                        LEFT JOIN profile ON profile_franchise_sm.profileId=profile.id
                        LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
                        LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
                        LEFT JOIN team ON team.id=franchise_team_sm.teamId
    					WHERE profile.id=?";
                    $stmt = $connection_production->prepare($sqlvideosteam);
                    $stmt->bind_param("i", $post_profile_id_set);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
            			while ($row = $result->fetch_assoc()) {
                    		if (!!$set_video_status_to
    								&& ($row['video_status'] != 'processing')
    								&& ($row['video_status'] != 'deadlink')) {

                    			$sqlvideosteam = "UPDATE video set videoStatus=? WHERE video.id=?";
                    			$stmt = $connection_production->prepare($sqlvideosteam);
                    			$stmt->bind_param("si", $set_video_status_to, $row["video_id"]);
                    			$stmt->execute();
                			}
                		}
                    }

                //********************       SQL INDIVIDAL SPORT VIDEOS     **************************

                    $sqlvideossport = "SELECT DISTINCT video.id AS video_id, video.reference AS reference, video.title AS title,
    					profile_activity_sm.activityId AS activity_id, activity.activity AS activity, video.summary AS video_summary,
    					profile.id AS video_profile_id, video.videoStatus AS video_status, video.thumbString AS thumb_string,
    					video.videoSourceId AS video_source_id, video_source.source AS video_source, profile_activity_sm_video_sm.id AS p_a_sm_v_sm_id
    					FROM video LEFT JOIN video_source ON video_source.id = video.videoSourceId
    					LEFT JOIN profile_activity_sm_video_sm ON video.id=profile_activity_sm_video_sm.videoId
    					LEFT JOIN profile_activity_sm ON profile_activity_sm_video_sm.profileActivitySmId=profile_activity_sm.id
    					LEFT JOIN profile ON profile_activity_sm.profileId = profile.id
    					LEFT JOIN activity ON profile_activity_sm.activityId = activity.id
    					WHERE profile.id=?";
                    $stmt2 = $connection_production->prepare($sqlvideossport);
                    $stmt2->bind_param("i", $post_profile_id_set);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    if ($result2->num_rows > 0) {
            			while ($row2 = $result2->fetch_assoc()) {
                			if (!!$set_video_status_to
    							&& ($row2['video_status'] != 'processing')
    							&& ($row2['video_status'] != 'deadlink')) {

                    			$sqlvideosteam = "UPDATE video set videoStatus=? WHERE video.id=?";
                    			$stmt = $connection_production->prepare($sqlvideosteam);
                    			$stmt->bind_param("si", $set_video_status_to, $row2["video_id"]);
                    			$stmt->execute();
                			}
                		}
                    }

                //***********       SQL INDIVIDAL VIDEO NO SPORT/TEAM ASSOCIATION     *******************

                    $sqlvideosparticipant = "SELECT DISTINCT video.id AS video_id, video.reference AS reference, video.title AS title,
    					video.summary AS video_summary, profile.id AS video_profile_id, video.videoStatus AS video_status,
    					video.thumbString AS thumb_string, video.videoSourceId AS video_source_id, video_source.source AS video_source,
    					profile_video_sm.id AS profile_video_sm_id FROM video
    					LEFT JOIN video_source ON video_source.id = video.videoSourceId
    					LEFT JOIN profile_video_sm ON video.id=profile_video_sm.videoId
    					LEFT JOIN profile ON profile_video_sm.profileId=profile.id
    					WHERE profile.id=?";

                    $stmt3 = $connection_production->prepare($sqlvideosparticipant);
                    $stmt3->bind_param("i", $post_profile_id_set);
                    $stmt3->execute();
                    $result3 = $stmt3->get_result();
                    if ($result3->num_rows > 0) {
                		while ($row3 = $result3->fetch_assoc()) {
                			if (!!$set_video_status_to
    							&& ($row3['video_status'] != 'processing')
    							&& ($row3['video_status'] != 'deadlink')) {

                    			$sqlvideosteam = "UPDATE video set videoStatus=? WHERE video.id=?";
                    			$stmt = $connection_production->prepare($sqlvideosteam);
                				$stmt->bind_param("si", $set_video_status_to, $row3["video_id"]);
                    			$stmt->execute();
                			}
        				}
                    }
    				if ($post_profileTypeId == 2) {
    					if (!!$specialty_id) {
    						$sqlspecialty = "UPDATE specialty SET specialty=? WHERE id=?";
    						$stmtspecialty = $connection_production->prepare($sqlspecialty);
    						$stmtspecialty->bind_param("si", $post_specialty, $specialty_id);
    						$stmtspecialty->execute();
    						$stmtspecialty->close();
    					} else {
    						$sqlspecialty = "INSERT INTO specialty (specialty) VALUES (?)";
    						$stmtspecialty = $connection_production->prepare($sqlspecialty);
    						$stmtspecialty->bind_param("s", $post_specialty);
    						$stmtspecialty->execute();
    						$stmtspecialty->close();
    						$last_id = $connection_production->insert_id;

    						createUUID("specialty", $last_id);

    						$sqlspecialty = "INSERT INTO profile_specialty_sm (specialtyId, profileId) VALUES (?, ?)";
    						$stmtspecialty = $connection_production->prepare($sqlspecialty);
    						$stmtspecialty->bind_param("ii", $last_id, $post_profile_id_set);
    						$stmtspecialty->execute();
    						$stmtspecialty->close();
    					}
    				} else {
    					if (!!$alternate_name_id) {
    						$sqlnickname = "UPDATE alternate_name SET alternateName=? WHERE id=?";

    						$stmtnickname = $connection_production->prepare($sqlnickname);
    						$stmtnickname->bind_param("si", $post_nickname, $alternate_name_id);
    						$stmtnickname->execute();
    						$stmtnickname->close();
    					} else {
    						$one = 1;
    						$sqlnickname = "INSERT INTO alternate_name (alternateName, alternateNameTypeId) VALUES (?,?)";

    						$stmtnickname = $connection_production->prepare($sqlnickname);
    						$stmtnickname->bind_param("si", $post_nickname, $one);
    						$stmtnickname->execute();
    						$stmtnickname->close();
    						$last_id = $connection_production->insert_id;

    						createUUID('alternate_name', $last_id);

    						$sqlnickname = "INSERT INTO profile_alternate_name_sm(alternateNameId, profileId) VALUES (?,?)";

    						$stmtnickname = $connection_production->prepare($sqlnickname);
    						$stmtnickname->bind_param("ii", $last_id, $post_profile_id_set);
    						$stmtnickname->execute();
    						$stmtnickname->close();
    					}
    				}

                    $update_string = 'profile: '.$_POST['profile_id_set'].' ';
                    if ($row["first_name"] != $post_first_name || $row["middle_name"] != $post_middle_name || $row["last_name"] != $post_last_name || $row["suffix"] != $post_suffix) {
                          $update_string .= ", first name: ".$post_first_name.", middle: ".$post_middle_name.", last name: ".$post_last_name.", ".$post_suffix." ";
                    }
                    if ($row["birthdate"] != $post_birthdate || $row["gender"] != $post_gender || $row["nickname"] != $post_nickname|| $row["profileTypeId"] != $post_profileTypeId) {
                        $update_string .= ", birthdate: ".$post_birthdate.", gender: ".$post_gender.", nickname: ".$post_nickname.", profile type: ".$post_profileTypeId." ";
                    }
                    if ($row["summary"] != $post_summary) {
                        $update_string .= ", changed summary: ".$post_summary." ";
                    }
    				if ($row['specialty'] != $post_specialty) {
    					$update_string .= ", changed specialty: ".$post_specialty." ";
    				}
                    if ($row["acclaim"] != $post_acclaim) {
                        $update_string .= ", changed acclaim: ".$post_acclaim." ";
                    }
                    insertChange($_SESSION['account_id'], 'profile', 'update info', $post_profile_id_set, $update_string);
                }
                header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']);
            }else{
                echo "<h3 style='color:red;'>A first name must be entered</h3>";
            }
        }
    }

//======================================================================
// ADD ATHLETE INFO
//======================================================================

    function addAthleteInfo() {
        global $connection_production;
        if (isset($_POST['add_athlete_info'])) {

            $post_first_name = $_POST['firstName'];
            $post_last_name = $_POST['lastName'];
            $post_middle_name = $_POST['middle'];
            $post_nickname = isset($_POST['nickname']) ? $_POST['nickname'] : "";
			$post_specialty = isset($_POST['specialty']) ? $_POST['specialty'] : "";
            $post_suffix = $_POST['suffix'];
            $post_status = $_POST['status'];
            $post_summary = $_POST['summary'];
            $post_acclaim = $_POST['acclaim'];
            $post_birthdate = $_POST['birthdate'];
            $post_gender = $_POST['gender'];
            $post_profileTypeId = $_POST['profileTypeId'];

            if (!!$post_first_name) {
                $zero = 0;
                $sql_participant = "INSERT INTO participant (firstName, middle, lastName,
					participantSuffixId, birthdate, gender) VALUES (?,?,?,?,?,?)";

                $stmt_participant = $connection_production->prepare($sql_participant);
                $stmt_participant->bind_param("sssiss", $post_first_name, $post_middle_name, $post_last_name, $post_suffix, $post_birthdate, $post_gender);
                $stmt_participant_result = $stmt_participant->execute();
                $participant_last_id = $connection_production->insert_id;
                $stmt_participant->close();
                createUUID('participant', $participant_last_id);

                $sql_profile = "INSERT INTO profile (participantId, profileTypeId, summary,
					acclaim, status, mainProfileType) VALUES (?,?,?,?,?,?)";

                $stmt_profile = $connection_production->prepare($sql_profile);
                $stmt_profile->bind_param("iisssi", $participant_last_id, $post_profileTypeId, $post_summary, $post_acclaim, $post_status, $zero);
                $stmt_profile->execute();
                $profile_last_id = $connection_production->insert_id;
                $stmt_profile->close();
                createUUID('profile', $profile_last_id);

                if (!!$post_nickname && $post_profileTypeId == 1) {
                    $one = 1;
                    $sql_nickname = "INSERT INTO alternate_name(alternateName, alternateNameTypeId) VALUES (?,?)";

                    $stmt_nickname = $connection_production->prepare($sql_nickname);
                    $stmt_nickname->bind_param("si", $post_nickname, $one);
                    $stmt_nickname->execute();
                    $stmt_nickname->close();
                    $alternate_name_last_id = $connection_production->insert_id;

                    createUUID('alternate_name', $alternate_name_last_id);

                    $sql_nickname_join = "INSERT INTO profile_alternate_name_sm(alternateNameId, profileId) VALUES (?,?)";

                    $stmt_nickname_join = $connection_production->prepare($sql_nickname_join);
                    $stmt_nickname_join->bind_param("ii", $alternate_name_last_id, $profile_last_id);
                    $stmt_nickname_join->execute();
                    $stmt_nickname_join->close();
                }
				else if (!!$post_specialty && $post_profileTypeId == 2) {
					$sql_specialty = "INSERT INTO specialty (specialty) VALUES (?)";
					$stmt_specialty = $connection_production->prepare($sql_specialty);
					$stmt_specialty->bind_param("s", $post_specialty);

					if ($stmt_specialty->execute()) {
						$specialty_id = $connection_production->insert_id;
						createUUID('specialty', $specialty_id);

						$sql_spec_join = "INSERT INTO profile_specialty_sm (specialtyId, profileId) VALUES (?, ?)";
						$stmt_spec_join = $connection_production->prepare($sql_spec_join);
						$stmt_spec_join->bind_param("ii", $specialty_id, $profile_last_id);

						if (!$stmt_spec_join->execute()) {
							$spec_error = true;
						}
						$stmt_spec_join->close();
					} else {
						$spec_error = true;
					}
					$stmt_specialty->close();
				}

				if (!empty($spec_error)) {
					echo "<h3 style='color:red'>Something went wrong inserting specialty</h3>";
				} else {
					$update_string = 'profile: '.$profile_last_id.' ';
	                if (!!$post_first_name || !!$post_middle_name || !!$post_last_name || !!$post_suffix) {
	                      $update_string .= ", first name: ".$post_first_name.", middle: ".$post_middle_name.", last name: ".$post_last_name.", suffix: ".$post_suffix." ";
	                }
	                if (!!$post_birthdate || !!$post_gender || !!$post_nickname|| !!$post_profileTypeId) {
	                    $update_string .= ", birthdate: ".$post_birthdate.", gender: ".$post_gender.", nickname: ".$post_nickname.", profile type: ".$post_profileTypeId." ";
	                }
	                if (!!$post_summary) {
	                    $update_string .= ", changed summary: ".$post_summary." ";
	                }
					if (!!$post_specialty) {
						$update_string .= ", specialty: ".$post_specialty." ";
					}
	                if (!!$post_acclaim) {
	                    $update_string .= ", changed acclaim: ".$post_acclaim." ";
	                }
	                insertChange($_SESSION['account_id'], 'profile', 'add profile', $profile_last_id, $update_string);
	                header("location: categories.php?source=profile_update_athlete&profile_id=".$profile_last_id);
				}
            } else {
                echo "<h3 style='color:red;'>A first name must be entered</h3>";
            }
        }
    }

//======================================================================
// ADD ATHLETE ACTIVITY
//======================================================================

    function addAthleteActivity() {
        global $connection_production;
        if (isset($_POST['add_profile_activity'])) {
            if (!!$_POST['activity_id']) {
                $post_profile_id_set = $_POST['profile_id_set'];
                $post_activity_id = $_POST['activity_id'];

                $sql_search_activity = "SELECT DISTINCT activity.id AS activity_id, activity.activity AS activity
					FROM activity LEFT JOIN profile_activity_sm ON activity.id=profile_activity_sm.activityId
					LEFT JOIN profile ON profile_activity_sm.profileId = profile.id
					WHERE profile.id=? AND activity.id=?";

                $stmt_search_activity = $connection_production->prepare($sql_search_activity);
                $stmt_search_activity->bind_param("ii", $post_profile_id_set, $post_activity_id);
                $stmt_search_activity->execute();
                $result_search_activity = $stmt_search_activity->get_result();
                $row_search_activity = $result_search_activity->fetch_assoc();
                $stmt_search_activity->close();

                if (!$row_search_activity['activity_id'] || ($row_search_activity['activity_id'] == NULL)) {

                    $query_add_profile_activity_sm = "INSERT INTO profile_activity_sm (activityId, profileId) VALUES (?, ?)";
                    $stmt_add_profile_activity_sm = $connection_production->prepare($query_add_profile_activity_sm);
                    $stmt_add_profile_activity_sm->bind_param("ii", $post_activity_id, $post_profile_id_set);
                    $stmt_add_profile_activity_sm->execute();
                    $result_add_profile_activity_sm = $stmt_add_profile_activity_sm->get_result();
                    $last_id = $connection_production->insert_id;
                    $stmt_add_profile_activity_sm->close();

                    $update_string = 'profile: '.$_POST['profile_id_set'].' , added activity: '.$row_search_activity['activity'].', and added profile activity association id: '.$last_id ;
                    insertChange($_SESSION['account_id'], 'profile', 'add activity', $post_profile_id_set, $update_string);
                    header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#add_activity");
                } else {
                    echo "<h3 style='color:red;'>Activity: ".$row_search_activity['activity']." already associated with profile</h3>";
                }
            } else {
            	echo "<h3 style='color:red;'>please select an activity</h3>";
            }
        }
    }

//======================================================================
// DELETE ATHLETE ACTIVITY
//======================================================================

    function deleteAthleteActivity() {
        global $connection_production;
        if (isset($_POST['delete_profile_activity'])) {
            if (!!$_POST['query_activity_id']) {
                $post_profile_id_set = $_POST['profile_id_set'];
                $post_activity_id = $_POST['query_activity_id'];
                $query_activity = $_POST['query_activity'];
                $query_profile_activity_sm_id = $_POST['query_profile_activity_sm_id'];
                $video_found_with_activity = false;
                $video_found_with_team = false;
                $temparray = [];

                $sqlsportvideos = "SELECT DISTINCT video.id AS videoId FROM video
					LEFT JOIN profile_activity_sm_video_sm ON video.id=profile_activity_sm_video_sm.videoId
					LEFT JOIN profile_activity_sm ON profile_activity_sm_video_sm.profileActivitySmId=profile_activity_sm.id
					LEFT JOIN profile ON profile_activity_sm.profileId=profile.id
					LEFT JOIN activity ON profile_activity_sm.activityId = activity.id
					WHERE activity.id=? AND profile.id =?";

                $sqlsportvideosteam = "SELECT DISTINCT video.id AS videoId FROM video
					LEFT JOIN profile_franchise_sm_video_sm ON video.id=profile_franchise_sm_video_sm.videoId
					LEFT JOIN profile_franchise_sm ON profile_franchise_sm_video_sm.profileFranchiseSmId=profile_franchise_sm.id
					LEFT JOIN profile ON profile_franchise_sm.profileId=profile.id
					LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
					LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
					LEFT JOIN partition_franchise_sm ON partition_franchise_sm.franchiseId=franchise.id
					LEFT JOIN `partition` AS partition_LP ON partition_LP.id=partition_franchise_sm.partitionId
					LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id
					LEFT JOIN entity_activity_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
					LEFT JOIN activity ON activity.id=entity_activity_sm.activityId
					WHERE activity.id=? AND profile.id=?";

                $stmt_search_activity_videos = $connection_production->prepare($sqlsportvideos);
                $stmt_search_activity_videos->bind_param("ii", $post_activity_id, $post_profile_id_set);
                $stmt_search_activity_videos->execute();
                $result_search_activity_videos = $stmt_search_activity_videos->get_result();
                if ($result_search_activity_videos->num_rows > 0) {
                    while ($row_search_activity_videos = $result_search_activity_videos->fetch_assoc()) {
                        if (!!$row_search_activity_videos['videoId']) {
                            array_push($temparray, $row_search_activity_videos["videoId"]);
                            $video_found_with_team = true;
                        }
                    }
                }

                $stmt_search_team_videos = $connection_production->prepare($sqlsportvideosteam);
                $stmt_search_team_videos->bind_param("ii", $post_activity_id, $post_profile_id_set);
                $stmt_search_team_videos->execute();
                $result_search_team_videos = $stmt_search_team_videos->get_result();
                if ($result_search_team_videos->num_rows > 0) {
                    while ($row_search_team_videos = $result_search_team_videos->fetch_assoc()) {
                        if (!!$row_search_team_videos['videoId']) {
                            array_push($temparray, $row_search_team_videos["videoId"]);
                            $video_found_with_activity = true;
                        }
                    }
                }

                if (!!$video_found_with_team || !!$video_found_with_activity) {
                    for ($i=0; $i < count($temparray); $i++) {
                        echo "<h3 style='color:red;'>Activity found associated with team or activity:</h3>";
                        echo "<h3 style='color:red;'>video id: ".$temparray[$i]."</h3>";
                    }
                } else {
                    $sql_delete_profile_activity = "DELETE FROM profile_activity_sm WHERE id=?";
                    $stmt_delete_profile_activity = $connection_production->prepare($sql_delete_profile_activity);
                    $stmt_delete_profile_activity->bind_param("i", $query_profile_activity_sm_id);
                    $result_profile_activity = $stmt_delete_profile_activity->execute();
                    $stmt_delete_profile_activity->close();

                    $update_string = 'profile: '.$_POST['profile_id_set'].' , deleted activity: '.$query_activity.', from profile and deleted profile activity association id: '.$query_profile_activity_sm_id ;
                    insertChange($_SESSION['account_id'], 'profile', 'delete activity', $post_profile_id_set, $update_string);
                    header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#activities");
                }
            } else {
                echo "<h3 style='color:red;'>something went wrong with deleting activity</h3>";
            }
        }
    }

//======================================================================
// ADD ATHLETE TEAM POSITION
//======================================================================

    function addAthleteTeamPosition() {
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
                    header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#teams");
                } else {
                   echo "<h3 style='color:red;'>role: ".$post_role_name." - already exists on team id: ".$team_role_id."</h3>";
                }
            } else {
                echo "<h3 style='color:red;'>You need to select a role</h3>";
            }
        }
    }

//======================================================================
// DELETE ATHLETE TEAM POSITION
//======================================================================

    function deleteAthleteTeamPosition() {
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
	            header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#teams");
            } else {
                echo "<h3 style='color:red;'>You need to select a role</h3>";
            }
        }
    }

//======================================================================
// UPDATE COACH TITLE
//======================================================================

    function updateCoachTeamTitle() {
        global $connection_production;
        if (isset($_POST['update_title'])) {
            if (!!$_POST['p_f_sm_t_sm_id']) {
                $title = $_POST['title'];
                $team_title_id = $_POST['team_title_id'];
                $post_profile_id_set = $_POST['profile_id_set'];
                $title_id = $_POST['title_id'];

                $sql_delete_title = "UPDATE title SET title=? WHERE id=?";
                $stmt_delete_title = $connection_production->prepare($sql_delete_title);
                $stmt_delete_title->bind_param("si", $title, $title_id);
                $stmt_delete_title->execute();
                $stmt_delete_title->close();

                $update_string = 'profile: '.$_POST['profile_id_set'].' update title : '. $title_id.', for team id: '.$team_title_id;
                insertChange($_SESSION['account_id'], 'profile', 'update title', $post_profile_id_set, $update_string);
                header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#teams");
            } else {
                echo "<h3 style='color:red;'>profile franchise title sm not found</h3>";
            }
        }
    }

//======================================================================
// ADD COACH TITLE
//======================================================================

    function addCoachTeamTitle() {
        global $connection_production;
        if (isset($_POST['add_title'])) {
            if (!!$_POST['title']) {
                $title = $_POST['title'];
                $team_title_id = $_POST['team_title_id'];
                $post_profile_id_set = $_POST['profile_id_set'];

                $query_profile_franchise_sm_id = "SELECT DISTINCT profile_franchise_sm.id AS profile_franchise_sm_id
                	FROM profile_franchise_sm
                	LEFT JOIN profile ON profile.id = profile_franchise_sm.profileId
                	LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=profile_franchise_sm.franchiseId
                	LEFT JOIN team ON team.id=franchise_team_sm.teamId
                	WHERE profile.id=".$post_profile_id_set." AND team.id=".$team_title_id;

                $select_profile_franchise_sm_id = mysqli_query($connection_production, $query_profile_franchise_sm_id);

                if (!!$select_profile_franchise_sm_id) {
                    $row_profile_franchise_sm_id = mysqli_fetch_assoc($select_profile_franchise_sm_id);
                    $profile_franchise_sm_id = $row_profile_franchise_sm_id['profile_franchise_sm_id'];

                    if (!!$profile_franchise_sm_id) {
                        $sql_title = "INSERT INTO title (title) VALUES (?)";
                        $prepared_title = $connection_production->prepare($sql_title);
                        $prepared_title->bind_param("s", $title);
                        $result_title=$prepared_title->execute();
                        $last_title_id = $connection_production->insert_id;
                        $prepared_title->close();

                        createUUID('title', $last_title_id);

                        $sql_p_f_sm_t_sm = "INSERT INTO profile_franchise_sm_title_sm (titleId, profileFranchiseSmId) VALUES (?, ?)";
                        $prepared_p_f_sm_t_sm = $connection_production->prepare($sql_p_f_sm_t_sm);
                        $prepared_p_f_sm_t_sm->bind_param("ii", $last_title_id, $profile_franchise_sm_id);
                        $result_p_f_sm_t_sm=$prepared_p_f_sm_t_sm->execute();
                        $last_id = $connection_production->insert_id;
                        $prepared_p_f_sm_t_sm->close();

                        $update_string = 'profile: '.$_POST['profile_id_set'].' add title id: '.$last_title_id.', title: '.$title.' to team: '.$team_title_id.', added profile frnchise title join table id: '.$last_id;
                        insertChange($_SESSION['account_id'], 'profile', 'add title', $post_profile_id_set, $update_string);
                        header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#teams");
                    } else {
                        echo "<h3 style='color:red;'>profile franchise sm not found</h3>";
                    }
                } else {
                    echo "<h3 style='color:red;'>profile franchise sm not found</h3>";
                }
            } else {
                echo "<h3 style='color:red;'>You need to enter a title</h3>";
            }
        }
    }

//======================================================================
// DELETE COACH TITLE
//======================================================================

    function deleteCoachTeamTitle() {
        global $connection_production;
        if (isset($_POST['delete_title'])) {
            if (!!$_POST['p_f_sm_t_sm_id']) {
                $team_title_id = $_POST['team_title_id'];
                $post_profile_id_set = $_POST['profile_id_set'];
                $p_f_sm_t_sm_id = $_POST['p_f_sm_t_sm_id'];
                $title_id = $_POST['title_id'];

                $sql_delete_p_f_sm_t_sm = "DELETE FROM profile_franchise_sm_title_sm WHERE id=?";
                $stmt_delete_p_f_sm_t_sm = $connection_production->prepare($sql_delete_p_f_sm_t_sm);
                $stmt_delete_p_f_sm_t_sm->bind_param("i", $p_f_sm_t_sm_id);
                $stmt_delete_p_f_sm_t_sm->execute();
                $stmt_delete_p_f_sm_t_sm->close();

                $sql_delete_title = "DELETE FROM title WHERE id=?";
                $stmt_delete_title = $connection_production->prepare($sql_delete_title);
                $stmt_delete_title->bind_param("i", $title_id);
                $stmt_delete_title->execute();
                $stmt_delete_title->close();

                $update_string = 'profile: '.$_POST['profile_id_set'].' deleted title : '. $title_id.', from team id: '.$team_title_id.', deleted profile franchse title join table id: '.$p_f_sm_t_sm_id;
                insertChange($_SESSION['account_id'], 'profile', 'deleted title', $post_profile_id_set, $update_string);
                header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#teams");
            } else {
                echo "<h3 style='color:red;'>You need to select a title</h3>";
            }
        }
    }

//======================================================================
// ADD ATHLETE TEAM PERIOD
//======================================================================

    function addAthleteTeamPeriod() {
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
                header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#teams");
            } else {
                echo "<h3 style='color:red;'>You need to select a period</h3>";
            }
        }
    }

//======================================================================
// DELETE ATHLETE TEAM PERIOD
//======================================================================

    function deleteAthleteTeamPeriod() {
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
                header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#teams");
            } else {
                echo "<h3 style='color:red;'>You need to select a role</h3>";
            }
        }
    }

//======================================================================
// ADD ATHLETE TO TEAM
//======================================================================

    function addTeamToProfile() {
        global $connection_production;
        if (isset($_POST['add_profile_team'])) {
            if (!!$_POST['team_id']) {
                $post_profile_id_set = $_POST['profile_id_set'];
                $post_team_id = $_POST['team_id'];

                $sql_search_team_found = "SELECT DISTINCT profile.id AS profile_id, franchise.id AS franchise_id FROM profile
					LEFT JOIN profile_franchise_sm ON profile_franchise_sm.profileId=profile.id
					LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
					LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
					LEFT JOIN team ON team.id=franchise_team_sm.teamId
					WHERE profile.id=? AND team.id=?";
                $stmt_search_team_found = $connection_production->prepare($sql_search_team_found);
                $stmt_search_team_found->bind_param("ii", $post_profile_id_set, $post_team_id);
                $stmt_search_team_found->execute();
                $result_search_team_found = $stmt_search_team_found->get_result();
                $row_search_team_found = $result_search_team_found->fetch_assoc();
                $stmt_search_team_found->close();

                if ($result_search_team_found->num_rows == 0
					|| (!$row_search_team_found['profile_id'] || $row_search_team_found['profile_id'] == NULL)
					&& (!$row_search_team_found['franchise_id'] || $row_search_team_found['franchise_id'] == NULL)) {

                    $sql_search_team_franchise = "SELECT DISTINCT team.id AS team_id, team.name AS team_name, franchise.id AS franchise_id
						FROM team LEFT JOIN franchise_team_sm ON team.id=franchise_team_sm.teamId
						LEFT JOIN franchise ON franchise.id=franchise_team_sm.franchiseId
						WHERE team.id=?";
                    $prepared_search_team_franchise = $connection_production->prepare($sql_search_team_franchise);
                    $prepared_search_team_franchise->bind_param("i", $post_team_id);
                    $result_search_team_franchise = $prepared_search_team_franchise->execute();
                    $result_search_team_franchise = $prepared_search_team_franchise->get_result();
                    $prepared_search_team_franchise->close();

                    if ($result_search_team_franchise->num_rows == 0) {
                         echo "<h3 style='color:red;'>franchise association to team not found</h3>";
                    } else if ($result_search_team_franchise->num_rows == 1) {
                        $row_search_team_franchise = $result_search_team_franchise->fetch_assoc();

                        $sql_add_profile_franchise_sm = "INSERT INTO profile_franchise_sm (franchiseId, profileId) VALUES (?, ?)";

                        $prepared_add_profile_franchise_sm = $connection_production->prepare($sql_add_profile_franchise_sm);
                        $prepared_add_profile_franchise_sm->bind_param("ii", $row_search_team_franchise['franchise_id'], $post_profile_id_set);
                        $result_add_profile_franchise_sm=$prepared_add_profile_franchise_sm->execute();
                        $last_id = $connection_production->insert_id;
                        $prepared_add_profile_franchise_sm->close();

                        $update_string = 'profile: '.$_POST['profile_id_set'].' , added team: '.$row_search_team_franchise['team_name'].', and added profile franchise/team association id: '.$last_id;
                        insertChange($_SESSION['account_id'], 'profile', 'add team', $post_profile_id_set, $update_string);
                        header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#add_team");

                    } else if ($result_search_team_franchise->num_rows > 1) {
                        echo "<h3 style='color:red;'>too many associations with franchise id: ";
                        while ($row_search_team = $result_search_team_franchise->fetch_assoc()) {
                            echo $row_search_team_franchise['franchise_id']."<br>";
                        }
                    } else if ($result_search_team_franchise->num_rows > 1) {
                        echo "<h3 style='color:red;'>too many associations with team id: ".$post_team_id."</h3>";
                    }
                } else if ($result_search_team_found->num_rows == 1) {
                    echo "<h3 style='color:red;'>team id: ".$post_team_id." already associated with profile</h3>";
                } else if ($result_search_team_found->num_rows > 1) {
                    echo "<h3 style='color:red;'>team has too many associations with franchise id: ".$post_team_id."</h3>";
                }
            } else {
                echo "<h3 style='color:red;'>please select a team</h3>";
            }
        }
    }

//======================================================================
// DELETE ATHLETE FROM TEAM
//======================================================================

    function deleteAthleteFromTeam() {
        global $connection_production;
        if (isset($_POST['delete_team_association'])) {
            if (!!$_POST['query_team_id']) {
                $profile_franchise_sm_id = $_POST['profile_franchise_sm_id'];
                $post_profile_id_set = $_POST['profile_id_set'];
                $delete_team_id = $_POST['query_team_id'];

                $sql_team_video = "SELECT DISTINCT video.id AS video_id, profile_franchise_sm_video_sm.id AS profile_franchise_sm_video_sm_id FROM video
                	LEFT JOIN profile_franchise_sm_video_sm ON video.id=profile_franchise_sm_video_sm.videoId
                	LEFT JOIN profile_franchise_sm ON profile_franchise_sm_video_sm.profileFranchiseSmId=profile_franchise_sm.id
                	LEFT JOIN profile ON profile_franchise_sm.profileId=profile.id
                	LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
                	LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
                	LEFT JOIN team ON team.id=franchise_team_sm.teamId
                	WHERE team.id=? AND profile.id=?";
                $stmt = $connection_production->prepare($sql_team_video);
                $stmt->bind_param("ii", $delete_team_id, $post_profile_id_set);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row_delete_video = $result->fetch_assoc()) {
                        $video_id = $row_delete_video["video_id"];
                        $profile_franchise_sm_video_sm_id = $row_delete_video["profile_franchise_sm_video_sm_id"];

                        if (!!$profile_franchise_sm_video_sm_id && ($profile_franchise_sm_video_sm_id != NULL)) {
                            $stmt2 = $connection_production->prepare("delete from `profile_franchise_sm_video_sm` WHERE id = ?");
                            $stmt2->bind_param("i", $profile_franchise_sm_video_sm_id);
                            $stmt2->execute();
                            $stmt2->close();
                        }
                        // if (!!$video_id && ($video_id != NULL)) {
                        //     $stmt2 = $connection_production->prepare("delete from `video` WHERE id = ?");
                        //     $stmt2->bind_param("i", $video_id);
                        //     $stmt2->execute();
                        //     $stmt2->close();
                        // }
                        if ((!!$profile_franchise_sm_video_sm_id && ($profile_franchise_sm_video_sm_id != NULL))
							|| (!!$video_id && ($video_id != NULL))) {

                            $update_string = 'profile: '.$_POST['profile_id_set'].' deleted team - video id: '.$video_id.', from team id: '.$delete_team_id.', deleted profile franchse video join table id: '.$profile_franchise_sm_video_sm_id;
                            insertChange($_SESSION['account_id'], 'profile', 'delete team - video', $post_profile_id_set, $update_string);
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
                	WHERE team.id=? AND profile.id=?";
                $stmt = $connection_production->prepare($sql_team_period);
                $stmt->bind_param("ii", $delete_team_id, $post_profile_id_set);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row_delete_team_period = $result->fetch_assoc()) {

                        $delete_profile_franchise_sm_period_sm_id = $row_delete_team_period["profile_franchise_sm_period_sm_id"];
                        $delete_period_id = $row_delete_team_period["period_id"];

                        if (!!$delete_profile_franchise_sm_period_sm_id && ($delete_profile_franchise_sm_period_sm_id != NULL)) {
                            $stmt_delete_p_f_sm_a_r_sm = "DELETE FROM profile_franchise_sm_period_sm WHERE id=?";
                            $stmt_delete_p_f_sm_a_r_sm = $connection_production->prepare($stmt_delete_p_f_sm_a_r_sm);
                            $stmt_delete_p_f_sm_a_r_sm->bind_param("i", $delete_profile_franchise_sm_period_sm_id);
                            $stmt_delete_p_f_sm_a_r_sm->execute();
                            $stmt_delete_p_f_sm_a_r_sm->close();
                        }
                        if (!!$delete_period_id && ($delete_period_id != NULL)) {
                            $stmt_delete_period = "DELETE FROM period WHERE id=?";
                            $stmt_delete_period = $connection_production->prepare($stmt_delete_period);
                            $stmt_delete_period->bind_param("i", $delete_period_id);
                            $stmt_delete_period->execute();
                            $stmt_delete_period->close();
                        }
                        if ((!!$delete_profile_franchise_sm_period_sm_id && ($delete_profile_franchise_sm_period_sm_id != NULL))
							|| (!!$delete_period_id && ($delete_period_id != NULL))) {

                            $update_string = 'profile: '.$_POST['profile_id_set'].' deleted team - period id: '.$delete_period_id.', from team id: '.$delete_team_id.', deleted profile franchse activity period join table id: '.$delete_profile_franchise_sm_period_sm_id;
                            insertChange($_SESSION['account_id'], 'profile', 'delete team - period', $_POST['profile_id_set'], $update_string);
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
                    WHERE team.id=? AND profile.id=?";
                $stmt = $connection_production->prepare($sql_team_role);
                $stmt->bind_param("ii", $delete_team_id, $post_profile_id_set);
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

                            $update_string = 'profile: '.$_POST['profile_id_set'].' deleted team - role : '. $role_name.', from team id: '.$delete_team_id.', deleted profile franchse activity role join table id: '.$p_f_sm_a_r_sm_id;
                            insertChange($_SESSION['account_id'], 'profile', 'delete team - role', $post_profile_id_set, $update_string);
                        }
                    }
                }
                $sql_delete_p_f_sm = "DELETE FROM profile_franchise_sm WHERE id=?";
                $stmt_delete_p_f_sm = $connection_production->prepare($sql_delete_p_f_sm);
                $stmt_delete_p_f_sm->bind_param("i", $profile_franchise_sm_id);
                $stmt_delete_p_f_sm->execute();
                $stmt_delete_p_f_sm->close();

                $update_string = 'profile: '.$_POST['profile_id_set'].' deleted profile team association : '.$profile_franchise_sm_id.', from team id: '.$delete_team_id;
                insertChange($_SESSION['account_id'], 'profile', 'delete team association', $post_profile_id_set, $update_string);
                header("location: categories.php?source=profile_update_athlete&profile_id=".$_POST['profile_id_set']."#teams");
            } else {
                echo "<h3 style='color:red;'>something went wrong with deleting activity</h3>";
            }
        }
    }

//======================================================================
// DELETE TEAM VIDEO
//======================================================================

	function profileDeleteTeamVideo() {
		global $connection_production;
		if (isset($_POST['delete_video_team'])) {
			if (!empty($_POST['delete_video_id'])) {
				$p_f_sm_v_sm_id = $_POST['p_f_sm_v_sm_id'];
				$team_id = $_POST['team_id'];
				$delete_video_id = $_POST['delete_video_id'];
				$profile_id = $_POST['profile_id_set'];

				$stmt1 = $connection_production->prepare("delete from `profile_franchise_sm_video_sm` WHERE id = ?");
				$stmt1->bind_param("i", $p_f_sm_v_sm_id);

                $stmt2 = $connection_production->prepare("UPDATE `video` SET profileAdminId=NULL WHERE id = ?");
                $stmt2->bind_param("i", $delete_video_id);

				// $stmt2 = $connection_production->prepare("delete from `video` WHERE id = ?");
				// $stmt2->bind_param("i", $delete_video_id);

				$update_string = 'deleted team-video id: '.$delete_video_id." - profile id: ".$profile_id.", team id: ".$team_id." :: ";
				$update_string .= "deleted p_f_sm_v_sm id: ".$p_f_sm_v_sm_id.", ";

				if ((deleteEventsHelper($delete_video_id, $update_string) != -1)
						&& ($stmt1->execute()) && ($stmt2->execute())
						&& (unassignAllProfileLinks($delete_video_id, $update_string) != -1)) {

					insertChange($_SESSION['account_id'], 'profile', 'delete video (team)', $profile_id, $update_string);
					header("location: categories.php?source=profile_update_athlete&profile_id=".$profile_id."#team_videos");
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

//======================================================================
// DELETE ACTIVITY VIDEO
//======================================================================

	function profileDeleteActivityVideo() {
		global $connection_production;
		if (isset($_POST['delete_video_activity'])) {
			if (!empty($_POST['delete_video_id'])) {
				$p_a_sm_v_sm_id = $_POST['p_a_sm_v_sm_id'];
				$activity = $_POST['solo_video_activity'];
				$delete_video_id = $_POST['delete_video_id'];
				$profile_id = $_POST['profile_id_set'];

				$stmt1 = $connection_production->prepare("delete from `profile_activity_sm_video_sm` WHERE id = ?");
				$stmt1->bind_param("i", $p_a_sm_v_sm_id);

                $stmt2 = $connection_production->prepare("UPDATE `video` SET profileAdminId=NULL WHERE id = ?");
                $stmt2->bind_param("i", $delete_video_id);

				// $stmt2 = $connection_production->prepare("delete from `video` WHERE id = ?");
				// $stmt2->bind_param("i", $delete_video_id);

				$update_string = 'deleted activity-video id: '.$delete_video_id." - profile id: ".$profile_id.", activity: ".$activity." :: ";
				$update_string .= "deleted p_a_sm_v_sm id: ".$p_a_sm_v_sm_id.", ";

				if ((deleteEventsHelper($delete_video_id, $update_string) != -1)
						&& ($stmt1->execute()) && ($stmt2->execute())
						&& (unassignAllProfileLinks($delete_video_id, $update_string) != -1)) {

					insertChange($_SESSION['account_id'], 'profile', 'delete video (activity)', $profile_id, $update_string);
					header("location: categories.php?source=profile_update_athlete&profile_id=".$profile_id."#activity_videos");
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

//======================================================================
// DELETE INDIVIDUAL VIDEO
//======================================================================

	function profileDeleteIndivVideo() {
		global $connection_production;
		if (isset($_POST['delete_video_indiv'])) {
			if (!empty($_POST['delete_video_id'])) {
				$p_v_sm_id = $_POST['p_v_sm_id'];
				$delete_video_id = $_POST['delete_video_id'];
				$profile_id = $_POST['profile_id_set'];

				$stmt1 = $connection_production->prepare("delete FROM `profile_video_sm` WHERE id = ?");
				$stmt1->bind_param("i", $p_v_sm_id);

				$stmt2 = $connection_production->prepare("UPDATE `video` SET profileAdminId=NULL WHERE id = ?");
				$stmt2->bind_param("i", $delete_video_id);

                // $stmt2 = $connection_production->prepare("delete FROM `video` WHERE id = ?");
                // $stmt2->bind_param("i", $delete_video_id);

				$update_string = 'deleted video id: '.$delete_video_id." - profile id: ".$profile_id." :: ";
				$update_string .= "deleted p_v_sm id: ".$p_v_sm_id.", ";

				if ((deleteEventsHelper($delete_video_id, $update_string) != -1)
						&& ($stmt1->execute()) && ($stmt2->execute())
						&& (unassignAllProfileLinks($delete_video_id, $update_string) != -1)) {

					insertChange($_SESSION['account_id'], 'profile', 'delete video (individual)', $profile_id, $update_string);
					header("location: categories.php?source=profile_update_athlete&profile_id=".$profile_id."#profile_videos");
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

//======================================================================
// REMOVE LINKED VIDEO
//======================================================================

	function profileRemoveLinkedVideo() {
		global $connection_production;
		if (isset($_POST['unlink_video'])) {
			$v_l_id = $_POST['v_l_id'];
			$linked_video_id = $_POST['linked_video_id'];
			$profile_id = $_POST['profile_id_set'];

			$del_link_sql = "DELETE FROM video_linked WHERE id=?";
			$del_link_stmt = $connection_production->prepare($del_link_sql);
			$del_link_stmt->bind_param("i", $v_l_id);

			if ($del_link_stmt->execute()) {
				$update_string = "profile id: ".$profile_id." :: ";
				$update_string .= "unlink video id: ".$linked_video_id.", deleted video_linked id: ".$v_l_id;
				insertChange($_SESSION['account_id'], 'profile', "Unlink video", $profile_id, $update_string);
				header("location: categories.php?source=profile_update_athlete&profile_id=".$profile_id."#linked_videos");
			} else {
				echo "<h3 style='color:red;'>Something went wrong</h3>";
			}
			$del_link_stmt->close();
		}
	}

//======================================================================
// ADD VIDEO TO PROFILE
//======================================================================

	function addVideoToProfile() {
		global $connection_production;
		if (isset($_POST['add_video'])) {
			$profile_id = $_POST['profile_id'];
			$video_id = $_POST['video_id'];
			$activity_id = $_POST['activity_id'];
			$team_id = $_POST['team_id'];

			if (empty($activity_id) && empty($team_id)) {
				$add_type = 1;
			} else if (!empty($activity_id) && empty($team_id)) {
				$add_type = 2;
			} else if (empty($activity_id) && !empty($team_id)) {
				$add_type = 3;
			} else {
				$add_type = -1;
			}

			$video_found_sql = "SELECT * FROM video WHERE id=?";
			$video_found_stmt = $connection_production->prepare($video_found_sql);
			$video_found_stmt->bind_param("i", $video_id);
			$video_found_stmt->execute();
			$row_video_found = $video_found_stmt->get_result()->fetch_assoc();
			$video_found_stmt->close();

			if (!!$row_video_found['id']) {

				$main_profile_check_sql = "SELECT profileAdminId AS main_profile_id FROM video WHERE video.id=?";
				$main_profile_check_stmt = $connection_production->prepare($main_profile_check_sql);
				$main_profile_check_stmt->bind_param("i", $video_id);
				$main_profile_check_stmt->execute();
				$row_main_profile_check = $main_profile_check_stmt->get_result()->fetch_assoc();
				$main_profile_check_stmt->close();

				$search_linked_video_sql = "SELECT * FROM video_linked WHERE videoId=? AND profileId=?";
				$search_linked_video_stmt = $connection_production->prepare($search_linked_video_sql);
				$search_linked_video_stmt->bind_param("ii", $video_id, $profile_id);
				$search_linked_video_stmt->execute();
				$row_search_linked_video = $search_linked_video_stmt->get_result()->fetch_assoc();
				$search_linked_video_stmt->close();

				if (!$row_main_profile_check['main_profile_id']) {
					//Add video as main profile
					if (!$row_search_linked_video['id']) {
						if ($add_type != -1) {
							if ($add_type == 1) {
								addSoloVideo($video_id, $profile_id);
							}
							else if ($add_type == 2) {
								addActivityVideo($video_id, $profile_id, $activity_id);
							}
							else if ($add_type == 3) {
								addTeamVideo($video_id, $profile_id, $team_id);
							}
						} else {
							echo "<h3 style='color:red'>Activity ID and Team ID cannot both be set</h3>";
						}
					} else {
						echo "<h3 style='color:red'>Video ID: ".$video_id." - Already added</h3>";
					}
				}
				else if ($add_type == 1) {
					//Add video as linked profile
					if (($row_main_profile_check['main_profile_id'] != $profile_id) && (!$row_search_linked_video['id'])) {

						$insert_linked_video_sql = "INSERT INTO video_linked (videoId, profileId) VALUES (?, ?)";
						$insert_linked_video_stmt = $connection_production->prepare($insert_linked_video_sql);
						$insert_linked_video_stmt->bind_param("ii", $video_id, $profile_id);

						if ($insert_linked_video_stmt->execute()) {
							$new_link_assoc_id = $connection_production->insert_id;
							$update_string = "profile id: ".$profile_id." :: assign linked video id: ".$video_id.", added video_linked id: ".$new_link_assoc_id;
							insertChange($_SESSION['account_id'], 'profile', 'assign linked video', $profile_id, $update_string);
							header("location: categories.php?source=profile_update_athlete&profile_id=".$profile_id."#add_video");
						} else {
							echo "<h3 style='color:red'>Something went wrong</h3>";
						}
						$insert_linked_video_stmt->close();
					} else {
						echo "<h3 style='color:red'>Video ID: ".$video_id." - Already added</h3>";
					}
				} else {
					echo "<h3 style='color:red'>Video ID: ".$video_id." - Already has main profile ID: ".$row_main_profile_check['main_profile_id']." - Only main profile can have activity ID or team ID</h3>";
				}
			} else {
				echo "<h3 style='color:red'>Video ID: ".$video_id." - Not found</h3>";
			}
		}
	}
	#Helper function for adding a video to an individual profile
	function addSoloVideo($video_id, $profile_id) {
		global $connection_production;

		$profile_search_sql = "SELECT * FROM profile_video_sm
			WHERE videoId=? AND profileId=?";
		$profile_search_stmt = $connection_production->prepare($profile_search_sql);
		$profile_search_stmt->bind_param("ii", $video_id, $profile_id);
		$profile_search_stmt->execute();
		$row_profile_search = $profile_search_stmt->get_result()->fetch_assoc();
		$profile_search_stmt->close();

		if (!$row_profile_search['id']) {

			$assoc_profile_sql = "INSERT INTO profile_video_sm (videoId, profileId) VALUES (?, ?)";
			$assoc_profile_stmt = $connection_production->prepare($assoc_profile_sql);
			$assoc_profile_stmt->bind_param("ii", $video_id, $profile_id);

			$set_main_profile_sql = "UPDATE video SET profileAdminId=? WHERE video.id=?";
			$set_main_profile_stmt = $connection_production->prepare($set_main_profile_sql);
			$set_main_profile_stmt->bind_param("ii", $profile_id, $video_id);

			if (($set_main_profile_stmt->execute()) && ($assoc_profile_stmt->execute())) {
				$new_assoc_id = $connection_production->insert_id;
				$update_string = "profile id: ".$profile_id." :: assign solo video id: ".$video_id.", added profile-video-association id: ".$new_assoc_id;
				insertChange($_SESSION['account_id'], 'profile', 'assign solo video', $profile_id, $update_string);
				header("location: categories.php?source=profile_update_athlete&profile_id=".$profile_id."#add_video");
			} else {
				echo "<h3 style='color:red'>Something went wrong</h3>";
			}
		} else {
			echo "<h3 style='color:red'>Video ID: ".$video_id." - already has solo profile ID: ".$profile_id."</h3>";
		}
	}
	#Helper function for adding a video to a profile through an activity
	function addActivityVideo($video_id, $profile_id, $activity_id) {
		global $connection_production;

		$profile_activity_search_sql = "SELECT * FROM profile_activity_sm
			WHERE profileId=? AND activityId=?";
		$profile_activity_search_stmt = $connection_production->prepare($profile_activity_search_sql);
		$profile_activity_search_stmt->bind_param("ii", $profile_id, $activity_id);
		$profile_activity_search_stmt->execute();
		$row_profile_activity_search = $profile_activity_search_stmt->get_result()->fetch_assoc();
		$profile_activity_search_stmt->close();

		if (!$row_profile_activity_search['id']) {

			$profile_activity_insert_sql = "INSERT INTO profile_activity_sm (profileId, activityId) VALUES (?, ?)";
			$profile_activity_insert_stmt = $connection_production->prepare($profile_activity_insert_sql);
			$profile_activity_insert_stmt->bind_param("ii", $profile_id, $activity_id);
			if ($profile_activity_insert_stmt->execute()) {
				$p_a_sm_id = $connection_production->insert_id;
			}
			$profile_activity_insert_stmt->close();
		} else {
			$p_a_sm_id = $row_profile_activity_search['id'];
		}

		if (isset($p_a_sm_id)) {

			$p_a_sm_v_search_sql = "SELECT * FROM profile_activity_sm_video_sm
				WHERE profileActivitySmId=? AND videoId=?";
			$p_a_sm_v_search_stmt = $connection_production->prepare($p_a_sm_v_search_sql);
			$p_a_sm_v_search_stmt->bind_param("ii", $p_a_sm_id, $video_id);
			$p_a_sm_v_search_stmt->execute();
			$row_p_a_sm_v_search = $p_a_sm_v_search_stmt->get_result()->fetch_assoc();
			$p_a_sm_v_search_stmt->close();

			if (!$row_p_a_sm_v_search['id']) {

				$p_a_sm_v_insert_sql = "INSERT INTO profile_activity_sm_video_sm (profileActivitySmId, videoId) VALUES (?, ?)";
				$p_a_sm_v_insert_stmt = $connection_production->prepare($p_a_sm_v_insert_sql);
				$p_a_sm_v_insert_stmt->bind_param("ii", $p_a_sm_id, $video_id);

				$set_main_profile_sql = "UPDATE video SET profileAdminId=? WHERE video.id=?";
				$set_main_profile_stmt = $connection_production->prepare($set_main_profile_sql);
				$set_main_profile_stmt->bind_param("ii", $profile_id, $video_id);

				if (($set_main_profile_stmt->execute()) && ($p_a_sm_v_insert_stmt->execute())) {
					$new_p_a_sm_v_id = $connection_production->insert_id;
					$update_string = "profile id: ".$profile_id." :: assign video/activity ids: ".$video_id."/".$activity_id.", p_a_sm_id ID: ".$p_a_sm_id.", added p_a_sm_v_sm ID: ".$new_p_a_sm_v_id;
					insertChange($_SESSION['account_id'], 'profile', 'assign video/activity', $profile_id, $update_string);
					header("location: categories.php?source=profile_update_athlete&profile_id=".$profile_id."#add_video");
				} else {
					echo "<h3 style='color:red'>Something went wrong</h3>";
				}
				$p_a_sm_v_insert_stmt->close();
				$set_main_profile_stmt->close();
			} else {
				echo "<h3 style='color:red'>Video ID: ".$video_id." - already has p_a_sm_v_sm ID: ".$row_p_a_sm_v_search['id']."</h3>";
			}
		} else {
			echo "<h3 style='color:red'>Something went wrong</h3>";
		}
	}
	#Helper function for adding a video to a profile through a team
	function addTeamVideo($video_id, $profile_id, $team_id) {
		global $connection_production;

		$profile_team_search_sql = "SELECT * FROM profile_franchise_sm
			WHERE profileId=? AND franchiseId=?";
		$profile_team_search_stmt = $connection_production->prepare($profile_team_search_sql);
		$profile_team_search_stmt->bind_param("ii", $profile_id, $team_id);
		$profile_team_search_stmt->execute();
		$row_profile_team_search = $profile_team_search_stmt->get_result()->fetch_assoc();
		$profile_team_search_stmt->close();

		if (!$row_profile_team_search['id']) {

			$profile_team_insert_sql = "INSERT INTO profile_franchise_sm (profileId, franchiseId) VALUES (?, ?)";
			$profile_team_insert_stmt = $connection_production->prepare($profile_team_insert_sql);
			$profile_team_insert_stmt->bind_param("ii", $profile_id, $team_id);
			if ($profile_team_insert_stmt->execute()) {
				$p_f_sm_id = $connection_production->insert_id;
			}
			$profile_team_insert_stmt->close();
		} else {
			$p_f_sm_id = $row_profile_team_search['id'];
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
					$update_string = "profile id: ".$profile_id." :: assign video/team ids: ".$video_id."/".$team_id.", p_f_sm_id ID: ".$p_f_sm_id.", added p_f_sm_v_sm ID: ".$new_p_f_sm_v_id;
					insertChange($_SESSION['account_id'], 'profile', 'assign video/team', $profile_id, $update_string);
					header("location: categories.php?source=profile_update_athlete&profile_id=".$profile_id."#add_video");
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
	}
?>
