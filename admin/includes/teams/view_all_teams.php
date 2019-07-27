<form method='get'>
	<input type='hidden' name='source' value='teams' />
	<input type='text' name='search' placeholder="Search teams" required pattern='.*\S+.*' />
	<input class="btn btn-primary" type="submit" value="Search" />
</form>
<?php
    $search_query = isset($_GET['search']) ? str_replace("+", " ", $_GET['search']) : "";
    if (isset($_GET['search'])) {
        echo "<h2>Search Results for \"".$search_query."\"</h2>";
    }
?>
<table class="table table-striped table-bordered table-hover">
    <?php deleteTeam(); ?>
    <thead>
        <tr>
            <th class='col-md-1'>ID</th>
            <th>Activity</th>
            <th>Image</th>
            <th>Locale | Name</th>
            <th>Description</th>
            <th>Location</th>
            <th class='col-md-1'>Edit</th>
            <th class='col-md-1'>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $query_filter = "WHERE team.status ='complete'";
			$words = preg_split('/\s+/', $search_query, -1, PREG_SPLIT_NO_EMPTY);
            if ($search_query != "") {
                foreach ($words as $key) {
                    $query_filter .= " AND (team.name LIKE ? OR team.locale LIKE ? OR team.id = ?)";
                }
            }
            $query_filter .= " ORDER BY team.id DESC";

            $team_count_query = "SELECT team.name, team.locale, team.id FROM team ";
            $team_count_query .= $query_filter;
			$stmt = $connection_production->prepare($team_count_query);

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
            $teams_find_count = $stmt->get_result();
			$stmt->close();
            $count = mysqli_num_rows($teams_find_count);
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
            findAllTeams($query_filter, $pageinate_1, $words);
        ?>
    </tbody>
</table>
<?php
    echo "<ul class='pager'>";
        $search_param = isset($_GET['search']) ? "&search=".$search_query : "";
        for ($i = 1; $i <= $count; $i++) {
           if ($i == $pageinate || $pageinate == '0') {
                echo "<li><a class='active_link' href='categories.php?source=teams&pageinate=".$i.$search_param."'>".$i."</a></li>";
            } else {
                echo "<li><a href='categories.php?source=teams&pageinate=".$i.$search_param."'>".$i."</a></li>";
            }
        }
    echo "</ul>";
?>
