<form method='get'>
	<input type='hidden' name='source' value='profiles' />
	<input type='text' name='search' placeholder="Search profiles" required pattern='.*\S+.*' />
	<input class="btn btn-primary" type="submit" value="Search" />
</form>
<?php
    $search_query = isset($_GET['search']) ? str_replace("+", " ", $_GET['search']) : "";
    if (isset($_GET['search'])) {
        echo "<h2>Search Results for \"".$search_query."\"</h2>";
    }
?>
<table class="table table-striped table-bordered table-hover">
    <?php deleteProfile(); ?>
    <thead>
        <tr>
            <th>ID</th>
            <th>First name</th>
            <th>Middle name</th>
            <th>Last name</th>
            <th>Nickname (athlete) or<br>Specialty (coach)</th>
            <th>Suffix</th>
            <th>Summary</th>
            <th>Acclaim</th>
            <th>Has location</th>
            <th>Has image</th>
            <th>Profile type</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $query_filter = "WHERE profile.mainProfileType = 0";
			$words = preg_split('/\s+/', $search_query, -1, PREG_SPLIT_NO_EMPTY);
            if ($search_query != "") {
                foreach ($words as $key) {
                    $query_filter .= " AND (participant.firstName LIKE ? OR participant.middle LIKE ?
                        OR participant.lastName LIKE ? OR profile.id = ?)";
                }
            }
            $query_filter .= " ORDER BY profile.id DESC";

            $profile_count_query = "SELECT DISTINCT profile.id, participant.firstName,
                participant.middle, participant.lastName FROM participant
                LEFT JOIN profile ON profile.participantId = participant.id ";
            $profile_count_query .= $query_filter;
			$stmt = $connection_production->prepare($profile_count_query);

			if ($search_query != "") {
				$bind_parameters = array();
				$bind_parameters[0] = "";

				foreach ($words as $key) {
					$bind_parameters[0] = $bind_parameters[0]."sssi";
					$format_param = '%'.$key.'%';
					array_push($bind_parameters, $format_param);
					array_push($bind_parameters, $format_param);
					array_push($bind_parameters, $format_param);
					array_push($bind_parameters, $key);
				}
				call_user_func_array(array($stmt, "bind_param"), refValues($bind_parameters));
			}
			$stmt->execute();
            $profile_find_count = $stmt->get_result();
			$stmt->close();
            $count = mysqli_num_rows($profile_find_count);
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
            findAllProfiles($query_filter, $pageinate_1, $words);
        ?>
    </tbody>
</table>
<?php
    echo "<ul class='pager'>";
        $search_param = isset($_GET['search']) ? "&search=".$search_query : "";
        for ($i = 1; $i <= $count; $i++) {
           if ($i == $pageinate || $pageinate == '0') {
                echo "<li><a class='active_link' href='categories.php?source=profiles&pageinate=".$i.$search_param."'>".$i."</a></li>";
            } else {
                echo "<li><a href='categories.php?source=profiles&pageinate=".$i.$search_param."'>".$i."</a></li>";
            }
        }
    echo "</ul>";
?>
