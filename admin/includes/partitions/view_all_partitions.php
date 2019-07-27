<form method='get'>
	<input type='hidden' name='source' value='partitions' />
	<input type='text' name='search' placeholder="Search partitions" required pattern='.*\S+.*' />
	<input class="btn btn-primary" type="submit" value="Search" />
</form>
<?php
    $search_query = isset($_GET['search']) ? str_replace("+", " ", $_GET['search']) : "";
    if (isset($_GET['search'])) {
        echo "<h2>Search Results for \"".$search_query."\"</h2>";
    }
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th class='col-md-1'>ID</th>
            <th class='col-md-2'>Partition name</th>
            <th>Description</th>
            <th class='col-md-1'>Type</th>
            <th class='col-md-1'>Edit</th>
            <th class='col-md-1'>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php
			$words = preg_split('/\s+/', $search_query, -1, PREG_SPLIT_NO_EMPTY);
			$query_filter = "WHERE NOT (partition_LP.partition LIKE '%no%' AND partition_LP.partition LIKE '%partition%' AND partition_LP.partition LIKE '%association%')";
            if ($search_query != "") {
                foreach ($words as $key) {
                    $query_filter .= " AND (partition_LP.partition LIKE ? OR partition_LP.description LIKE ? OR partition_LP.id = ?)";
                }
            }
            $query_filter .= " ORDER BY partition_LP.id DESC";

			$partition_count_query = "SELECT DISTINCT partition_LP.id AS partition_id, partition_LP.partition AS partition_name,
				partition_LP.description AS partition_description, partition_LP.partitionTypeId AS type_id FROM `partition` AS partition_LP
				LEFT JOIN partition_type ON partition_type.id=partition_LP.partitionTypeId ";
			$partition_count_query .= $query_filter;
			$stmt = $connection_production->prepare($partition_count_query);

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
			$partition_find_count = $stmt->get_result();
			$stmt->close();
			$count = mysqli_num_rows($partition_find_count);
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
            findAllPartitions($query_filter, $pageinate_1, $words);
        ?>
    </tbody>
</table>
<?php
    echo "<ul class='pager'>";
        $search_param = isset($_GET['search']) ? "&search=".$search_query : "";
        for ($i = 1; $i <= $count; $i++) {
           if ($i == $pageinate || $pageinate == '0') {
                echo "<li><a class='active_link' href='categories.php?source=partitions&pageinate=".$i.$search_param."'>".$i."</a></li>";
            } else {
                echo "<li><a href='categories.php?source=partitions&pageinate=".$i.$search_param."'>".$i."</a></li>";
            }
        }
    echo "</ul>";
?>
