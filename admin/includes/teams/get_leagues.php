<?php 

if(isset($_POST['league_id_for_partition'])){
global $connection_production;
	$league_id_for_partition = (int)$_POST['league_id_for_partition'];

	    echo "<div class='form-group col-xs-2'>";
            echo "<label for='team_partition'>Partition</label>";
            echo "<select name='team_partition' id='team_partition'>";
                echo "<option value=''>choose partition</option>";
 
    
                    $team_partition_query_ppartition = "SELECT partition_LP.id AS partition_id, partition_LP.partition AS partition_name FROM `partition` AS partition_LP 
                    LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id
                    LEFT JOIN entity_activity_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
                    LEFT JOIN entity ON entity.id=entity_activity_sm.entityId WHERE entity.id=".$league_id_for_partition;
                    $all_team_partition_query = mysqli_query($connection_production, $team_partition_query_ppartition);
                    while($row = mysqli_fetch_assoc($all_team_partition_query)){
                        echo "<option value=".$row['partition_id'].">".$row['partition_name']."</option>";
                    }

            echo "</select>";
        echo "</div>";  


	// $team_entity_partition_query = "SELECT partition_LP.id AS partition_id, partition_LP.partition AS partition_name FROM `partition` AS partition_LP 
 //    LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id
 //    LEFT JOIN entity_activity_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
 //    LEFT JOIN entity ON entity.id=entity_activity_sm.entityId WHERE entity.id=".$league_id;
 //    $all_team_partition_query = mysqli_query($connection_production, $team_partition_query);



	// $prepared_partition_query = $connection_production->prepare($team_entity_partition_query);
	// $prepared_partition_query->bind_param("i", $league_id);
	// $result_partition_query=$prepared_partition_query->execute();
	// $prepared_partition_query->close();
	// echo json_encode($result_partition_query->fetch_all(MYSQLI_ASSOC));
}

// if(isset($_POST['league_id_for_partition_set'])){
// 	$league_id_for_partition_dropdown = (int)$_POST['league_id_for_partition_set'];
// }


?>