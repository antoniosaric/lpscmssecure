<form method='get'>
	<input type='hidden' name='source' value='images' />
	<input type='text' name='search' placeholder="Search images" required pattern='.*\S+.*' />
	<input class="btn btn-primary" type="submit" value="Search" />
</form>
<?php
    $search_query = isset($_GET['search']) ? str_replace("+", " ", $_GET['search']) : "";
    if (isset($_GET['search'])) {
        echo "<h2>Search Results for \"".$search_query."\"</h2>";
    }
?>
<table class="table table-striped table-bordered table-hover">
	<?php deleteImageFile(); ?>
    <thead>
        <tr>
            <th class='col-md-1'>Image ID</th>
            <th>Image Name</th>
            <th class='col-md-2'>Image Type</th>
            <th class='col-md-2'>Linked To</th>
            <th class='col-md-1'>Linked ID</th>
            <th class='col-md-1'>Update</th>
            <th class='col-md-1'>Delete</th>
        </tr>
    </thead>
    <tbody>
		<?php
			$query_filter = "";
			$words = preg_split('/\s+/', $search_query, -1, PREG_SPLIT_NO_EMPTY);
			if ($search_query != "") {
				$query_filter .= "WHERE";
				$key_count = 0;
				foreach ($words as $key) {
					if ($key_count > 0) {
						$query_filter .= " AND";
					}
					$linked_search = array();
					if (strpos("activity", $key) !== false) {
						array_push($linked_search, "activity_image_sm.activityId IS NOT NULL OR");
					}
					if (strpos("entity_activity_sm", $key) !== false) {
						array_push($linked_search, "entity_activity_sm_image_sm.entityActivitySmId IS NOT NULL OR");
					}
					if (strpos("entity", $key) !== false) {
						array_push($linked_search, "entity_image_sm.entityId IS NOT NULL OR");
					}
					if (strpos("institution", $key) !== false) {
						array_push($linked_search, "institution_image_sm.institutionId IS NOT NULL OR");
					}
					if (strpos("participant", $key) !== false) {
						array_push($linked_search, "participant_image_sm.participantId IS NOT NULL OR");
					}
					if (strpos("partition", $key) !== false) {
						array_push($linked_search, "partition_image_sm.partitionId IS NOT NULL OR");
					}
					if (strpos("profile_activity_sm", $key) !== false) {
						array_push($linked_search, "profile_activity_sm_image_sm.profileActivitySmId IS NOT NULL OR");
					}
					if (strpos("profile_franchise_sm", $key) !== false) {
						array_push($linked_search, "profile_franchise_sm_image_sm.profileFranchiseSmId IS NOT NULL OR");
					}
					if (strpos("profile_entity_activity_sm_partition_sm", $key) !== false) {
						array_push($linked_search, "profile_entity_activity_sm_partition_sm_sm_image_sm.profileEntityActivitySmPartitionSmSmId IS NOT NULL OR");
					}
					if (strpos("profile", $key) !== false) {
						array_push($linked_search, "profile_image_sm.profileId IS NOT NULL OR");
					}
					if (strpos("profile_partition_sm", $key) !== false) {
						array_push($linked_search, "profile_partition_sm_image_sm.profilePartitionSmId IS NOT NULL OR");
					}
					if (strpos("profile_team_sm", $key) !== false) {
						array_push($linked_search, "profile_team_sm_image_sm.profileTeamSmId IS NOT NULL OR");
					}
					if (strpos("school", $key) !== false) {
						array_push($linked_search, "school_image_sm.schoolId IS NOT NULL OR");
					}
					if (strpos("team", $key) !== false) {
						array_push($linked_search, "team_image_sm.teamId IS NOT NULL OR");
					}
					$query_filter .= " (".implode(" ", $linked_search)." image.imageName LIKE ? OR image_type.type LIKE ? OR image.id = ?)";
					$key_count++;
				}
			}
			$query_filter .= " ORDER BY image.id DESC";

			$image_count_query = "SELECT image.id AS image_id, image.imageName AS image_name, image_type.type AS image_type,
				activity_image_sm.activityId AS activity_id, entity_activity_sm_image_sm.entityActivitySmId AS e_a_sm_id,
				entity_image_sm.entityId AS entity_id, institution_image_sm.institutionId AS institution_id,
				participant_image_sm.participantId AS participant_id, partition_image_sm.partitionId AS partition_id,
				profile_activity_sm_image_sm.profileActivitySmId AS p_a_sm_id, profile_franchise_sm_image_sm.profileFranchiseSmId AS p_f_sm_id,
				profile_entity_activity_sm_partition_sm_sm_image_sm.profileEntityActivitySmPartitionSmSmId AS p_e_a_sm_p_sm_id,
				profile_image_sm.profileId AS profile_id, profile_partition_sm_image_sm.profilePartitionSmId AS p_p_sm_id,
				profile_team_sm_image_sm.profileTeamSmId AS p_t_sm_id, school_image_sm.schoolId AS school_id,
				team_image_sm.teamId AS team_id FROM image
				LEFT JOIN image_type ON image_type.id = image.imageTypeId
				LEFT JOIN activity_image_sm ON activity_image_sm.imageId = image.id
				LEFT JOIN entity_activity_sm_image_sm ON entity_activity_sm_image_sm.imageId = image.id
				LEFT JOIN entity_image_sm ON entity_image_sm.imageId = image.id
				LEFT JOIN institution_image_sm ON institution_image_sm.imageId = image.id
				LEFT JOIN participant_image_sm ON participant_image_sm.imageId = image.id
				LEFT JOIN partition_image_sm ON partition_image_sm.imageId = image.id
				LEFT JOIN profile_activity_sm_image_sm ON profile_activity_sm_image_sm.imageId = image.id
				LEFT JOIN profile_entity_activity_sm_partition_sm_sm_image_sm ON profile_entity_activity_sm_partition_sm_sm_image_sm.imageId = image.id
				LEFT JOIN profile_franchise_sm_image_sm ON profile_franchise_sm_image_sm.imageId = image.id
				LEFT JOIN profile_image_sm ON profile_image_sm.imageId = image.id
				LEFT JOIN profile_partition_sm_image_sm ON profile_partition_sm_image_sm.imageId = image.id
				LEFT JOIN profile_team_sm_image_sm ON profile_team_sm_image_sm.imageId = image.id
				LEFT JOIN school_image_sm ON school_image_sm.imageId = image.id
				LEFT JOIN team_image_sm ON team_image_sm.imageId = image.id ";
			$image_count_query .= $query_filter;
			$stmt = $connection_production->prepare($image_count_query);

			if ($search_query != "") {
				$bind_parameters = array();
				$bind_parameters[0] = "";

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
			$images_find_count = $stmt->get_result();
			$stmt->close();
			$count = mysqli_num_rows($images_find_count);
			$count = ceil($count/20);

			if (isset($_GET['pageinate'])) {
				$pageinate = $_GET['pageinate'];
			} else {
				$pageinate = "";
			}
			if ($pageinate == "" || $pageinate == 1) {
				$pageinate_1 = 0;
			} else {
				$pageinate_1 = ($pageinate * 20) - 20;
			}
			findAllImages($query_filter, $pageinate_1, $words);
		?>
    </tobdy>
</table>
<?php
    echo "<ul class='pager'>";
        $search_param = isset($_GET['search']) ? "&search=".$search_query : "";
        for ($i = 1; $i <= $count; $i++) {
           if ($i == $pageinate || $pageinate == '0') {
                echo "<li><a class='active_link' href='categories.php?source=images&pageinate=".$i.$search_param."'>".$i."</a></li>";
            } else {
                echo "<li><a href='categories.php?source=images&pageinate=".$i.$search_param."'>".$i."</a></li>";
            }
        }
    echo "</ul>";
?>
