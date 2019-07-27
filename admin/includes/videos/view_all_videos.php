<form method='get'>
	<input type='hidden' name='source' value='videos' />
	<?php
		if (isset($_GET['page_length'])) {
			echo "<input type='hidden' name='page_length' value=".$_GET['page_length']." />";
		}
	?>
	<input type='text' name='search' placeholder="Search videos" required pattern='.*\S+.*' />
	<input class="btn btn-primary" type="submit" value="Search" />
</form>
<?php
	#Fetch optional $_GET parameters (current page numbers, page length, search query)
	$page_team = isset($_GET['page_team']) ? $_GET['page_team'] : 1;
	$page_sport = isset($_GET['page_sport']) ? $_GET['page_sport'] : 1;
	$page_indiv = isset($_GET['page_indiv']) ? $_GET['page_indiv'] : 1;
	$page_length = isset($_GET['page_length']) ? $_GET['page_length'] : 20;
	$search_query = isset($_GET['search']) ? str_replace("+", " ", $_GET['search']) : "";
	if ($page_length != 20 && $page_length != 50 && $page_length != 100) {
		$page_length = 20;
	}
?>
<?php
	#Compute page info for table 1
	if (isset($_GET['search'])) {
		$select_all_video_1 = getVideosTeamQuery(-1, 20, $search_query);
	} else {
		$select_all_video_1 = getVideosTeamQuery();
	}
	$pagecount_1 = ceil(mysqli_num_rows($select_all_video_1) / $page_length);
	if ($page_team < 1 || $page_team > $pagecount_1 || $page_team == "") {
		$page_team = 1;
	}
	$offset_1 = $page_length * ($page_team - 1);
?>
<?php
	#Compute page info for table 2
	if (isset($_GET['search'])) {
		$select_all_video_2 = getVideosActivityQuery(-1, 20, $search_query);
	} else {
		$select_all_video_2 = getVideosActivityQuery();
	}
	$pagecount_2 = ceil(mysqli_num_rows($select_all_video_2) / $page_length);
	if ($page_sport < 1 || $page_sport > $pagecount_2 || $page_sport == "") {
		$page_sport = 1;
	}
	$offset_2 = $page_length * ($page_sport - 1);
?>
<?php
	#Compute page info for table 3
	if (isset($_GET['search'])) {
		$select_all_video_3 = getVideosIndivQuery(-1, 20, $search_query);
	} else {
		$select_all_video_3 = getVideosIndivQuery();
	}
	$pagecount_3 = ceil(mysqli_num_rows($select_all_video_3) / $page_length);
	if ($page_indiv < 1 || $page_indiv > $pagecount_3 || $page_indiv == "") {
		$page_indiv = 1;
	}
	$offset_3 = $page_length * ($page_indiv - 1);
?>
<?php
	#Generate pagelength selector, tables, and pagination
	$team_param = isset($_GET['page_team']) ? "&page_team=".$page_team : "";
	$sport_param = isset($_GET['page_sport']) ? "&page_sport=".$page_sport : "";
	$indiv_param = isset($_GET['page_indiv']) ? "&page_indiv=".$page_indiv : "";
	$plength_param = isset($_GET['page_length']) ? "&page_length=".$page_length : "";
	$search_param = isset($_GET['search']) ? "&search=".$search_query : "";

	if (!isset($_GET['search'])) {
		makeRecent10Table();
		makePagelengthSelector($page_length);
	} else {
		echo "<h2>Search Results for \"".$search_query."\"</h2>";
		makePagelengthSelector($page_length, $search_query);
	}

	deleteTeamVideo();
	if (isset($_GET['search'])) {
		makeVideosTable(1, $offset_1, $page_length, $search_query);
	} else {
		makeVideosTable(1, $offset_1, $page_length);
	}
	$pagelink_1 = "<li><a href='categories.php?source=videos&page_team=(?)"
			.$sport_param.$indiv_param.$plength_param.$search_param."'>(?)</a></li>";
	makeVideoPagination($pagelink_1, $page_team, $pagecount_1);

	deleteActivityVideo();
	if (isset($_GET['search'])) {
		makeVideosTable(2, $offset_2, $page_length, $search_query);
	} else {
		makeVideosTable(2, $offset_2, $page_length);
	}
	$pagelink_2 = "<li><a href='categories.php?source=videos&page_sport=(?)"
			.$team_param.$indiv_param.$plength_param.$search_param."'>(?)</a></li>";
	makeVideoPagination($pagelink_2, $page_sport, $pagecount_2);

	deleteIndivVideo();
	if (isset($_GET['search'])) {
		makeVideosTable(3, $offset_3, $page_length, $search_query);
	} else {
		makeVideosTable(3, $offset_3, $page_length);
	}
	$pagelink_3 = "<li><a href='categories.php?source=videos&page_indiv=(?)"
			.$team_param.$sport_param.$plength_param.$search_param."'>(?)</a></li>";
	makeVideoPagination($pagelink_3, $page_indiv, $pagecount_3);
?>
