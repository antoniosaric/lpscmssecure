<?php

function deleteVideoById($video_id = $_GET['video_id']){

	global $connection_production;

	$sql_profile_videos = 'SELECT video.id AS video_id, video.profileAdminId AS profileAdminId, video.reference AS reference FROM video WHERE video.id=?';
	$stmt_profile->bind_param("i", $video_id);
	$stmt_profile->execute();
	$result_profile = $stmt_profile->get_result();
	$stmt_profile->close();
	//row count for individual videos
	if(!!$result_profile_videos){
		if($result_profile_videos->num_rows > 0){
		  	while( $row_profile_videos = $result_profile_videos->fetch_assoc()){
	            if ( strpos($row_profile_videos['reference'], "s3.us-east-2.amazonaws.com") !== false ){
	                // http://s3.us-east-2.amazonaws.com/lpsportsvideo/xxxxx/output.mp4
	                $video_name_pos = strlen("http://s3.us-east-2.amazonaws.com/xxxxx/");
	                $video_name = substr($row_profile_videos['reference'], $video_name_pos);
	                $tmp = explode('/', $video_name);
	      			    $folder_name = $tmp[0];
	                
	                $s3Region = 'us-east-2';
	                $s3Client = new S3Client([
	                  'version'     => 'latest',
	                  'region'      => $s3Region,
	                  'credentials' => [
	                    'key'    => 'xxxxx',
	                    'secret' => 'xxxxx',
	                  ],
	                  ]);
	                $bucket="lpsportsvideo";
	                //$result = $s3Client->deleteObject(array(
	                $result = $s3Client->deleteMatchingObjects($bucket,$folder_name);
	                
	            }

				$stmt = $conn->prepare("delete from `video_likes` where video_likes.videoId = ?");
				$stmt->bind_param("i", $row_profile_videos['video_id']);
				$stmt->execute();
				$stmt->close();

				$stmt = $conn->prepare("delete from `participant_favorites` where participant_favorites.videoId = ?");
				$stmt->bind_param("i", $row_profile_videos['video_id']);
				$stmt->execute();
				$stmt->close();          

				$stmt = $conn->prepare("delete from `video` where id = ?");
				$stmt->bind_param("i", $row_profile_videos['video_id']);
				$stmt->execute();
				$stmt->close();

				$stmt2 = $conn->prepare("delete from `profile_franchise_sm_video_sm` where videoId = ?");
				$stmt2->bind_param("i", $row_profile_videos['video_id']);
				$stmt2->execute();            
				$stmt2->close();

				$stmt3 = $conn->prepare("delete from `profile_activity_sm_video_sm` where videoId = ?");
				$stmt3->bind_param("i", $row_profile_videos['video_id']);
				$stmt3->execute();
				$stmt3->close();

				$stmt4 = $conn->prepare("delete from `profile_video_sm` where videoId = ?");
				$stmt4->bind_param("i", $row_profile_videos['video_id']);
				$stmt4->execute();
				$stmt4->close();

				$stmt4 = $conn->prepare("delete from `video_linked` where videoId = ?");
				$stmt4->bind_param("i", $row_profile_videos['video_id']);
				$stmt4->execute();
				$stmt4->close();

			}
		}
	}
}

function deleteProfileById($profile_id = $_GET['profile_id']){

	global $connection_production;

	$stmt_profile = $conn->prepare("SELECT * FROM `profile` WHERE profile.id = ?");
	$stmt_profile->bind_param("i", $profile_id);
	$stmt_profile->execute();
	$result_profile = $stmt_profile->get_result();
	$stmt_profile->close();

	if(!!$result_profile && $result_profile->num_rows > 0){
		$row_profile = $result_profile->fetch_assoc();

		$sql_profile_videos = 'SELECT video.id AS video_id, video.profileAdminId AS profileAdminId, video.reference AS reference FROM video WHERE video.profileAdminId='.$row_profile['id'].'';
		$result_profile_videos = $conn->query($sql_profile_videos);
		//row count for individual videos
		if(!!$result_profile_videos){
			if($result_profile_videos->num_rows > 0){
			  	while( $row_profile_videos = mysqli_fetch_assoc($result_profile_videos) ){
			  		if($row_profile_videos['profileAdminId'] == $row_profile['id']){
						deleteVideoById($row_profile_videos['video_id'])
					}else{
						$stmt4 = $conn->prepare("delete from `video_linked` where videoId = ? && profileId = ?");
						$stmt4->bind_param("ii", $row_profile_videos['video_id'], $row_profile['id']);
						$stmt4->execute();
						$stmt4->close();
					}
				}
			}
		}

		$sqlMatchIdsForDelete = "SELECT profile_franchise_sm.id AS profile_franchise_sm_id, franchise.id AS franchise_id, profile_franchise_sm_activity_role_sm.id AS profile_franchise_sm_activity_role_sm_id, profile_franchise_sm_title_sm.id AS profile_franchise_sm_title_sm_id, title.id AS title_id, profile_franchise_sm_period_sm.id AS profile_franchise_sm_period_sm_id, period.id AS period_id  
		FROM franchise
		LEFT JOIN profile_franchise_sm ON profile_franchise_sm.franchiseId=franchise.id
		LEFT JOIN profile ON profile.id=profile_franchise_sm.profileId
		LEFT JOIN profile_franchise_sm_activity_role_sm ON profile_franchise_sm_activity_role_sm.profileFranchiseSmId=profile_franchise_sm.id
		LEFT JOIN profile_franchise_sm_title_sm ON profile_franchise_sm_title_sm.profileFranchiseSmId=profile_franchise_sm.id
		LEFT JOIN title ON title.id = profile_franchise_sm_title_sm.titleId
		LEFT JOIN profile_franchise_sm_period_sm ON profile_franchise_sm_period_sm.profileFranchiseSmId=profile_franchise_sm.id
		LEFT JOIN period ON period.id=profile_franchise_sm_period_sm.periodId WHERE profile.id=".$row_profile['id']."";
		$resultMatchIdsForDelete = $conn->query($sqlMatchIdsForDelete);
		//row count for individual videos
		if(!!$resultMatchIdsForDelete){
			if($resultMatchIdsForDelete->num_rows > 0){
			  	while( $rowMatchIdsForDelete = mysqli_fetch_assoc($resultMatchIdsForDelete) ){

					$stmt = $conn->prepare("delete FROM `period` WHERE period.id = ?");
					$stmt->bind_param("i", $rowMatchIdsForDelete['period_id']);
					$stmt->execute();
					$stmt->close();

					$stmt = $conn->prepare("delete FROM `profile_franchise_sm_period_sm` WHERE profile_franchise_sm_period_sm.id = ?");
					$stmt->bind_param("i", $rowMatchIdsForDelete['profile_franchise_sm_period_sm_id']);
					$stmt->execute();
					$stmt->close();

					$stmt = $conn->prepare("delete FROM `title` WHERE title.id = ?");
					$stmt->bind_param("i", $rowMatchIdsForDelete['title']);
					$stmt->execute();
					$stmt->close();

					$stmt = $conn->prepare("delete FROM `profile_franchise_sm_title_sm` WHERE profile_franchise_sm_title_sm.id = ?");
					$stmt->bind_param("i", $rowMatchIdsForDelete['profile_franchise_sm_title_sm_id']);
					$stmt->execute();
					$stmt->close();

					$stmt = $conn->prepare("delete FROM `profile_franchise_sm_activity_role_sm` WHERE profile_franchise_sm_activity_role_sm.id = ?");
					$stmt->bind_param("i", $rowMatchIdsForDelete['profile_franchise_sm_activity_role_sm_id']);
					$stmt->execute();
					$stmt->close();

					$stmt = $conn->prepare("delete FROM `franchise` WHERE franchise.id = ?");
					$stmt->bind_param("i", $rowMatchIdsForDelete['franchise_id']);
					$stmt->execute();
					$stmt->close();

					$stmt = $conn->prepare("delete FROM `profile_franchise_sm` WHERE profile_franchise_sm.id = ?");
					$stmt->bind_param("i", $rowMatchIdsForDelete['profile_franchise_sm_id']);
					$stmt->execute();
					$stmt->close();

				}
			}
		}

		if($row_profile['profileTypeId'] == 1){
		    $sql_profile_nickname = "SELECT DISTINCT alternate_name.id AS alternate_name_id, profile_alternate_name_sm.id AS profile_alternate_name_sm_id FROM alternate_name LEFT JOIN profile_alternate_name_sm ON profile_alternate_name_sm.alternateNameId = alternate_name.id LEFT JOIN profile ON profile.id = profile_alternate_name_sm.profileId WHERE profile.id=?";

		    $stmt_profile_nickname = $conn->prepare($sql_profile_nickname);
		    $stmt_profile_nickname->bind_param("i", $row_profile['id']);
		    $stmt_profile_nickname->execute();
		    $result_profile_nickname = $stmt_profile_nickname->get_result();
		    $stmt_profile_nickname->close();

		    if ( $result_profile_nickname->num_rows > 0 ){
		        while($row_profile_nickname = $result_profile_nickname->fetch_assoc()){
					$stmt = $conn->prepare("delete FROM `alternate_name` WHERE alternate_name.id = ?");
					$stmt->bind_param("i", $row_profile_nickname['alternate_name_id']);
					$stmt->execute();
					$stmt->close();

					$stmt = $conn->prepare("delete FROM `profile_alternate_name_sm` WHERE profile_alternate_name_sm.id = ?");
					$stmt->bind_param("i", $row_profile_nickname['profile_alternate_name_sm_id']);
					$stmt->execute();
					$stmt->close();
		        }

		    }
		}else if($profileTypeId == 2){

		    $sql_profile_specialty = "SELECT DISTINCT specialty.id AS specialtyId, specialty.specialty AS specialty FROM specialty LEFT JOIN profile_specialty_sm ON profile_specialty_sm.specialtyId = specialty.id LEFT JOIN profile ON profile.id = profile_specialty_sm.profileId WHERE profile.id=?";

		        $stmt_profile_specialty = $conn->prepare($sql_profile_specialty);
		        $stmt_profile_specialty->bind_param("i", $row_profile['id']);
		        $stmt_profile_specialty->execute();
		        $result_profile_specialty = $stmt_profile_specialty->get_result();
		        $stmt_profile_specialty->close();

		    if ( $result_profile_specialty->num_rows > 0 ){
		        while($row_profile_specialty = $result_profile_specialty->fetch_assoc()){

					$stmt = $conn->prepare("delete FROM `specialty` WHERE specialty.id = ?");
					$stmt->bind_param("i", $row_profile['id']);
					$stmt->execute();
					$stmt->close();

					$stmt = $conn->prepare("delete FROM `profile_specialty_sm` WHERE profile_specialty_sm.id = ?");
					$stmt->bind_param("i", $row_profile['id']);
					$stmt->execute();
					$stmt->close();
		        }
		    }
		}

		$stmt = $conn->prepare("delete FROM `profile_activity_sm` WHERE profile_activity_sm.profileId = ?");
		$stmt->bind_param("i", $row_profile['id']);
		$stmt->execute();
		$stmt->close();

		$sqlaccountpic = 'SELECT image.id AS image_id FROM image INNER JOIN profile_image_sm ON profile_image_sm.imageId=image.id INNER JOIN profile ON profile_image_sm.profileId=profile.id WHERE profile.id='.$row_profile['id'].'';
		$resultaccountpic = $conn->query($sqlaccountpic);

		if($resultaccountpic){
			if($resultaccountpic->num_rows > 0){
		  		$rowaccountpic = $resultaccountpic->fetch_assoc();

		  		$sqldeleteaccountpic = "DELETE FROM profile_image_sm WHERE profile_image_sm.profileId=".$row_profile['id']." AND image.id=".$rowaccountpic['image_id']."";  
		  		$resultDeleteaccountpic = $conn->query($sqldeleteaccountpic);

		  		$sqldeleteaccountpic2 = "DELETE FROM image WHERE image.id=".$rowaccountpic['image_id']."";  
		  		$resultDeleteaccountpic2 = $conn->query($sqldeleteaccountpic2);

		  		if(file_exists('../images/profile/'.$rowaccountpic['accountPic'])){
		    		unlink('../images/profile/'.$rowaccountpic['accountPic']);
		  		}
			}  
		}

		$stmt = $conn->prepare("delete FROM `profile` WHERE profile.id = ?");
		$stmt->bind_param("i", $profile_id);
		$stmt->execute();
		$stmt->close();

		$sql_participant = 'SELECT participant.id AS participant_id FROM participant INNER JOIN profile ON profile.partitionId=participant.id WHERE profile.id='.$row_profile['id'].'';
		$result_participant = $conn->query($sql_participant);

		if($result_participant){
			if($result_participant->num_rows > 0){
		  		$row_participant = $result_participant->fetch_assoc();

				$sql_participant_location = 'SELECT location.id AS location_id, participant_location_sm.id AS participant_location_smId FROM participant INNER JOIN participant_location_sm ON participant_location_sm.participantId=participant.id INNER JOIN location ON location.id=participant_location_sm.locationId WHERE participant.id='.$row_participant['participant_id'].'';
				$result_participant_location = $conn->query($sql_participant_location);

				if($result_participant_location){
					if($result_participant_location->num_rows > 0){
				  		$row_participant_location = $result_participant_location->fetch_assoc();

				  		$sql_delete_participant_location = "DELETE FROM location WHERE location.id=".$row_participant_location['location_id']."";  
				  		$result_delete_participant_location = $conn->query($sql_delete_participant_location);

				  		$sql_delete_participant_location = "DELETE FROM participant_location_sm WHERE participant_location_sm.participantId=".$row_participant_location['participant_id']."";  
				  		$result_delete_participant_location = $conn->query($sql_delete_participant_location);
					}  
				}

		  		$sqldelete_participant = "DELETE FROM participant WHERE participant.id=".$row_participant['participant_id']."";  
		  		$resultDelete_participant = $conn->query($sqldelete_participant);
			}  
		}
	}
}


function deleteTeamById($team_id = $_GET['team_id']){

	global $connection_production;

	$prepare_team_id_query = "SELECT team.id AS team_id, team_image_sm.id AS team_image_sm_id, image.id AS image_id, location.id AS location_id, team_location_sm.id AS team_location_sm_id, alternate_name.id AS alternate_name_id, team_alternate_name_sm.id AS team_alternate_name_sm_id FROM `team` 
	LEFT JOIN team_image_sm ON team_image_sm.teamId=team.id
	LEFT JOIN image ON image.id=team_image_sm.imageId
	LEFT JOIN team_location_sm ON team_location_sm.teamId=team.id
	LEFT JOIN location ON locationId=team_location_sm.locationId
	LEFT JOIN team_alternate_name_sm ON team_alternate_name_sm.teamId=team.id
	LEFT JOIN alternate_name ON alternate_name.id=team_alternate_name_sm.alternateNameId
	WHERE team.id = ?";
	$stmt_team = $conn->prepare($prepare_team_id_query);
	$stmt_team->bind_param("i", $team_id);
	$stmt_team->execute();
	$result_team = $stmt_team->get_result();
	$stmt_team->close();

	if(!!$result_team && $result_team->num_rows > 0){
		$row_team = $result_team->fetch_assoc();


  		$sql_delete_team_image = "DELETE FROM image WHERE image.id=".$row_team['image_id']."";  
  		$result_delete_team_image = $conn->query($sql_delete_team_image);

  		$sql_delete_team_image_association = "DELETE FROM team_image_sm WHERE team_image_sm.teamId=".$row_team['team_id']."";  
  		$result_delete_team_image_association = $conn->query($sql_delete_team_image_association);

  		$sql_delete_team_location = "DELETE FROM location WHERE location.id=".$row_team['location_id']."";  
  		$result_delete_team_location = $conn->query($sql_delete_team_location);

  		$sql_delete_team_location_association = "DELETE FROM team_location_sm WHERE team_location_sm.teamId=".$row_team['team_id']."";  
  		$result_delete_team_location_association = $conn->query($sql_delete_team_location_association);

  		$sql_delete_team_alternate_name = "DELETE FROM alternate_name WHERE alternate_name.id=".$row_team['alternate_name_id']."";  
  		$result_delete_team_alternate_name = $conn->query($sql_delete_team_alternate_name);

  		$sql_delete_team_alternate_association = "DELETE FROM team_alternate_name_sm WHERE team_alternate_name_sm.teamId=".$row_team['team_id']."";  
  		$result_delete_team_alternate_association = $conn->query($sql_delete_team_alternate_association);

	}else{
		//team id not found
	}


	$prepare_team_franchise_query = "SELECT team.id AS team_id, franchise_team_sm.id AS franchise_team_sm_id, partition_franchise_sm.id AS partition_franchise_sm_id, franchise.id AS franchise_id FROM `franchise` 
	LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
	LEFT JOIN team ON team.id=franchise_team_sm.teamId
	LEFT JOIN partition_franchise_sm ON partition_franchise_sm.franchiseId=team.id
	WHERE team.id = ?";
	$stmt_team_franchise = $conn->prepare($prepare_team_franchise_query);
	$stmt_team_franchise->bind_param("i", $team_id);
	$stmt_team_franchise->execute();
	$result_team_franchise = $stmt_team_franchise->get_result();
	$stmt_team_franchise->close();

	if(!!$result_team_franchise && $result_team_franchise->num_rows > 0){
		$row_team_franchise = $result_team_franchise->fetch_assoc();

		$prepare_franchise_profile_query = "SELECT profile.id AS profile_id, profile_franchise_sm.id AS profile_franchise_sm_id FROM `profile` 
		LEFT JOIN profile_franchise_sm ON profile_franchise_sm.profileId=profile.id
		LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
		WHERE franchise.id = ?";
		$stmt_franchise_profile = $conn->prepare($prepare_franchise_profile_query);
		$stmt_franchise_profile->bind_param("i", $franchise_id);
		$stmt_franchise_profile->execute();
		$result_franchise_profile = $stmt_franchise_profile->get_result();
		$stmt_franchise_profile->close();

		if(!!$result_franchise_profile && $result_franchise_profile->num_rows > 0){
			while($row_franchise_profile = $result_franchise_profile->fetch_assoc()){

			deleteProfileById($row_franchise_profile['profile_id']);

            $sql_delete_partition_franchise = "DELETE FROM profile_franchise_sm WHERE id=?";

            $prepared_delete_partition_franchise = $connection_production->prepare($sql_delete_partition_franchise);
            $prepared_delete_partition_franchise->bind_param("i", $row_franchise_profile['profile_franchise_sm_id']);
            $result_delete_partition_franchise=$prepared_delete_partition_franchise->execute();
            $prepared_delete_partition_franchise->close();
			}
		}


        $sql_delete_partition_franchise = "DELETE FROM partition_franchise_sm WHERE franchiseId=?";

        $prepared_delete_partition_franchise = $connection_production->prepare($sql_delete_partition_franchise);
        $prepared_delete_partition_franchise->bind_param("i", $row_team_franchise['franchise_id']);
        $result_delete_partition_franchise=$prepared_delete_partition_franchise->execute();
        $prepared_delete_partition_franchise->close();

        $sql_delete_franchise_team = "DELETE FROM franchise_team_sm WHERE teamId=?";

        $prepared_delete_franchise_team = $connection_production->prepare($sql_delete_franchise_team);
        $prepared_delete_franchise_team->bind_param("i", $row_team_franchise['team_id']);
        $result_delete_franchise_team=$prepared_delete_franchise_team->execute();
        $prepared_delete_franchise_team->close();

        $sql_delete_franchise = "DELETE FROM franchise WHERE id=?";

        $prepared_delete_franchise = $connection_production->prepare($sql_delete_franchise);
        $prepared_delete_franchise->bind_param("i", $row_team_franchise['franchise_id']);
        $result_delete_franchise=$prepared_delete_franchise->execute();
        $prepared_delete_franchise->close();
	}
}


function deletePartitionById($partition_id){

	global $connection_production;

	$prepare_partition_ids = "SELECT DISTINCT partition_LP.id AS partition_LP_id entity_activity_sm_partition_sm.id AS entity_activity_sm_partition_sm_id, image.id AS image_id, partition_image_sm.id AS partition_image_sm_id, alternate_name.id AS alternate_name_id, partition_alternate_name_sm.id AS partition_alternate_name_sm_id  FROM `partition` AS partition_LP LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id LEFT JOIN partition_image_sm ON partition_LP.id=partition_image_sm.partitionId LEFT JOIN image ON partition_image_sm.imageId=image.id LEFT JOIN partition_alternate_name_sm ON partition_alternate_name_sm.partitionId=partition_LP.id LEFT JOIN alternate_name ON alternate_name.id=partition_alternate_name_sm.alternateNameId WHERE partition_LP.id=?";
	$stmt_partition = $conn->prepare($prepare_partition_ids);
	$stmt_partition->bind_param("i", $partition_id);
	$stmt_partition->execute();
	$result_partition = $stmt_partition->get_result();
	$stmt_partition->close();

	if(!!$result_partition && $result_partition->num_rows > 0){
		$row_partition = $result_partition->fetch_assoc();

  		$sql_delete_partition_image = "DELETE FROM image WHERE image.id=".$row_partition['image_id']."";  
  		$result_delete_partition_image = $conn->query($sql_delete_partition_image);

  		$sql_delete_partition_image_association = "DELETE FROM partition_image_sm WHERE partition_image_sm.partitionId=".$row_partition['partition_LP_id']."";  
  		$result_delete_partition_image_association = $conn->query($sql_delete_partition_image_association);

  		$sql_delete_e_a_sm_p_sm = "DELETE FROM entity_activity_sm_partition_sm WHERE entity_activity_sm_partition_sm.partitionId=".$row_partition['partition_LP_id']."";  
  		$result_e_a_sm_p_sm = $conn->query($sql_delete_e_a_sm_p_sm);

  		$sql_delete_partition_alternate_name = "DELETE FROM alternate_name WHERE alternate_name.id=".$row_partition['alternate_name_id']."";  
  		$result_delete_partition_alternate_name = $conn->query($sql_delete_partition_alternate_name);

  		$sql_delete_partition_alternate_association = "DELETE FROM partition_alternate_name_sm WHERE partition_alternate_name_sm.partitionId=".$row_partition['partition_LP_id']."";  
  		$result_delete_partition_alternate_association = $conn->query($sql_delete_partition_alternate_association);

		$prepare_team_ids = "SELECT DISTINCT team.id AS team_id FROM `team` LEFT JOIN franchise_team_sm ON franchise_team_sm.teamId=team.id LEFT JOIN partition_franchise_sm ON partition_franchise_sm.franchiseId=franchise_team_sm.franchiseId LEFT JOIN `partition` AS partition_LP ON partition_LP.id=partition_franchise_sm.partitionId WHERE partition_LP.id=?";
		$stmt_teams = $conn->prepare($row_partition['partition_LP_id']);
		$stmt_teams->bind_param("i", $teams_id);
		$stmt_teams->execute();
		$result_teams = $stmt_teams->get_result();
		$stmt_teams->close();

		if(!!$result_teams && $result_teams->num_rows > 0){
			while( $row_teams = mysqli_fetch_assoc($result_teams) ){

				deleteTeamById($row_teams['team_id']);

				$sql_delete_franchise_team_sm = "DELETE FROM franchise_team_sm WHERE franchise_team_sm.teamId=".$row_teams['team_id']."";  
				$result_delete_franchise_team_sm = $conn->query($sql_delete_franchise_team_sm);

				// $sql_delete_team = "DELETE FROM team WHERE team.id=".$row_teams['team_id']."";  
				// $result_delete_team = $conn->query($sql_delete_team);

			}
		}

  		$sql_delete_partition_franchise_sm = "DELETE FROM partition_franchise_sm WHERE partition_franchise_sm.partitionId=".$row_partition['partition_LP_id']."";  
  		$result_delete_partition_franchise_sm = $conn->query($sql_delete_partition_franchise_sm);

  		$sql_delete_partition = "DELETE FROM partition WHERE partition.id=".$row_partition['partition_LP_id']."";  
  		$result_delete_partition = $conn->query($sql_delete_partition);

	}else{
		//partition id not found
	}
}

function deleteLeagueById($league_id){

	global $connection_production;

    $get_entity_id = "SELECT entity.id AS entity_id, location.id AS location_id, alternate_name.id AS alternate_name_id FROM entity LEFT JOIN entity_location_sm ON entity_location_sm.id = entity.id LEFT JOIN location on location.id = entity_location_sm.locationId LEFT JOIN entity_alternate_name_sm ON entity_alternate_name_sm.id = entity.id LEFT JOIN alternate_name ON alternate_name.id=entity_alternate_name_sm.alternateNameId WHERE entity.id=?";
    $get_entity_id_stmt = $connection_production->prepare($get_entity_id);
    $get_entity_id_stmt->bind_param("i", $league_id);
    $get_entity_id_stmt->execute();
    $get_entity_id_result = $get_entity_id_stmt->get_result();
    $row_entity_id_result = $get_entity_id_result->fetch_assoc();
    $get_entity_id_stmt->close();
    $e_id = $row_entity_id_result['entity_id'];
    $location_id = $row_entity_id_result['location_id'];
    $alternate_name_id = $row_entity_id_result['alternate_name_id'];

    if (!!$e_id) {

    	$sql_delete_entity_location = "DELETE FROM location WHERE location.id=".$location_id;  
			$result_delete_entity_location = $conn->query($sql_delete_entity_location);

  		$sql_delete_entity_location_association = "DELETE FROM entity_location_sm WHERE entity_location_sm.entityId=".$e_id;  
  		$result_delete_entity_location_association = $conn->query($sql_delete_entity_location_association);

    	$sql_delete_entity_nickname = "DELETE FROM alternate_name WHERE alternate_name.id=".$alternate_name_id;  
			$result_delete_entity_nickname = $conn->query($sql_delete_entity_nickname);

  		$sql_delete_entity_nickname_association = "DELETE FROM entity_alternate_name_sm WHERE entity_alternate_name_sm.entityId=".$e_id;  
  		$result_delete_entity_nickname_association = $conn->query($sql_delete_entity_nickname_association);

		// find partition and cycle through to delete
        $get_partition_id = "SELECT entity_activity_sm_partition_sm.partitionId AS e_a_sm_p_id FROM entity_activity_sm_partition_sm LEFT JOIN entity_activity_sm ON entity_activity_sm.id = entity_activity_sm_partition_sm.entityActivitySmId WHERE entity_activity_sm.entityId=".$e_id;
		$select_all_partitions = mysqli_query($connection_production, $get_partition_id);

        // start loop
        while($row_partition = mysqli_fetch_assoc($select_all_partitions)){
        	deletePartitionById($row_partition['e_a_sm_p_id']);
        }

        $sql_delete_entity_activity_sm = "DELETE FROM entity_activity_sm WHERE entity_activity_sm.entityId=".$e_id; 
        $result_delete_entity_activity_sm = mysqli_query($connection_production, $sql_delete_entity_activity_sm);

        $sql_delete_entity = "DELETE FROM entity WHERE entity.id=".$e_id;
        $result_delete_entity = mysqli_query($connection_production, $sql_delete_entity); 

	}else{
		//entity not found
	}
}


function deleteActivityCascadeDeleteAll($activity_id){

	global $connection_production;

    $get_entity_ids = "SELECT entity.id AS entity_id FROM entity LEFT JOIN entity_activity_sm ON entity_activity_sm.id = entity.id WHERE entity_activity_sm.activityId=?";
    $get_entity_ids_stmt = $connection_production->prepare($get_entity_ids);
    $get_entity_ids_stmt->bind_param("i", $activity_id);
    $get_entity_ids_stmt->execute();
    $get_entity_ids_result = $get_entity_ids_stmt->get_result();
    $get_entity_ids_stmt->close();

    if(!!$get_entity_ids_result && $get_entity_ids_result->num_rows > 0 ){	
	    while($row_entity_ids_result = $get_entity_ids_result->fetch_assoc()){
		    $e_id = $row_entity_ids_result['entity_id'];
	    	deleteLeagueById($e_id);
	    }
    }
}










?>