<?php
// VIEW ALL VIDEOS FUNCTIONS:


    #SQL query for table 1
    function getVideosTeamQuery($offset = -1, $page_length = 20, $search = "") {
        global $connection_production;
        $sqlvideosteam = "SELECT DISTINCT video.id AS videoId, video.reference AS reference,
            video.title AS title, video.summary AS videoSummary, profile.id AS videoProfileId,
            video.videoStatus AS videoStatus, video.thumbString AS thumbString,
            video.videoSourceId AS videoSourceId, video_source.source AS videoSource,
            team.id AS teamId, profile_franchise_sm_video_sm.id AS p_f_sm_v_sm_id FROM video
            LEFT JOIN video_source ON video_source.id = video.videoSourceId
            LEFT JOIN profile_franchise_sm_video_sm ON video.id=profile_franchise_sm_video_sm.videoId
            LEFT JOIN profile_franchise_sm ON profile_franchise_sm_video_sm.profileFranchiseSmId=profile_franchise_sm.id
            LEFT JOIN profile ON profile_franchise_sm.profileId=profile.id
            LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
            LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
            LEFT JOIN team ON team.id=franchise_team_sm.teamId
            WHERE profile.mainProfileType=0";

        $words = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        if ($search != "") {
            foreach ($words as $key) {
                $sqlvideosteam .= " AND (video.title LIKE ? OR video.summary LIKE ? OR video.id = ?)";
            }
        }
        $sqlvideosteam .= " ORDER BY video.id DESC";
        if ($offset != -1) {
            $sqlvideosteam .= " LIMIT ?, ?";
        }
        $stmt = $connection_production->prepare($sqlvideosteam);

        $bind_parameters = array();
        $bind_parameters[0] = "";
        if ($search != "") {
            foreach ($words as $key) {
                $bind_parameters[0] = $bind_parameters[0]."ssi";
                $format_param = '%'.$key.'%';
                array_push($bind_parameters, $format_param);
                array_push($bind_parameters, $format_param);
                array_push($bind_parameters, $key);
            }
        }
        if ($offset != -1) {
            $bind_parameters[0] = $bind_parameters[0]."ii";
            array_push($bind_parameters, $offset);
            array_push($bind_parameters, $page_length);
        }
        if ($search != "" || $offset != -1) {
            call_user_func_array(array($stmt, "bind_param"), refValues($bind_parameters));
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
    #SQL query for table 2
    function getVideosActivityQuery($offset = -1, $page_length = 20, $search = "") {
        global $connection_production;
        $sqlvideosactivity = "SELECT DISTINCT video.id AS videoId, video.reference AS reference,
            video.title AS title, video.summary AS videoSummary, profile.id AS videoProfileId,
            video.videoStatus AS videoStatus, video.thumbString AS thumbString,
            video.videoSourceId AS videoSourceId, video_source.source AS videoSource, activity.id AS activityId,
            activity.activity AS activity, profile_activity_sm_video_sm.id AS p_a_sm_v_sm_id FROM video
            LEFT JOIN video_source ON video_source.id = video.videoSourceId
            LEFT JOIN profile_activity_sm_video_sm ON video.id=profile_activity_sm_video_sm.videoId
            LEFT JOIN profile_activity_sm ON profile_activity_sm_video_sm.profileActivitySmId=profile_activity_sm.id
            LEFT JOIN profile ON profile_activity_sm.profileId = profile.id
            LEFT JOIN activity ON activity.id = profile_activity_sm.activityId
            WHERE profile.mainProfileType=0";

        $words = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        if ($search != "") {
            foreach ($words as $key) {
                $sqlvideosactivity .= " AND (video.title LIKE ? OR video.summary LIKE ? OR video.id = ?)";
            }
        }
        $sqlvideosactivity .= " ORDER BY video.id DESC";
        if ($offset != -1) {
            $sqlvideosactivity .= " LIMIT ?, ?";
        }
        $stmt = $connection_production->prepare($sqlvideosactivity);

        $bind_parameters = array();
        $bind_parameters[0] = "";
        if ($search != "") {
            foreach ($words as $key) {
                $bind_parameters[0] = $bind_parameters[0]."ssi";
                $format_param = '%'.$key.'%';
                array_push($bind_parameters, $format_param);
                array_push($bind_parameters, $format_param);
                array_push($bind_parameters, $key);
            }
        }
        if ($offset != -1) {
            $bind_parameters[0] = $bind_parameters[0]."ii";
            array_push($bind_parameters, $offset);
            array_push($bind_parameters, $page_length);
        }
        if ($search != "" || $offset != -1) {
            call_user_func_array(array($stmt, "bind_param"), refValues($bind_parameters));
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
    #SQL query for table 3
    function getVideosIndivQuery($offset = -1, $page_length = 20, $search = "") {
        global $connection_production;
        $sqlvideosindiv = "SELECT DISTINCT video.id AS videoId, video.reference AS reference,
            video.title AS title, video.summary AS videoSummary, profile.id AS videoProfileId,
            video.videoStatus AS videoStatus, video.thumbString AS thumbString,
            video.videoSourceId AS videoSourceId, video_source.source AS videoSource,
            profile_video_sm.id AS p_v_sm_id FROM video
            LEFT JOIN video_source ON video_source.id = video.videoSourceId
            LEFT JOIN profile_video_sm ON video.id=profile_video_sm.videoId
            LEFT JOIN profile ON profile_video_sm.profileId=profile.id
            WHERE profile.mainProfileType=0";

        $words = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        if ($search != "") {
            foreach ($words as $key) {
                $sqlvideosindiv .= " AND (video.title LIKE ? OR video.summary LIKE ? OR video.id = ?)";
            }
        }
        $sqlvideosindiv .= " ORDER BY video.id DESC";
        if ($offset != -1) {
            $sqlvideosindiv .= " LIMIT ?, ?";
        }
        $stmt = $connection_production->prepare($sqlvideosindiv);

        $bind_parameters = array();
        $bind_parameters[0] = "";
        if ($search != "") {
            foreach ($words as $key) {
                $bind_parameters[0] = $bind_parameters[0]."ssi";
                $format_param = '%'.$key.'%';
                array_push($bind_parameters, $format_param);
                array_push($bind_parameters, $format_param);
                array_push($bind_parameters, $key);
            }
        }
        if ($offset != -1) {
            $bind_parameters[0] = $bind_parameters[0]."ii";
            array_push($bind_parameters, $offset);
            array_push($bind_parameters, $page_length);
        }
        if ($search != "" || $offset != -1) {
            call_user_func_array(array($stmt, "bind_param"), refValues($bind_parameters));
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
    function refValues($arr) {
        if (strnatcmp(phpversion(),'5.3') >= 0) { //Reference is required for PHP 5.3+
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }
    #Create dropdown to select number of results per page
    function makePagelengthSelector($page_length, $search = "") {
        $s_20 = $page_length == 20;
        $s_50 = $page_length == 50;
        $s_100 = $page_length == 100;
        echo "<div style='margin-top:2.5vh'>";
        echo "Videos per page:";
        echo "<form style='display:inline; margin-left:1.3vw'>";
        echo "<input type='hidden' name='source' value='videos' />";
        if ($search != "") {
            echo "<input type='hidden' name='search' value='".$search."' />";
        }
        echo "<select name='page_length' onchange='this.form.submit()'>";
        echo "<option value=20".($s_20 ? " selected" : "").">20</option>";
        echo "<option value=50".($s_50 ? " selected" : "").">50</option>";
        echo "<option value=100".($s_100 ? " selected" : "").">100</option>";
        echo "</select>";
        echo "</form>";
        echo "</div>";
    }
    #Generate videos table given table number, result-offset, and page length
    function makeVideosTable($tablenum, $offset, $page_length, $search = "") {
        switch ($tablenum) {
            case 1:
            $video_query = getVideosTeamQuery($offset, $page_length, $search); break;
            case 2:
            $video_query = getVideosActivityQuery($offset, $page_length, $search); break;
            case 3:
            $video_query = getVideosIndivQuery($offset, $page_length, $search); break;
        }
        if (mysqli_num_rows($video_query) > 0) {
            switch ($tablenum) {
                case 1:
                echo '<h3>Video Team Association</h3>'; break;
                case 2:
                echo '<h3>Video Activity Association</h3>'; break;
                case 3:
                echo '<h3>Video No Team/Activity Association</h3>'; break;
            }
            $styling = ".noverflow {max-height:5vh; overflow-y:auto}";
            $styling .= " table {table-layout:fixed}";
            $styling .= " table td {word-wrap:break-word}";
            echo "<style>".$styling."</style>";
            echo "<table class='table table-striped table-bordered table-hover'>";
            echo '<thead><tr>';
            echo "<th style='width:4vw'>Video ID</th>";
            echo "<th class='col-md-2'>Title</th>";
            echo "<th class='col-md-2'>Reference</th>";
            echo "<th style='width:6vw'>Source</th>";
            echo "<th>Description</th>";
            echo "<th style='width:4vw'>Profile ID</th>";
            echo "<th style='width:4vw'>Activity ID</th>";
            echo "<th style='width:4vw'>Team ID</th>";
            echo "<th style='width:4vw'>Update</th>";
            echo "<th style='width:6vw'>Delete</th>";
            echo '</tr></thead>';
            echo '<tbody>';

            while ($row = mysqli_fetch_assoc($video_query)) {
                $video_id = $row['videoId'];
                $video_title = $row['title'];
                $video_reference = $row['reference'];
                $video_source_name = $row['videoSource'];
                $video_summary = $row['videoSummary'];
                $video_profile_id = $row['videoProfileId'];

                $video_team_id = ($tablenum == 1 ? $row['teamId'] : "Not set");
                $p_f_sm_v_sm_id = ($tablenum == 1 ? $row['p_f_sm_v_sm_id'] : "");
                $video_activity_id = ($tablenum == 2 ? $row['activityId'] : "Not set");
                $video_activity_name = ($tablenum == 2 ? $row['activity'] : "");
                $p_a_sm_v_sm_id = ($tablenum == 2 ? $row['p_a_sm_v_sm_id'] : "");
                $p_v_sm_id = ($tablenum == 3 ? $row['p_v_sm_id'] : "");

                echo "<tr>";
                echo "<td>".$video_id."</td>";
                echo "<td><div class='noverflow'>".$video_title."</div></td>";
                echo "<td><div class='noverflow'>".$video_reference."</div></td>";
                echo "<td>".$video_source_name."</td>";
                echo "<td><div class='noverflow'>".$video_summary."</div></td>";
                echo "<td><a href='categories.php?source=profile_update_athlete&profile_id=".$video_profile_id."'>".$video_profile_id."</a></td>";
                $link_1 = "<a href='categories.php?source=sports_update&activity_id=".$video_activity_id."'>";
                echo "<td>".($tablenum == 2 ? $link_1.$video_activity_id."</a></td>" : $video_activity_id."</td>");
                $link_2 = "<a href='categories.php?source=team_update&team_id=".$video_team_id."'>";
                echo "<td>".($tablenum == 1 ? $link_2.$video_team_id."</a></td>" : $video_team_id."</td>");
                echo "<td><a class='btn btn-info' href='categories.php?source=update_video&video_id=".$video_id."'>Edit</a></td>";
                echo "<td><form method='post'>";
                echo "<input type='hidden' name='delete_video_id' value=".$video_id." />";
                echo "<input type='hidden' name='profile_id_set' value=".$video_profile_id." />";
                if ($tablenum == 1) {
                    echo "<input type='hidden' name='p_f_sm_v_sm_id' value=".$p_f_sm_v_sm_id." />";
                    echo "<input type='hidden' name='team_id' value=".$video_team_id." />";
                    $delete_type = 'delete_video_team';
                } else if ($tablenum == 2) {
                    echo "<input type='hidden' name='p_a_sm_v_sm_id' value=".$p_a_sm_v_sm_id." />";
                    echo "<input type='hidden' name='solo_video_activity' value=".$video_activity_name." />";
                    $delete_type = 'delete_video_activity';
                } else if ($tablenum == 3) {
                    echo "<input type='hidden' name='p_v_sm_id' value=".$p_v_sm_id." />";
                    $delete_type = 'delete_video_indiv';
                }
                echo "<input onClick=\"return confirm('Are you sure you want to do that?');\" class='btn btn-danger' type='submit' name='".$delete_type."' value='DELETE' />";
                echo "</form></td>";
                echo "</tr>";
            }
            echo '</tbody>';
            echo '</table>';
        }
    }
    #Create table showing 10 most recent videos
    #Does not have video deletion functionality
    function makeRecent10Table() {
        global $connection_production;
        $recent_sql = "SELECT video.id AS videoId, reference, title, summary, video_source.source AS source FROM video
            LEFT JOIN video_source ON video_source.id = video.videoSourceId
            ORDER BY video.id DESC
            LIMIT 10";
        $recent_query = mysqli_query($connection_production, $recent_sql);
        if (mysqli_num_rows($recent_query) > 0) {
            echo "<h3>10 Most Recent Videos</h3>";
            $styling = ".noverflow {max-height:5vh; overflow-y:auto}";
            $styling .= " table {table-layout:fixed}";
            $styling .= " table td {word-wrap:break-word}";
            echo "<style>".$styling."</style>";
            echo "<table class='table table-striped table-bordered table-hover'>";
            echo '<thead><tr>';
            echo "<th style='width:4vw'>ID</th>";
            echo "<th class='col-md-2'>Title</th>";
            echo "<th class='col-md-2'>Reference</th>";
            echo "<th style='width:6vw'>Source</th>";
            echo "<th>Description</th>";
            echo "<th style='width:4vw'>Update</th>";
            echo '</tr></thead>';
            echo "<tbody>";
            
            while ($row = mysqli_fetch_assoc($recent_query)) {
                $video_id = $row['videoId'];
                $video_title = $row['title'];
                $video_reference = $row['reference'];
                $video_source_name = $row['source'];
                $video_summary = $row['summary'];

                echo "<tr>";
                echo "<td>".$video_id."</td>";
                echo "<td><div class='noverflow'>".$video_title."</td>";
                echo "<td><div class='noverflow'>".$video_reference."</td>";
                echo "<td>".$video_source_name."</td>";
                echo "<td><div class='noverflow'>".$video_summary."</td>";
                echo "<td><a class='btn btn-info' href='categories.php?source=update_video&video_id=".$video_id."'>Edit</a></td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        }
    }
    #Create pagination buttons using pagelink template string
    #Replaces "(?)" in pagelink string with the button number
    #Limits #buttons displayed to previous 100 and next 150 from current page
    #Always includes a link to first and last pages
    function makeVideoPagination($pagelink, $curr_page, $pagecount) {
        if ($pagecount <= 1) {
            return;
        }
        echo "<ul class='pager'>";
        $firstlink = str_replace("(?)", "1", $pagelink);
        if ($curr_page == 1) {
            echo substr_replace($firstlink, "class='active_link' ", 7, 0);
        } else {
            echo $firstlink;
        }
        if ($curr_page > 102) {
            echo "...";
        }
        for ($i = ($curr_page > 101 ? $curr_page-100 : 2);
                ($i <= $pagecount) && ($i - $curr_page <= 150);
                $i++) {
            $replaced = str_replace("(?)", $i, $pagelink);
            if ($i == $curr_page) {
                echo substr_replace($replaced, "class='active_link' ", 7, 0);
            } else {
                echo $replaced;
            }
        }
        if ($curr_page + 151 <= $pagecount) {
            if ($curr_page + 151 < $pagecount) {
                echo "...";
            }
            echo str_replace("(?)", $pagecount, $pagelink);
        }
        echo "</ul>";
    }
?>



<?php
// ADD/UPDATE VIDEO FUNCTIONS:


    #Inserts a new video's information into the 'video' table
    function addVideoInfo() {
        global $connection_production;
        if (isset($_POST['add_video_info'])) {
            $reference = $_POST['reference'];
            $source_id = $_POST['source_id'];
            $title = $_POST['title'];
            $summary = $_POST['summary'];
            $status = $_POST['status'];
            $thumbstring = computeThumbString($reference, $source_id);

            $video_sql = "INSERT INTO video (reference, thumbString, title,
                summary, videoSourceId, videoStatus) VALUES (?, ?, ?, ?, ?, ?)";
            $video_stmt = $connection_production->prepare($video_sql);
            $video_stmt->bind_param("ssssis", $reference, $thumbstring, $title, $summary, $source_id, $status);

            if ($video_stmt->execute()) {
                $video_last_id = $connection_production->insert_id;
                createUUID('video', $video_last_id);
                $update_string = "added video id: ".$video_last_id." :: title: ".$title.
                    ", reference: ".$reference.", status: ".$status;
                insertChange($_SESSION['account_id'], 'video', 'add video', $video_last_id, $update_string);
                header("location: categories.php?source=update_video&video_id=".$video_last_id);
            } else {
                echo "<h3 style='color:red;'>Something went wrong</h3>";
            }
            $video_stmt->close();
        }
    }
    #Updates the information of a row in the video table
    function updateVideoInfo() {
        global $connection_production;
        if (isset($_POST['update_video_info'])) {
            $video_id = $_POST['video_id'];
            $reference = $_POST['reference'];
            $source_id = $_POST['source_id'];
            $thumbstring = $_POST['thumbstring'];
            $title = $_POST['title'];
            $summary = $_POST['summary'];
            $status = $_POST['status'];

            $prev_vid_sql = "SELECT * FROM video WHERE id=?";
            $prev_stmt = $connection_production->prepare($prev_vid_sql);
            $prev_stmt->bind_param("i", $video_id);
            $prev_stmt->execute();
            $prev_result = $prev_stmt->get_result();
            $prev_row = $prev_result->fetch_assoc();
            $prev_stmt->close();

            if ($prev_result->num_rows > 0) {
                if (($prev_row['thumbString'] == $thumbstring || empty($thumbstring))
                    && ($prev_row['videoSourceId'] != $source_id || $prev_row['reference'] != $reference)) {
                    $thumbstring = computeThumbString($reference, $source_id);
                }
                $update_sql = "UPDATE video SET reference=?, videoSourceId=?,
                    thumbString=?, title=?, summary=?, videoStatus=? WHERE id=?";
                $update_stmt = $connection_production->prepare($update_sql);
                $update_stmt->bind_param("sissssi", $reference, $source_id,
                    $thumbstring, $title, $summary, $status, $video_id);

                if ($update_stmt->execute()) {
                    $update_string = 'update video id: '.$video_id;
                    if ($prev_row['reference'] != $reference) {
                        $update_string .= ', changed reference: '.$reference;
                    }
                    if ($prev_row['videoSourceId'] != $source_id) {
                        $update_string .= ', changed source_id: '.$source_id;
                    }
                    if ($prev_row['thumbString'] != $thumbstring) {
                        $update_string .= ', changed thumbnail URL: '.$thumbstring;
                    }
                    if ($prev_row['title'] != $title) {
                        $update_string .= ', changed title: '.$title;
                    }
                    if ($prev_row['summary'] != $summary) {
                        $update_string .= ', changed summary: '.$summary;
                    }
                    if ($prev_row['videoStatus'] != $status) {
                        $update_string .= ', changed status: '.$status;
                    }
                    insertChange($_SESSION['account_id'], 'video', 'update info', $video_id, $update_string);
                    header("location: categories.php?source=update_video&video_id=".$video_id);
                } else {
                    echo "<h3 style='color:red'>Something went wrong</h3>";
                }
                $update_stmt->close();
            } else {
                echo "<h3 style='color:red'>Video ID: ".$video_id." - Not found</h3>";
            }
        }
    }
    #Create a new event and associate it with the current video
    function addVideoNewEvent() {
        global $connection_production;
        if (isset($_POST['add_video_new_event'])) {
            $video_id = $_POST['video_id'];
            $event_name = $_POST['event_name'];
            $event_desc = $_POST['event_desc'];

            $search_event_sql = "SELECT eventId AS event_id FROM event_video_sm WHERE videoId=?";
            $search_event_stmt = $connection_production->prepare($search_event_sql);
            $search_event_stmt->bind_param("i", $video_id);
            $search_event_stmt->execute();
            $row_search_event = $search_event_stmt->get_result()->fetch_assoc();
            $search_event_stmt->close();

            if (!$row_search_event['event_id']) {

                $new_event_sql = "INSERT INTO event (event, description) VALUES (?, ?)";
                $new_event_stmt = $connection_production->prepare($new_event_sql);
                $new_event_stmt->bind_param("ss", $event_name, $event_desc);

                if ($new_event_stmt->execute()) {
                    $new_event_id = $connection_production->insert_id;
                    createUUID('event', $new_event_id);

                    $new_assoc_sql = "INSERT INTO event_video_sm (videoId, eventId) VALUES (?, ?)";
                    $new_assoc_stmt = $connection_production->prepare($new_assoc_sql);
                    $new_assoc_stmt->bind_param("ii", $video_id, $new_event_id);

                    if ($new_assoc_stmt->execute()) {
                        $new_assoc_id = $connection_production->insert_id;
                        $update_string = "video id: ".$video_id." :: added new event id: ".$new_event_id.", added video-event-association id: ".$new_assoc_id;
                        insertChange($_SESSION['account_id'], 'video', 'add new event', $video_id, $update_string);
                        header("location: categories.php?source=update_video&video_id=".$video_id."#add_event");
                    } else {
                        echo "<h3 style='color:red'>Something went wrong</h3>";
                    }
                    $new_assoc_stmt->close();
                } else {
                    echo "<h3 style='color:red'>Something went wrong</h3>";
                }
                $new_event_stmt->close();
            } else {
                echo "<h3 style='color:red'>Video ID: ".$video_id." - already has an event ID: "
                    .$row_search_event['event_id']." - one event per video.</h3>";
            }
        }
    }
    #Associate an existing event with a video
    function addVideoExistingEvent() {
        global $connection_production;
        if (isset($_POST['add_video_existing_event'])) {
            $video_id = $_POST['video_id'];
            $event_id = $_POST['event_id'];

            $search_event_sql = "SELECT eventId AS event_id FROM event_video_sm WHERE videoId=?";
            $search_event_stmt = $connection_production->prepare($search_event_sql);
            $search_event_stmt->bind_param("i", $video_id);
            $search_event_stmt->execute();
            $row_search_event = $search_event_stmt->get_result()->fetch_assoc();
            $search_event_stmt->close();

            $found_event_sql = "SELECT event.id AS event_id FROM event WHERE event.id=?";
            $found_event_stmt = $connection_production->prepare($found_event_sql);
            $found_event_stmt->bind_param("i", $event_id);
            $found_event_stmt->execute();
            $row_found_event = $found_event_stmt->get_result()->fetch_assoc();
            $found_event_stmt->close();

            if (!$row_search_event['event_id']) {
                if (!!$row_found_event['event_id']) {

                    $assoc_event_sql = "INSERT INTO event_video_sm (videoId, eventId) VALUES (?, ?)";
                    $assoc_event_stmt = $connection_production->prepare($assoc_event_sql);
                    $assoc_event_stmt->bind_param("ii", $video_id, $event_id);

                    if ($assoc_event_stmt->execute()) {
                        $new_assoc_id = $connection_production->insert_id;
                        $update_string = "video id: ".$video_id." :: add existing event id: ".$event_id.", added video-event-association id: ".$new_assoc_id;
                        insertChange($_SESSION['account_id'], 'video', 'add existing event', $video_id, $update_string);
                        header("location: categories.php?source=update_video&video_id=".$video_id."#add_event");
                    } else {
                        echo "<h3 style='color:red'>Something went wrong</h3>";
                    }
                    $assoc_event_stmt->close();
                } else {
                    echo "<h3 style='color:red'>Event ID: ".$event_id." - Not found</h3>";
                }
            } else {
                echo "<h3 style='color:red'>Video ID: ".$video_id." - already has an event ID: "
                    .$row_search_event['event_id']." - one event per video.</h3>";
            }
        }
    }
    #Updates an event's information
    function updateVideoEvent() {
        global $connection_production;
        if (isset($_POST['update_event'])) {
            $video_id = $_POST['video_id'];
            $event_id = $_POST['event_id'];
            $event_name = $_POST['event_name'];
            $event_desc = $_POST['event_desc'];

            $prev_event_sql = "SELECT * FROM event WHERE id=?";
            $prev_stmt = $connection_production->prepare($prev_event_sql);
            $prev_stmt->bind_param("i", $event_id);
            $prev_stmt->execute();
            $prev_row = $prev_stmt->get_result()->fetch_assoc();
            $prev_stmt->close();

            if (!!$prev_row['id']) {
                if ($prev_row['event'] != $event_name || $prev_row['description'] != $event_desc) {

                    $update_event_sql = "UPDATE event SET event=?, description=? WHERE id=?";
                    $update_event_stmt = $connection_production->prepare($update_event_sql);
                    $update_event_stmt->bind_param("ssi", $event_name, $event_desc, $event_id);

                    if ($update_event_stmt->execute()) {
                        $update_string = "update event id: ".$event_id." (associated video id: ".$video_id.")";
                        if ($prev_row['event'] != $event_name) {
                            $update_string .= ", changed event name: ".$event_name;
                        }
                        if ($prev_row['description'] != $event_desc) {
                            $update_string .= ", changed event description: ".$event_desc;
                        }
                        insertChange($_SESSION['account_id'], 'video', 'update event', $video_id, $update_string);
                        header("location: categories.php?source=update_video&video_id=".$video_id."#event");
                    } else {
                        echo "<h3 style='color:red'>Something went wrong</h3>";
                    }
                    $update_event_stmt->close();
                } else {
                    echo "<h3 style='color:red'>Event ID: ".$event_id." - already has this name and description</h3>";
                }
            } else {
                echo "<h3 style='color:red'>Event ID: ".$event_id." - Not found</h3>";
            }
        }
    }
    #Deletes the association between an event and the video
    #If the event is only associated with this video, also deletes the event itself
    function deleteVideoEvent() {
        global $connection_production;
        if (isset($_POST['delete_event'])) {
            $video_id = $_POST['video_id'];

            $update_string = "video id: ".$video_id." :: ";
            if (deleteEventsHelper($video_id, $update_string) == 1) {

                insertChange($_SESSION['account_id'], 'video', 'delete event', $video_id, $update_string);
                header("location: categories.php?source=update_video&video_id=".$video_id."#event");
            } else {
                echo "<h3 style='color:red'>Something went wrong</h3>";
            }
        }
    }
    #function to handle deletion of events
    function deleteEventsHelper($video_id, &$update_string) {
        global $connection_production;

        $check_events_assoc_sql = "SELECT id AS e_v_sm_id, eventId AS event_id FROM event_video_sm WHERE videoId=?";
        $check_events_assoc_stmt = $connection_production->prepare($check_events_assoc_sql);
        $check_events_assoc_stmt->bind_param("i", $video_id);
        $check_events_assoc_stmt->execute();
        $check_events_assoc_result = $check_events_assoc_stmt->get_result();
        $check_events_assoc_stmt->close();

        if ($check_events_assoc_result->num_rows > 0) {
            $deleted_associations = array();
            $deleted_events = array();

            while ($row_event_assoc = $check_events_assoc_result->fetch_assoc()) {
                if (!$row_event_assoc['e_v_sm_id']) {
                    break;
                }
                $e_v_sm_id = $row_event_assoc['e_v_sm_id'];
                $event_id = $row_event_assoc['event_id'];

                $all_assoc_sql = "SELECT videoId AS video_id FROM event_video_sm WHERE eventId=?";
                $all_assoc_stmt = $connection_production->prepare($all_assoc_sql);
                $all_assoc_stmt->bind_param("i", $event_id);
                $all_assoc_stmt->execute();
                $all_assoc_result = $all_assoc_stmt->get_result();
                $all_assoc_stmt->close();

                if ($all_assoc_result->num_rows == 1) {

                    $delete_event_sql = "DELETE FROM event WHERE id=?";
                    $delete_event_stmt = $connection_production->prepare($delete_event_sql);
                    $delete_event_stmt->bind_param("i", $event_id);
                    $event_deleted = $delete_event_stmt->execute();
                    $delete_event_stmt->close();
                }
                if (!isset($event_deleted) || !!$event_deleted) {

                    $delete_assoc_sql = "DELETE FROM event_video_sm WHERE id=?";
                    $delete_assoc_stmt = $connection_production->prepare($delete_assoc_sql);
                    $delete_assoc_stmt->bind_param("i", $e_v_sm_id);

                    if ($delete_assoc_stmt->execute()) {
                        $delete_assoc_stmt->close();
                        array_push($deleted_associations, $e_v_sm_id);
                        if (isset($event_deleted)) {
                            array_push($deleted_events, $event_id);
                        }
                    } else {
                        $delete_assoc_stmt->close();
                        return -1;
                    }
                } else {
                    return -1;
                }
            }
            $update_string .= "deleted event-video-association id(s): ".implode('/', $deleted_associations);
            if (empty($deleted_events)) {
                $update_string .= ", no deleted events";
            } else {
                $update_string .= ", deleted event id(s): ".implode('/', $deleted_events);
            }
            return 1;
        } else {
            return 0;
        }
    }
    #Assigns a profile to the video, either by itself, through an activity,
    #or through a franchise/team - depending on the combination of input fields
    function assignProfileToVideo() {
        global $connection_production;
        if (isset($_POST['assign_profile'])) {
            $video_id = $_POST['video_id'];
            $profile_id = $_POST['profile_id'];
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

            $profile_found_sql = "SELECT * FROM profile WHERE id=?";
            $profile_found_stmt = $connection_production->prepare($profile_found_sql);
            $profile_found_stmt->bind_param("i", $profile_id);
            $profile_found_stmt->execute();
            $row_profile_found = $profile_found_stmt->get_result()->fetch_assoc();
            $profile_found_stmt->close();

            if (!!$row_profile_found['id']) {

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
                    //Add main profile
                    if (!$row_search_linked_video['id']) {
                        if ($add_type != -1) {
                            if ($add_type == 1) {
                                assignSoloProfile($video_id, $profile_id);
                            }
                            else if ($add_type == 2) {
                                assignActivityProfile($video_id, $profile_id, $activity_id);
                            }
                            else if ($add_type == 3) {
                                assignTeamProfile($video_id, $profile_id, $team_id);
                            }
                        } else {
                            echo "<h3 style='color:red'>Activity ID and Team ID cannot both be set</h3>";
                        }
                    } else {
                        echo "<h3 style='color:red'>Profile ID: ".$profile_id." - Already added</h3>";
                    }
                }
                else if ($add_type == 1) {
                    //Add linked profile
                    if (($row_main_profile_check['main_profile_id'] != $profile_id) && (!$row_search_linked_video['id'])) {

                        $insert_linked_video_sql = "INSERT INTO video_linked (videoId, profileId) VALUES (?, ?)";
                        $insert_linked_video_stmt = $connection_production->prepare($insert_linked_video_sql);
                        $insert_linked_video_stmt->bind_param("ii", $video_id, $profile_id);

                        if ($insert_linked_video_stmt->execute()) {
                            $new_link_assoc_id = $connection_production->insert_id;
                            $update_string = "video id: ".$video_id." :: assign linked profile id: ".$profile_id.", added video_linked id: ".$new_link_assoc_id;
                            insertChange($_SESSION['account_id'], 'video', 'assign linked profile', $video_id, $update_string);
                            header("location: categories.php?source=update_video&video_id=".$video_id."#assign_profile");
                        } else {
                            echo "<h3 style='color:red'>Something went wrong</h3>";
                        }
                        $insert_linked_video_stmt->close();
                    } else {
                        echo "<h3 style='color:red'>Profile ID: ".$profile_id." - Already added</h3>";
                    }
                } else {
                    echo "<h3 style='color:red'>Main profile ID: ".$row_main_profile_check['main_profile_id']." - Already added. Only main profile can have activity ID or team ID</h3>";
                }
            } else {
                echo "<h3 style='color:red'>Profile ID: ".$profile_id." - Not found</h3>";
            }
        }
    }
    #Helper function for assigning a solo main profile to a video
    function assignSoloProfile($video_id, $profile_id) {
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
                $update_string = "video id: ".$video_id." :: assign solo profile id: ".$profile_id.", added profile-video-association id: ".$new_assoc_id;
                insertChange($_SESSION['account_id'], 'video', 'assign solo profile', $video_id, $update_string);
                header("location: categories.php?source=update_video&video_id=".$video_id."#video_profiles");
            } else {
                echo "<h3 style='color:red'>Something went wrong</h3>";
            }
            $assoc_profile_stmt->close();
            $set_main_profile_stmt->close();
        } else {
            echo "<h3 style='color:red'>Video ID: ".$video_id." - already has solo profile ID: ".$profile_id."</h3>";
        }
    }
    #Helper function for assinging a main profile to a video through an activity
    function assignActivityProfile($video_id, $profile_id, $activity_id) {
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
                    $update_string = "video id: ".$video_id." :: assign profile/activity ids: ".$profile_id."/".$activity_id.", p_a_sm_id ID: ".$p_a_sm_id.", added p_a_sm_v_sm ID: ".$new_p_a_sm_v_id;
                    insertChange($_SESSION['account_id'], 'video', 'assign profile/activity', $video_id, $update_string);
                    header("location: categories.php?source=update_video&video_id=".$video_id."#video_profiles");
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
    #Helper function for assinging a main profile to a video through a team
    function assignTeamProfile($video_id, $profile_id, $team_id) {
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
                    $update_string = "video id: ".$video_id." :: assign profile/team ids: ".$profile_id."/".$team_id.", p_f_sm_id ID: ".$p_f_sm_id.", added p_f_sm_v_sm ID: ".$new_p_f_sm_v_id;
                    insertChange($_SESSION['account_id'], 'video', 'assign profile/team', $video_id, $update_string);
                    header("location: categories.php?source=update_video&video_id=".$video_id."#video_profiles");
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
    #delete main profile assignment from a video
    #call helper function based on assignment type
    function unassignVideoMainProfile() {
        global $connection_production;
        if (isset($_POST['unassign_main_profile'])) {
            $video_id = $_POST['video_id'];
            $profile_id = $_POST['profile_id'];
            $activity_id = isset($_POST['activity_id']) ? $_POST['activity_id'] : NULL;
            $team_id = isset($_POST['team_id']) ? $_POST['team_id'] : NULL;

            if (empty($activity_id) && empty($team_id)) {

                unassignSoloProfile($video_id, $profile_id);
            }
            else if (!empty($activity_id) && empty($team_id)) {

                unassignActivityProfile($video_id, $profile_id, $activity_id);
            }
            else if (empty($activity_id) && !empty($team_id)) {

                unassignTeamProfile($video_id, $profile_id, $team_id);
            } else {
                echo "<h3 style='color:red'>Something went wrong</h3>";
            }
        }
    }
    #helper function for unassigning main solo profile from a video
    function unassignSoloProfile($video_id, $profile_id) {
        global $connection_production;

        $find_profile_video_sql = "SELECT * FROM profile_video_sm WHERE profileId=? AND videoId=?";
        $find_profile_video_stmt = $connection_production->prepare($find_profile_video_sql);
        $find_profile_video_stmt->bind_param("ii", $profile_id, $video_id);
        $find_profile_video_stmt->execute();
        $row_profile_video = $find_profile_video_stmt->get_result()->fetch_assoc();
        $find_profile_video_stmt->close();

        $update_string = "video id: ".$video_id." :: unassign solo profile id: ".$profile_id." - ";

        if (!!$row_profile_video['id']) {
            $p_v_sm_id = $row_profile_video['id'];

            $del_profile_video_sql = "DELETE FROM profile_video_sm WHERE id=?";
            $del_profile_video_stmt = $connection_production->prepare($del_profile_video_sql);
            $del_profile_video_stmt->bind_param("i", $p_v_sm_id);

            if ($del_profile_video_stmt->execute()) {
                $update_string .= "deleted profile-video-association id: ".$p_v_sm_id;
            } else {
                echo "<h3 style='color:red'>Something went wrong</h3>";
                $error = true;
            }
            $del_profile_video_stmt->close();
        } else {
            $update_string .= "profile-video association not found";
        }
        if (!isset($error) || !$error) {

            if (clearVideoProfileAdminId($video_id)) {
                $update_string .= ", cleared profileAdminId on the video";
                insertChange($_SESSION['account_id'], 'video', 'unassign solo profile', $video_id, $update_string);
                header("location: categories.php?source=update_video&video_id=".$video_id."#profiles");
            } else {
                echo "<h3 style='color:red'>Something went wrong</h3>";
            }
        }
    }
    #helper function for unassigning main profile/activity from a video
    function unassignActivityProfile($video_id, $profile_id, $activity_id) {
        global $connection_production;

        $search_profile_activity_sql = "SELECT * FROM profile_activity_sm WHERE profileId=? AND activityId=?";
        $search_profile_activity_stmt = $connection_production->prepare($search_profile_activity_sql);
        $search_profile_activity_stmt->bind_param("ii", $profile_id, $activity_id);
        $search_profile_activity_stmt->execute();
        $row_search_profile_activity = $search_profile_activity_stmt->get_result()->fetch_assoc();
        $search_profile_activity_stmt->close();

        if (!!$row_search_profile_activity['id']) {
            $p_a_sm_id = $row_search_profile_activity['id'];

            $all_profile_activity_video_sql = "SELECT * FROM profile_activity_sm_video_sm WHERE profileActivitySmId=?";
            $all_profile_activity_video_stmt = $connection_production->prepare($all_profile_activity_video_sql);
            $all_profile_activity_video_stmt->bind_param("i", $p_a_sm_id);
            $all_profile_activity_video_stmt->execute();
            $result_all_profile_activity_video = $all_profile_activity_video_stmt->get_result();
            $all_profile_activity_video_stmt->close();

            if ($result_all_profile_activity_video->num_rows > 0) {
                if ($result_all_profile_activity_video->num_rows == 1) {

                    $del_profile_activity_sql = "DELETE FROM profile_activity_sm WHERE id=?";
                    $del_profile_activity_stmt = $connection_production->prepare($del_profile_activity_sql);
                    $del_profile_activity_stmt->bind_param("i", $p_a_sm_id);
                    $p_a_sm_deleted = $del_profile_activity_stmt->execute();
                    $del_profile_activity_stmt->close();
                }
                if (!isset($p_a_sm_deleted) || !!$p_a_sm_deleted) {

                    $search_profile_activity_sql = "SELECT * FROM profile_activity_sm_video_sm
                        WHERE profileActivitySmId=? AND videoId=?";
                    $search_profile_activity_stmt = $connection_production->prepare($search_profile_activity_sql);
                    $search_profile_activity_stmt->bind_param("ii", $p_a_sm_id, $video_id);
                    $search_profile_activity_stmt->execute();
                    $row_search_profile_activity = $search_profile_activity_stmt->get_result()->fetch_assoc();
                    $search_profile_activity_stmt->close();

                    if (!!$row_search_profile_activity['id']) {
                        $p_a_sm_v_sm_id = $row_search_profile_activity['id'];

                        $del_profile_activity_video_sql = "DELETE FROM profile_activity_sm_video_sm WHERE id=?";
                        $del_profile_activity_video_stmt = $connection_production->prepare($del_profile_activity_video_sql);
                        $del_profile_activity_video_stmt->bind_param("i", $p_a_sm_v_sm_id);

                        if ($del_profile_activity_video_stmt->execute()) {

                            clearVideoProfileAdminId($video_id);

                            $update_string = "video id: ".$video_id." :: unassign profile/activity ids: ".$profile_id."/".$activity_id;
                            if ($p_a_sm_deleted == true) {
                                $update_string .= ", deleted p_a_sm_id: ".$p_a_sm_id;
                            } else {
                                $update_string .= ", p_a_sm_id: ".$p_a_sm_id." - not deleted";
                            }
                            $update_string .= ", deleted p_a_sm_v_sm id: ".$p_a_sm_v_sm_id;
                            $update_string .= ", cleared profileAdminId on the video";

                            insertChange($_SESSION['account_id'], 'video', 'unassign profile/activity', $video_id, $update_string);
                            header("location: categories.php?source=update_video&video_id=".$video_id."#profiles");
                        } else {
                            echo "<h3 style='color:red'>Something went wrong</h3>";
                        }
                        $del_profile_activity_video_stmt->close();
                    } else {
                        echo "<h3 style='color:red'>Could not find profile_activity_sm_video_sm id</h3>";
                    }
                } else {
                    echo "<h3 style='color:red'>Something went wrong</h3>";
                }
            } else {
                echo "<h3 style='color:red'>Could not find profile_activity_sm_video_sm id</h3>";
            }
        } else {
            echo "<h3 style='color:red'>Could not find profile_activity_sm id</h3>";
        }
    }
    #helper function for unassigning main profile/team from a video
    function unassignTeamProfile($video_id, $profile_id, $team_id) {
        global $connection_production;

        $search_profile_team_sql = "SELECT * FROM profile_franchise_sm WHERE profileId=? AND franchiseId=?";
        $search_profile_team_stmt = $connection_production->prepare($search_profile_team_sql);
        $search_profile_team_stmt->bind_param("ii", $profile_id, $team_id);
        $search_profile_team_stmt->execute();
        $row_search_profile_team = $search_profile_team_stmt->get_result()->fetch_assoc();
        $search_profile_team_stmt->close();

        if (!!$row_search_profile_team['id']) {
            $p_f_sm_id = $row_search_profile_team['id'];

            $all_profile_team_video_sql = "SELECT * FROM profile_franchise_sm_video_sm WHERE profileFranchiseSmId=?";
            $all_profile_team_video_stmt = $connection_production->prepare($all_profile_team_video_sql);
            $all_profile_team_video_stmt->bind_param("i", $p_f_sm_id);
            $all_profile_team_video_stmt->execute();
            $result_all_profile_team_video = $all_profile_team_video_stmt->get_result();
            $all_profile_team_video_stmt->close();

            if ($result_all_profile_team_video->num_rows > 0) {
                if ($result_all_profile_team_video->num_rows == 1) {

                    $del_profile_team_sql = "DELETE FROM profile_franchise_sm WHERE id=?";
                    $del_profile_team_stmt = $connection_production->prepare($del_profile_team_sql);
                    $del_profile_team_stmt->bind_param("i", $p_f_sm_id);
                    $p_f_sm_deleted = $del_profile_team_stmt->execute();
                    $del_profile_team_stmt->close();
                }
                if (!isset($p_f_sm_deleted) || !!$p_f_sm_deleted) {

                    $search_profile_team_sql = "SELECT * FROM profile_franchise_sm_video_sm
                        WHERE profileFranchiseSmId=? AND videoId=?";
                    $search_profile_team_stmt = $connection_production->prepare($search_profile_team_sql);
                    $search_profile_team_stmt->bind_param("ii", $p_f_sm_id, $video_id);
                    $search_profile_team_stmt->execute();
                    $row_search_profile_team = $search_profile_team_stmt->get_result()->fetch_assoc();
                    $search_profile_team_stmt->close();

                    if (!!$row_search_profile_team['id']) {
                        $p_f_sm_v_sm_id = $row_search_profile_team['id'];

                        $del_profile_team_video_sql = "DELETE FROM profile_franchise_sm_video_sm WHERE id=?";
                        $del_profile_team_video_stmt = $connection_production->prepare($del_profile_team_video_sql);
                        $del_profile_team_video_stmt->bind_param("i", $p_f_sm_v_sm_id);

                        if ($del_profile_team_video_stmt->execute()) {

                            clearVideoProfileAdminId($video_id);

                            $update_string = "video id: ".$video_id." :: unassign profile/team ids: ".$profile_id."/".$team_id;
                            if ($p_f_sm_deleted == true) {
                                $update_string .= ", deleted p_f_sm_id: ".$p_f_sm_id;
                            } else {
                                $update_string .= ", p_f_sm_id: ".$p_f_sm_id." - not deleted";
                            }
                            $update_string .= ", deleted p_f_sm_v_sm id: ".$p_f_sm_v_sm_id;
                            $update_string .= ", cleared profileAdminId on the video";

                            insertChange($_SESSION['account_id'], 'video', 'unassign profile/team', $video_id, $update_string);
                            header("location: categories.php?source=update_video&video_id=".$video_id."#profiles");
                        } else {
                            echo "<h3 style='color:red'>Something went wrong</h3>";
                        }
                        $del_profile_team_video_stmt->close();
                    } else {
                        echo "<h3 style='color:red'>Could not find profile_franchise_sm_video_sm id</h3>";
                    }
                } else {
                    echo "<h3 style='color:red'>Something went wrong</h3>";
                }
            } else {
                echo "<h3 style='color:red'>Could not find profile_franchise_sm_video_sm id</h3>";
            }
        } else {
            echo "<h3 style='color:red'>Could not find profile_franchise_sm id</h3>";
        }
    }
    #sets profileAdminId to NULL on selected video
    function clearVideoProfileAdminId($video_id) {
        global $connection_production;
        $clear_profileAdminId_sql = "UPDATE video SET profileAdminId=NULL WHERE video.id=?";
        $clear_profileAdminId_stmt = $connection_production->prepare($clear_profileAdminId_sql);
        $clear_profileAdminId_stmt->bind_param("i", $video_id);
        $status = $clear_profileAdminId_stmt->execute();
        $clear_profileAdminId_stmt->close();
        return $status;
    }
    #unlink a profile from a video (video_linked table)
    function unassignVideoLinkedProfile() {
        global $connection_production;
        if (isset($_POST['unassign_linked_profile'])) {
            $video_id = $_POST['video_id'];
            $profile_id = $_POST['profile_id'];

            $find_profile_link_sql = "SELECT * FROM video_linked WHERE videoId=? AND profileId=?";
            $find_profile_link_stmt = $connection_production->prepare($find_profile_link_sql);
            $find_profile_link_stmt->bind_param("ii", $video_id, $profile_id);
            $find_profile_link_stmt->execute();
            $row_find_profile_link = $find_profile_link_stmt->get_result()->fetch_assoc();
            $find_profile_link_stmt->close();

            if (!!$row_find_profile_link['id']) {
                $video_linked_id = $row_find_profile_link['id'];

                $del_profile_link_sql = "DELETE FROM video_linked WHERE id=?";
                $del_profile_link_stmt = $connection_production->prepare($del_profile_link_sql);
                $del_profile_link_stmt->bind_param("i", $video_linked_id);

                if ($del_profile_link_stmt->execute()) {
                    $update_string = "video id: ".$video_id." :: unassign profile id: ".$profile_id.", deleted video_linked id: ".$video_linked_id;
                    insertChange($_SESSION['account_id'], 'video', 'unassign linked profile', $video_id, $update_string);
                    header("location: categories.php?source=update_video&video_id=".$video_id."#profiles");
                } else {
                    echo "<h3 style='color:red'>Something went wrong</h3>";
                }
                $del_profile_link_stmt->close();
            } else {
                echo "<h3 style='color:red'>Entry not found in video_linked table</h3>";
            }
        }
    }
    #Given a reference link and the source_id, compute thumbnail url
    #Currently only works for youtube, vimeo, dailymotion
    function computeThumbString($reference, $source_id) {
        $thumbstring = "";
        $tmp = explode("/", $reference);
        $tmp = explode("=", end($tmp));
        $parameter = end($tmp);
        if ($source_id == 1) {
            //Youtube
            $thumbstring = "https://img.youtube.com/vi/".$parameter."/mqdefault.jpg";
        } else if ($source_id == 2) {
            //Vimeo
            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$parameter.".php"));
            if (!!$hash[0]['thumbnail_medium']) {
                $thumbstring = $hash[0]['thumbnail_medium'];
            }
        } else if ($source_id == 3) {
            //Dailymotion
            //not sure if this fully works because the link redirects
                //to the actual thumbnail link
            $position = strpos($post_reference, "video");
            if (!!$position) {
                $thumbstring = substr_replace($post_reference, "thumbnail/", $position, 0);
            }
        }
        return $thumbstring;
    }
?>



<?php
// DELETE VIDEO FUNCTIONS


    #Delete a video and the team association from the database (table 1)
    function deleteTeamVideo() {
        global $connection_production;
        if (isset($_POST['delete_video_team'])) {
            if (!empty($_POST['delete_video_id'])) {
                $p_f_sm_v_sm_id = $_POST['p_f_sm_v_sm_id'];
                $team_id = $_POST['team_id'];
                $delete_video_id = $_POST['delete_video_id'];
                $profile_id = $_POST['profile_id_set'];

                $stmt1 = $connection_production->prepare("delete from `profile_franchise_sm_video_sm` WHERE id = ?");
                $stmt1->bind_param("i", $p_f_sm_v_sm_id);

                $stmt2 = $connection_production->prepare("delete from `video` WHERE id = ?");
                $stmt2->bind_param("i", $delete_video_id);

                $update_string = 'deleted team-video id: '.$delete_video_id." - profile id: ".$profile_id.", team id: ".$team_id." :: ";
                $update_string .= "deleted p_f_sm_v_sm id: ".$p_f_sm_v_sm_id.", ";

                if ((deleteEventsHelper($delete_video_id, $update_string) != -1)
                        && ($stmt1->execute()) && ($stmt2->execute())
                        && (unassignAllProfileLinks($delete_video_id, $update_string) != -1)) {

                    insertChange($_SESSION['account_id'], 'video', 'delete video (team)', $delete_video_id, $update_string);
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
    #Delete a video and the activity association from the database (table 2)
    #Untested until 2nd and 3rd queries work
    function deleteActivityVideo() {
        global $connection_production;
        if (isset($_POST['delete_video_activity'])) {
            if (!empty($_POST['delete_video_id'])) {
                $p_a_sm_v_sm_id = $_POST['p_a_sm_v_sm_id'];
                $activity = $_POST['solo_video_activity'];
                $delete_video_id = $_POST['delete_video_id'];
                $profile_id = $_POST['profile_id_set'];

                $stmt1 = $connection_production->prepare("delete from `profile_activity_sm_video_sm` WHERE id = ?");
                $stmt1->bind_param("i", $p_a_sm_v_sm_id);

                $stmt2 = $connection_production->prepare("delete from `video` WHERE id = ?");
                $stmt2->bind_param("i", $delete_video_id);

                $update_string = 'deleted activity-video id: '.$delete_video_id." - profile id: ".$profile_id.", activity: ".$activity." :: ";
                $update_string .= "deleted p_a_sm_v_sm id: ".$p_a_sm_v_sm_id.", ";

                if ((deleteEventsHelper($delete_video_id, $update_string) != -1)
                        && ($stmt1->execute()) && ($stmt2->execute())
                        && (unassignAllProfileLinks($delete_video_id, $update_string) != -1)) {

                    insertChange($_SESSION['account_id'], 'video', 'delete video (activity)', $delete_video_id, $update_string);
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
    #Delete a video and the profile association from the database (table 3)
    #Untested until 2nd and 3rd queries work
    function deleteIndivVideo() {
        global $connection_production;
        if (isset($_POST['delete_video_indiv'])) {
            if (!empty($_POST['delete_video_id'])) {
                $p_v_sm_id = $_POST['p_v_sm_id'];
                $delete_video_id = $_POST['delete_video_id'];
                $profile_id = $_POST['profile_id_set'];

                $stmt1 = $connection_production->prepare("delete FROM `profile_video_sm` WHERE id = ?");
                $stmt1->bind_param("i", $p_v_sm_id);

                $stmt2 = $connection_production->prepare("delete FROM `video` WHERE id = ?");
                $stmt2->bind_param("i", $delete_video_id);

                $update_string = 'deleted video id: '.$delete_video_id." - profile id: ".$profile_id." :: ";
                $update_string .= "deleted p_v_sm id: ".$p_v_sm_id.", ";

                if ((deleteEventsHelper($delete_video_id, $update_string) != -1)
                        && ($stmt1->execute()) && ($stmt2->execute())
                        && (unassignAllProfileLinks($delete_video_id, $update_string) != -1)) {

                    insertChange($_SESSION['account_id'], 'video', 'delete video (individual)', $delete_video_id, $update_string);
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
    #helper function for removing all profile links from a video
    function unassignAllProfileLinks($video_id, &$update_string) {
        global $connection_production;

        $search_linked_profiles_sql = "SELECT profileId AS profile_id, id AS assoc_id FROM video_linked WHERE videoId=?";
        $search_linked_profiles_stmt = $connection_production->prepare($search_linked_profiles_sql);
        $search_linked_profiles_stmt->bind_param("i", $video_id);
        $search_linked_profiles_stmt->execute();
        $search_linked_profiles_result = $search_linked_profiles_stmt->get_result();
        $search_linked_profiles_stmt->close();

        if ($search_linked_profiles_result->num_rows > 0) {
            $unlinked_profiles = array();

            while ($row_linked_profile = $search_linked_profiles_result->fetch_assoc()) {
                $assoc_id = $row_linked_profile['assoc_id'];
                $profile_id = $row_linked_profile['profile_id'];

                $del_profile_link_sql = "DELETE FROM video_linked WHERE id=?";
                $del_profile_link_stmt = $connection_production->prepare($del_profile_link_sql);
                $del_profile_link_stmt->bind_param("i", $assoc_id);

                if ($del_profile_link_stmt->execute()) {
                    $del_profile_link_stmt->close();
                    array_push($unlinked_profiles, $profile_id);
                } else {
                    $del_profile_link_stmt->close();
                    return -1;
                }
            }
            $update_string .= ", unassign linked profiles id(s): ".implode('/', $unlinked_profiles);
            return 1;
        } else {
            return 0;
        }
    }
?>
