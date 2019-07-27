<?php

//======================================================================
// FIND ALL DATABASE ERRORS FOR VIDEOS
//======================================================================

	function findAllDatabaseErrorsVideos(){
		global $connection_production;
        //videos
	    $sqlvideosteam = "SELECT DISTINCT video.id AS videoId, video.reference AS reference, video.title AS title, video.summary AS videoSummary, profile.id AS videoProfileId, video.videoStatus AS videoStatus, video.thumbString AS thumbString, video.videoSourceId AS videoSourceId, video_source.source AS videoSource, team.id AS teamId FROM video 
    		LEFT JOIN video_source ON video_source.id = video.videoSourceId
    		LEFT JOIN profile_franchise_sm_video_sm ON video.id=profile_franchise_sm_video_sm.videoId 
    		LEFT JOIN profile_franchise_sm ON profile_franchise_sm_video_sm.profileFranchiseSmId=profile_franchise_sm.id 
    		LEFT JOIN profile ON profile_franchise_sm.profileId=profile.id
    		LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
    		LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
    		LEFT JOIN team ON team.id=franchise_team_sm.teamId WHERE profile.mainProfileType=0"; 

        $select_all_video_error_query = mysqli_query($connection_production, $sqlvideosteam);
	    if(mysqli_num_rows($select_all_video_error_query) > 0){ 
        	echo '<h3>Video Team Association</h3>' ;
			while($row = mysqli_fetch_assoc($select_all_video_error_query)){
				$error_log = array();
				if(!$row['reference'] || !$row['title'] || !$row['videoSummary'] || !$row['videoProfileId'] || !$row['videoStatus'] != 'complete' || !$row['thumbString'] || !$row['videoSource'] || !$row['teamId']){  
					!isset($row['reference']) ? array_push($error_log, 'no reference') : NULL;
					!isset($row['title']) ? array_push($error_log, 'no title') : NULL;
					!isset($row['videoSummary']) ? array_push($error_log, 'no video summary') : NULL;
					!isset($row['videoProfileId']) ? array_push($error_log, 'no profile association') : NULL;
					!isset($row['videoStatus']) ? array_push($error_log, 'status not complete') : NULL;
					!isset($row['thumbString']) ? array_push($error_log, 'no thumbString') : NULL;
					!isset($row['videoSource']) ? array_push($error_log, 'no video source') : NULL;
					!isset($row['teamId']) ? array_push($error_log, 'no team association') : NULL;
				    echo "<tr>";
				    echo "<td>".$row['videoId']."</td>";
				    echo "<td>Video</td>";
				    echo "<td>missing: ".implode(", ",$error_log)."</td>";
				    // echo "<td><a class='btn btn-info' href='categories.php?source=profile_update_athlete&profile_id=".$row['video_id']."'>Edit</a></td>";
				}
			}
	    }

	    $sqlvideossport = "SELECT DISTINCT video.id AS videoId, video.reference AS reference, video.title AS title, video.summary AS videoSummary, profile.id AS videoProfileId, video.videoStatus AS videoStatus, video.thumbString AS thumbString, video.videoSourceId AS videoSourceId, video_source.source AS videoSource, activity.id AS activityId FROM video LEFT JOIN video_source ON video_source.id = video.videoSourceId LEFT JOIN profile_activity_sm_video_sm ON video.id=profile_activity_sm_video_sm.videoId LEFT JOIN profile_activity_sm ON profile_activity_sm_video_sm.profileActivitySmId=profile_activity_sm.id LEFT JOIN profile ON profile_activity_sm.profileId = profile.id LEFT JOIN activity ON activity.id = profile_activity_sm.activityId WHERE profile.mainProfileType=0";
	            
	    $select_all_video_2__error_query = mysqli_query($connection_production, $sqlvideossport);
	    if(mysqli_num_rows($select_all_video_2__error_query) > 0){ 
        	echo '<h3>Video Solo Sport Association</h3>' ;
			while($row = mysqli_fetch_assoc($select_all_video_2__error_query)){
				$error_log = array();
				if(!$row['reference'] || !$row['title'] || !$row['videoSummary'] || !$row['videoProfileId'] || !$row['videoStatus'] != 'complete' || !$row['thumbString'] || !$row['videoSource'] || !$row['activityId']){  
					!isset($row['reference']) ? array_push($error_log, 'no reference') : NULL;
					!isset($row['title']) ? array_push($error_log, 'no title') : NULL;
					!isset($row['videoSummary']) ? array_push($error_log, 'no video summary') : NULL;
					!isset($row['videoProfileId']) ? array_push($error_log, 'no profile association') : NULL;
					!isset($row['videoStatus']) ? array_push($error_log, 'status not complete') : NULL;
					!isset($row['thumbString']) ? array_push($error_log, 'no thumbString') : NULL;
					!isset($row['videoSource']) ? array_push($error_log, 'no video source') : NULL;
					!isset($row['activityId']) ? array_push($error_log, 'no activity association') : NULL;
				    echo "<tr>";
				    echo "<td>".$row['videoId']."</td>";
				    echo "<td>Video</td>";
				    echo "<td>missing: ".implode(", ",$error_log)."</td>";
				}
			}
	    }  

	    $sqlvideosparticipant = "SELECT DISTINCT video.id AS videoId, video.reference AS reference, video.title AS title, video.summary AS videoSummary, profile.id AS videoProfileId, video.videoStatus AS videoStatus, video.thumbString AS thumbString, video.videoSourceId AS videoSourceId, video_source.source AS videoSource, profile.id AS profileId FROM video LEFT JOIN video_source ON video_source.id = video.videoSourceId LEFT JOIN profile_video_sm ON video.id=profile_video_sm.videoId LEFT JOIN profile ON profile_video_sm.profileId=profile.id WHERE profile.mainProfileType=0";

	    $select_all_video_3__error_query = mysqli_query($connection_production, $sqlvideossport);
	    if(mysqli_num_rows($select_all_video_3__error_query) > 0){ 
        	echo '<h3>Video No Team/Sport Association</h3>' ;
			while($row = mysqli_fetch_assoc($select_all_video_3__error_query)){
				$error_log = array();
				if(!$row['reference'] || !$row['title'] || !$row['videoSummary'] || !$row['videoProfileId'] || !$row['videoStatus'] != 'complete' || !$row['thumbString'] || !$row['videoSource'] || !$row['profileId']){  
					!isset($row['reference']) ? array_push($error_log, 'no reference') : NULL;
					!isset($row['title']) ? array_push($error_log, 'no title') : NULL;
					!isset($row['videoSummary']) ? array_push($error_log, 'no video summary') : NULL;
					!isset($row['videoProfileId']) ? array_push($error_log, 'no profile association') : NULL;
					!isset($row['videoStatus']) ? array_push($error_log, 'status not complete') : NULL;
					!isset($row['thumbString']) ? array_push($error_log, 'no thumbString') : NULL;
					!isset($row['videoSource']) ? array_push($error_log, 'no video source') : NULL;
					!isset($row['profileId']) ? array_push($error_log, 'no profile association') : NULL;
				    echo "<tr>";
				    echo "<td>".$row['videoId']."</td>";
				    echo "<td>Video</td>";
				    echo "<td>missing: ".implode(", ",$error_log)."</td>";
				}
			}
	    }
	}	

	// function findAllDatabaseErrorsVideosImages(){

	// 	global $connection_production;

	// 	//activity
 //        $query = "SELECT id AS video_id, thumbString AS thumbString FROM video WHERE thumbString IS NULL OR thumbString = '' ORDER BY id DESC";
 //        $select_all_video_error_query = mysqli_query($connection_production, $query);

 //        // start loop
 //        if(mysqli_num_rows($select_all_video_error_query) > 0){ 
 //        	echo '<h3>Video Image Error</h3>' ;
	//         while($row = mysqli_fetch_assoc($select_all_video_error_query)){
	//         	$error_log = array(); 
 //        		array_push($error_log, 'no thumbString');
	//             echo "<tr>";
	//             echo "<td>".$row['video_id']."</td>";
	//             echo "<td>video</td>";
	//             echo "<td>missing: ".implode(", ",$error_log)."</td>";
	//         }     
 //        }
 //    }

//======================================================================
// FIND ALL DATABASE ERRORS FOR ACTIVITY IMAGE
//======================================================================

	function findAllDatabaseErrorsActivityImage(){

		global $connection_production;

		//activity
        $query = "SELECT activity.id AS activity_id, activity.activity AS activity, activity.description AS description, image.imageName AS imageName From activity LEFT JOIN activity_image_sm ON activity_image_sm.activityId = activity.id LEFT JOIN image On image.id=activity_image_sm.imageId";
        $select_all_activity_error_query = mysqli_query($connection_production, $query);

        // start loop
        if(mysqli_num_rows($select_all_activity_error_query) > 0){ 
        	echo '<h3>Activity</h3>' ;
	        while($row = mysqli_fetch_assoc($select_all_activity_error_query)){
	        	$error_log = array();
	        	if(!$row['activity'] || !$row['description'] || !$row['imageName']){  
	        		!isset($row['activity']) ? array_push($error_log, 'no activity') : NULL;
	        		!isset($row['description']) ? array_push($error_log, 'no description') : NULL;
	        		!isset($row['imageName']) ? array_push($error_log, 'no imageName') : NULL;
		            echo "<tr>";
		            echo "<td>".$row['activity_id']."</td>";
		            echo "<td>Activity</td>";
		            echo "<td>missing: ".implode(", ",$error_log)."</td>";
	        	}
	        }     
        }
    }

//======================================================================
// FIND ALL DATABASE ERRORS FOR PROFILES
//======================================================================

	function findAllDatabaseErrorsProfiles(){
		global $connection_production;
        //videos
	    $sql_profile_errors = "SELECT profile.id AS profile_id, participant.firstName AS first_name, participant.middle AS middle, participant.lastName AS last_name, participant.birthdate AS birthdate, participant.gender AS gender, profile.summary AS summary, profile.acclaim AS acclaim, profile.mainProfileType AS mainProfileType, profile.profileTypeId AS profileTypeId, profile.status AS status FROM participant LEFT JOIN profile ON profile.participantId = participant.id WHERE profile.mainProfileType=0 ORDER BY profile.id DESC"; 

        $select_all_profile_errors = mysqli_query($connection_production, $sql_profile_errors);
	    if(mysqli_num_rows($select_all_profile_errors) > 0){ 
        	echo '<h3>Profile Errors</h3>' ;
			while($row = mysqli_fetch_assoc($select_all_profile_errors)){
				$error_log = array();
				if(!$row['summary'] || !$row['acclaim'] || !$row['first_name'] || $row['profileTypeId'] == 0 || $row['status'] != 'complete' ){  
					!isset($row['summary']) ? array_push($error_log, 'no summary') : NULL;
					!isset($row['acclaim']) ? array_push($error_log, 'no acclaim') : NULL;
					!isset($row['first_name']) ? array_push($error_log, 'no first_name') : NULL;
					$row['profileTypeId'] == 0 ? array_push($error_log, 'profile type not set') : NULL;
					$row['status'] != 'complete' ? array_push($error_log, 'profile status is not complete') : NULL;
				    echo "<tr>";
				    echo "<td>".$row['profile_id']."</td>";
				    echo "<td>Profile</td>";
				    echo "<td>missing: ".implode(", ",$error_log)."</td>";
				    echo "<td><a class='btn btn-info' href='categories.php?source=profile_update_athlete&profile_id=".$row['profile_id']."'>Edit</a></td>";
				}
			}
	    }
	}

//======================================================================
// FIND ALL DATABASE ERRORS FOR PROFILE IMAGE
//======================================================================

	function findAllDatabaseErrorsProfileImage(){
		global $connection_production;
        //videos
	    $sql_profile_image_errors = "SELECT profile.id AS profile_id, image.id AS image_id, image.imageName AS imageName FROM image LEFT JOIN profile_image_sm ON profile_image_sm.imageId = image.id LEFT JOIN profile ON profile_image_sm.profileId = profile.id WHERE profile.mainProfileType=0 ORDER BY profile.id DESC"; 

        $select_all_profile_image_errors = mysqli_query($connection_production, $sql_profile_image_errors);
	    if(mysqli_num_rows($select_all_profile_image_errors) > 0){ 
        	echo '<h3>Profile Image Errors</h3>' ;
			while($row = mysqli_fetch_assoc($select_all_profile_image_errors)){
				$error_log = array();
				if(!$row['imageName'] ){  
					!isset($row['imageName']) ? array_push($error_log, 'no image') : NULL;
				    echo "<tr>";
				    echo "<td>".$row['profile_id']."</td>";
				    echo "<td>Profile</td>";
				    echo "<td>missing: ".implode(", ",$error_log)."</td>";
				}
			}
	    }
	}

//======================================================================
// FIND ALL DATABASE ERRORS FOR TEAM IMAGE
//======================================================================

	function findAllDatabaseErrorsTeamImage(){
		global $connection_production;
        //videos
	    $sql_profile_errors = "SELECT team.id AS team_id, image.id AS image_id, image.imageName AS imageName FROM image LEFT JOIN team_image_sm ON team_image_sm.imageId = image.id LEFT JOIN team ON team_image_sm.teamId = team.id WHERE team.mainteamType=0 ORDER BY team.id DESC"; 

        $select_all_profile_errors = mysqli_query($connection_production, $sql_profile_errors);
	    if(mysqli_num_rows($select_all_profile_errors) > 0){ 
        	echo '<h3>Team Image Errors</h3>' ;
			while($row = mysqli_fetch_assoc($select_all_profile_errors)){
				$error_log = array();
				if(!$row['thumbString'] ){  
					!isset($row['thumbString']) ? array_push($error_log, 'no thumbString') : NULL;
				    echo "<tr>";
				    echo "<td>".$row['teamId']."</td>";
				    echo "<td>Team</td>";
				    echo "<td>missing: ".implode(", ",$error_log)."</td>";
				}
			}
	    }
	}

//======================================================================
// FIND ALL DATABASE ERRORS FOR TEAMS
//======================================================================

	function findAllDatabaseErrorsTeams(){
		global $connection_production;
        //videos
	    $sql_team_errors = "SELECT team.id AS team_id, team.name AS name, team.locale AS locale, team.description AS description, team.status AS status FROM team ORDER BY team.id DESC"; 

        $select_all_team_errors = mysqli_query($connection_production, $sql_team_errors);
	    if(mysqli_num_rows($select_all_team_errors) > 0){ 
        	echo '<h3>Team Errors</h3>' ;
			while($row = mysqli_fetch_assoc($select_all_team_errors)){
				$error_log = array();
				if(!$row['name'] || !$row['locale'] || !$row['description'] || $row['status'] != 'complete' ){  
					!isset($row['name']) ? array_push($error_log, 'no team name') : NULL;
					!isset($row['locale']) ? array_push($error_log, 'no team locale') : NULL;
					!isset($row['description']) ? array_push($error_log, 'no description') : NULL;
					$row['status'] != 'complete' ? array_push($error_log, 'team status is not complete') : NULL;
				    echo "<tr>";
				    echo "<td>".$row['team_id']."</td>";
				    echo "<td>Team</td>";
				    echo "<td>missing: ".implode(", ",$error_log)."</td>";
				    echo "<td><a class='btn btn-info' href='categories.php?source=profile_update_team&team_id=".$row['team_id']."'>Edit</a></td>";
				}
			}
	    }
	}

//======================================================================
// FIND ALL DATABASE ERRORS FOR LEAGUES
//======================================================================

	function findAllDatabaseErrorsLeagues(){
		global $connection_production;
        //videos
	    $sql_entity_errors = "SELECT entity.id AS entity_id, entity.name AS name, entity.description AS description FROM entity ORDER BY entity.id DESC"; 

        $select_all_entity_errors = mysqli_query($connection_production, $sql_entity_errors);
	    if(mysqli_num_rows($select_all_entity_errors) > 0){ 
        	echo '<h3>Entity Errors</h3>' ;
			while($row = mysqli_fetch_assoc($select_all_entity_errors)){
				$error_log = array();
				if(!$row['name'] || !$row['description'] ){  
					!isset($row['name']) ? array_push($error_log, 'no entity name') : NULL;
					!isset($row['description']) ? array_push($error_log, 'no description') : NULL;
				    echo "<tr>";
				    echo "<td>".$row['entity_id']."</td>";
				    echo "<td>entity</td>";
				    echo "<td>missing: ".implode(", ",$error_log)."</td>";
				    echo "<td><a class='btn btn-info' href='categories.php?source=profile_update_entity&entity_id=".$row['entity_id']."'>Edit</a></td>";
				}
			}
	    }
	}

//======================================================================
// FIND ALL DATABASE ERRORS FOR ENTITY IMAGE
//======================================================================

	function findAllDatabaseErrorsEntityImage(){
		global $connection_production;
        //videos
	    $sql_entity_image_errors = "SELECT entity.id AS entity_id, image.id AS image_id, image.imageName AS imageName, entity_image_sm.id AS entity_image_sm_id FROM entity LEFT JOIN entity_image_sm ON entity_image_sm.entityId = entity.id LEFT JOIN image ON entity_image_sm.imageId = image.id WHERE entity.id < 6 OR entity.id > 20 ORDER BY entity.id DESC"; 
        $select_all_entity_image_errors = mysqli_query($connection_production, $sql_entity_image_errors);
	    if(mysqli_num_rows($select_all_entity_image_errors) > 0){ 
        	echo '<h3>Entity Image Errors</h3>' ;
			while($row = mysqli_fetch_assoc($select_all_entity_image_errors)){
				$error_log = array();
				if(!isset($row['imageName']) ){  
					!isset($row['imageName']) ? array_push($error_log, 'no image') : NULL;
				    echo "<tr>";
				    echo "<td>".$row['entity_id']."</td>";
				    echo "<td>Entity</td>";
				    echo "<td>missing: ".implode(", ",$error_log)."</td>";
				}
			}
	    }
	}



?>
