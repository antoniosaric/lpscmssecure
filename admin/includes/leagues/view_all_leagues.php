<form method='get'>
	<input type='hidden' name='source' value='leagues' />
	<input type='text' name='search' placeholder="Search leagues" required pattern='.*\S+.*' />
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
            <th class='col-md-2'>Entity name</th>
            <th class='col-md-2'>Image</th>
            <th>Description</th>
            <th class='col-md-1'>Edit</th>
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
                    $query_filter .= " (entity.name LIKE ? OR entity.description LIKE ? OR entity.id = ?)";
                    $key_count++;
                }
            }
            $query_filter .= " ORDER BY entity.id DESC";

            findAllEntities($query_filter, $words);
        ?>
    </tbody>
</table>
