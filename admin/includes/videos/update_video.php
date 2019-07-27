<form action="" method="post">
    <?php
        updateVideoInfo();
        if (!empty($_GET['video_id'])) {
            $video_id = $_GET['video_id'];
            $stmt = "SELECT DISTINCT * FROM video WHERE video.id = ?";
            $prepared = $connection_production->prepare($stmt);
            $prepared->bind_param("i", $video_id);
            $prepared->execute();
            $result = $prepared->get_result();
            $prepared->close();
            $row = $result->fetch_assoc();

            $reference = $row['reference'];
            $thumbstring = $row['thumbString'];
            $sourceId = $row['videoSourceId'];
            $title = $row['title'];
            $summary = $row['summary'];
            $status = $row['videoStatus'];
            if (!strcasecmp($status, "complete")) {
                $stat_int = 1;
            } else if (!strcasecmp($status, "incomplete")) {
                $stat_int = 2;
            } else if (!strcasecmp($status, "deadlink")) {
                $stat_int = 3;
            } else {
                $stat_int = -1;
            }
    ?>
            <div class="form-group">
                <input type='hidden' name='video_id' value="<?php echo $video_id; ?>" />
                <div class="row">
                    <div class="form-group col-xs-4">
                        <label for="reference">Reference</label>
                        <input class="form-control" type="text" name="reference" value="<?php echo $reference; ?>" required pattern='.*\S+.*' />
                    </div>
                    <div class="form-group col-xs-2">
                        <label for="source_id" style="margin-right:2vw">Source ID</label>
                        <select name="source_id" required>
                            <?php
                                $source_query = "SELECT id, source FROM video_source
                                    ORDER BY video_source.id ASC";
                                $source_result = mysqli_query($connection_production, $source_query);
                                if (empty($sourceId)) {
                                    echo "<option value='' disabled selected>Choose source</option>";
                                }
                                while ($row_source = mysqli_fetch_assoc($source_result)) {
                                    $query_source_id = $row_source['id'];
                                    $query_source_name = $row_source['source'];
                                    $selected = (!empty($sourceId)) && ($query_source_id == $sourceId);
                                    echo "<option value=".$query_source_id.($selected ? " selected" : "").">"
                                        .$query_source_id.": ".$query_source_name."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-xs-3">
                        <label for="thumbstring">Thumbnail URL</label>
                        <input class="form-control" type="text" name="thumbstring" value="<?php echo $thumbstring; ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-4">
                        <label for="title">Title</label>
                        <input class="form-control" type="text" name="title" value="<?php echo $title; ?>" required pattern='.*\S+.*' />
                    </div>
                    <div class="form-group col-xs-4">
                        <label for="summary">Summary</label>
                        <input class="form-control" type="text" name="summary" value="<?php echo $summary; ?>" />
                    </div>
                    <div class="form-group col-xs-1">
                        <label for="status">Status</label>
                        <select name="status" required>
                            <?php
                                if ($stat_int == -1) {
                                    echo "<option value='' disabled selected>Choose status</option>";
                                }
                                echo "<option value='complete'".($stat_int == 1 ? " selected" : "").">Complete</option>";
                                echo "<option value='incomplete'".($stat_int == 2 ? " selected" : "").">Incomplete</option>";
                                echo "<option value='deadlink'".($stat_int == 3 ? " selected" : "").">Dead link</option>";
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <input class="btn btn-primary" type="submit" name="update_video_info" value="Update Info" />
            </div>
    <?php
        } else {
            echo "<h3 style='color:red;'>please select a video</h3>";
        }
    ?>
</form>

<div class="form-group">
    <label id="add_event">Add Event</label>
    <?php
        if (isset($_POST['event_add_mode'])) {
            $add_mode = $_POST['event_add_mode'];
        } else {
            $add_mode = 1;
        }
    ?>
    <form method='post' style='display:inline'>
        <select name='event_add_mode' onchange='this.form.submit()'>
            <option value=1<?php if ($add_mode == 1) echo " selected"; ?>>Add new event</option>
            <option value=2<?php if ($add_mode == 2) echo " selected"; ?>>Add existing event by ID</option>
        </select>
    </form>
    <table class="table table-striped table-bordered table-hover">
        <?php
            addVideoExistingEvent();
            addVideoNewEvent();
        ?>
        <thead>
            <tr>
                <th class="col-md-2">Event ID</th>
                <th>Event Name</th>
                <th>Event Description</th>
                <th class="col-md-2">Add Event</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                    if (!empty($_GET['video_id'])) {
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='video_id' value=".$_GET['video_id']." />";
                        echo "<td><input class='form-control' type='".($add_mode==2 ? "number" : "hidden")
                            ."' style='width:100%' name='event_id' value='' required /></td>";
                        echo "<td><input class='form-control' type='".($add_mode==1 ? "text" : "hidden")
                            ."' style='width:100%' name='event_name' value='' required pattern='.*\S+.*' /></td>";
                        echo "<td><input class='form-control' type='".($add_mode==1 ? "text" : "hidden")
                            ."' style='width:100%' name='event_desc' value='' /></td>";
                        $btn_name = ($add_mode == 2 ? "add_video_existing_event" : "add_video_new_event");
                        echo "<td><input class='btn btn-primary' type='submit' name='".$btn_name."' value='Add' /></td>";
                        echo "</form>";
                    }
                ?>
            </tr>
        </tbody>
    </table>

    <label id="event">Event</label>
    <table class="table table-striped table-bordered table-hover">
        <?php
            updateVideoEvent();
            deleteVideoEvent();
        ?>
        <thead>
            <tr>
                <th class="col-md-2">Event ID</th>
                <th>Event Name</th>
                <th>Event Description</th>
                <th class="col-md-1">Update</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if (!empty($_GET['video_id'])) {
                    $video_id = $_GET['video_id'];
                    $input_video_id = "<input type='hidden' name='video_id' value=".$video_id." />";

                    $stmt_event = "SELECT DISTINCT event.id AS event_id, event.event AS event_name,
                        event.description AS event_desc, event_video_sm.id AS e_v_sm_id FROM event
                        LEFT JOIN event_video_sm ON event_video_sm.eventId=event.id
                        LEFT JOIN video ON video.id=event_video_sm.videoId
                        WHERE video.id=?";
                    $prepared_event = $connection_production->prepare($stmt_event);
                    $prepared_event->bind_param("i", $video_id);
                    $prepared_event->execute();
                    $result_event = $prepared_event->get_result();
                    $prepared_event->close();

                    if ($result_event->num_rows > 0) {
                        while ($row_event = $result_event->fetch_assoc()) {
                            if (!!$row_event['event_id']) {
                                $event_name = $row_event['event_name'];
                                $event_desc = $row_event['event_desc'];
                                $e_v_sm_id = $row_event['e_v_sm_id'];
                                $event_id = $row_event['event_id'];
                                $input_event_id = "<input type='hidden' name='event_id' value=".$event_id." />";

                                echo "<tr>";
                                echo "<form method='post'>";
                                echo $input_video_id;
                                echo $input_event_id;
                                echo "<td>".$event_id."</td>";
                                echo "<td><input class='form-control' type='text' name='event_name' value='".$event_name."' required pattern='.*\S+.*' /></td>";
                                echo "<td><input class='form-control' type='text' name='event_desc' value='".$event_desc."' /></td>";
                                echo "<td><input class='btn btn-info' type='submit' name='update_event' value='Update' /></td>";
                                echo "</form>";
                                //Separate form for delete:
                                echo "<td><form method='post'>";
                                echo $input_video_id;
                                echo $input_event_id;
                                echo "<input type='hidden' name='delete_video_event_sm_id' value=".$e_v_sm_id." />";
            ?>
                                <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_event' value='Delete' />
            <?php
                                echo "</form></td>";
                                echo "</tr>";
                            }
                        }
                    } else {
                        echo "<tr><td>NO EVENT ADDED</td></tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>

<div class="form-group">
    <label id="assign_profile">Assign Profile</label>
    <table class="table table-striped table-bordered table-hover">
        <?php assignProfileToVideo(); ?>
        <thead>
            <tr>
                <th class="col-md-2">Profile ID</th>
                <th>Activity ID (only for main profile)</th>
                <th>Team ID (only for main profile)</th>
                <th class="col-md-2">Assign Profile</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                    if (isset($_GET['video_id'])) {
                ?>
                    <form method='post'>
                        <input type='hidden' name='video_id' value=<?php echo $_GET['video_id']; ?> />
                        <td><input class='form-control' type='number' name='profile_id' value='' required /></td>
                        <td><input class='form-control' type='number' name='activity_id' value='' /></td>
                        <td><input class='form-control' type='number' name='team_id' value='' /></td>
                        <td><input class='btn btn-primary' type='submit' name='assign_profile' value='Submit' /></td>
                    </form>
                <?php } ?>
            </tr>
        </tbody>
    </table>

    <label id="profiles">Profiles</label>
    <table class="table table-striped table-bordered table-hover">
        <?php
            unassignVideoMainProfile();
            unassignVideoLinkedProfile();
        ?>
        <thead>
            <tr>
                <th class="col-md-2">Profile ID</th>
                <th>Activity ID</th>
                <th>Team ID</th>
                <th class='col-md-2'>Delete Profile Assignment</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if (!empty($_GET['video_id'])) {
                    $video_id = $_GET['video_id'];
                    $input_video_id = "<input type='hidden' name='video_id' value=".$video_id." />";

                    $video_main_profile_sql = "SELECT video.profileAdminId AS main_profile_id FROM video WHERE video.id=?";
                    $video_main_profile_stmt = $connection_production->prepare($video_main_profile_sql);
                    $video_main_profile_stmt->bind_param("i", $video_id);
                    $video_main_profile_stmt->execute();
                    $row_video_main_profile = $video_main_profile_stmt->get_result()->fetch_assoc();
                    $video_main_profile_stmt->close();

                    if (!!$row_video_main_profile['main_profile_id']) {
                        $main_profile_id = $row_video_main_profile['main_profile_id'];
                        $activity_id = NULL;
                        $team_id = NULL;

                        $check_solo_search_sql = "SELECT * FROM profile_video_sm WHERE videoId=? AND profileId=?";
                        $check_solo_search_stmt = $connection_production->prepare($check_solo_search_sql);
                        $check_solo_search_stmt->bind_param("ii", $video_id, $main_profile_id);
                        $check_solo_search_stmt->execute();
                        $check_solo_search_result = $check_solo_search_stmt->get_result();
                        $check_solo_search_stmt->close();

                        $check_activity_search_sql = "SELECT activityId AS activity_id FROM profile_activity_sm
                            LEFT JOIN profile_activity_sm_video_sm ON profile_activity_sm_video_sm.profileActivitySmId=profile_activity_sm.id
                            WHERE profile_activity_sm_video_sm.videoId=? AND profile_activity_sm.profileId=?";
                        $check_activity_search_stmt = $connection_production->prepare($check_activity_search_sql);
                        $check_activity_search_stmt->bind_param("ii", $video_id, $main_profile_id);
                        $check_activity_search_stmt->execute();
                        $row_activity_search = $check_activity_search_stmt->get_result()->fetch_assoc();
                        $check_activity_search_stmt->close();

                        $check_team_search_sql = "SELECT franchiseId AS team_id FROM profile_franchise_sm
                            LEFT JOIN profile_franchise_sm_video_sm ON profile_franchise_sm_video_sm.profileFranchiseSmId=profile_franchise_sm.id
                            WHERE profile_franchise_sm_video_sm.videoId=? AND profile_franchise_sm.profileId=?";
                        $check_team_search_stmt = $connection_production->prepare($check_team_search_sql);
                        $check_team_search_stmt->bind_param("ii", $video_id, $main_profile_id);
                        $check_team_search_stmt->execute();
                        $row_team_search = $check_team_search_stmt->get_result()->fetch_assoc();
                        $check_team_search_stmt->close();

                        if (!!$row_activity_search['activity_id']) {
                            $activity_id = $row_activity_search['activity_id'];
                        }
                        if (!!$row_team_search['team_id']) {
                            $team_id = $row_team_search['team_id'];
                        }

                        echo "<tr><td>Main Profile</td></tr>";
                        echo "<tr>";
                        echo "<form method='post'>";
                        echo $input_video_id;
                        echo "<input type='hidden' name='profile_id' value=".$main_profile_id." />";
                        if (!empty($activity_id)) {
                            echo "<input type='hidden' name='activity_id' value=".$activity_id." />";
                        }
                        if (!empty($team_id)) {
                            echo "<input type='hidden' name='team_id' value=".$team_id." />";
                        }

                        echo "<td><a href='categories.php?source=profile_update_athlete&profile_id=".$main_profile_id."'>".$main_profile_id."</a></td>";
                        $link_1 = "<a href='categories.php?source=sports_update&activity_id=".$activity_id."'>";
                        echo "<td>".(empty($activity_id) ? "N/A</td>" : $link_1.$activity_id."</a></td>");
                        $link_2 = "<a href='categories.php?source=team_update&team_id=".$team_id."'>";
                        echo "<td>".(empty($team_id) ? "N/A</td>" : $link_2.$team_id."</a></td>");
                        echo "<td>";
        ?>
                        <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='unassign_main_profile' value='Delete' />
        <?php
                        echo "</td>";
                        echo "</form>";
                        echo "</tr>";
                    } else {
                        echo "<tr><td>NO MAIN PROFILE</td></tr>";
                    }

                    $linked_videos_search_sql = "SELECT DISTINCT profileId AS profile_id FROM video_linked WHERE videoId=?";
                    $linked_videos_search_stmt = $connection_production->prepare($linked_videos_search_sql);
                    $linked_videos_search_stmt->bind_param("i", $video_id);
                    $linked_videos_search_stmt->execute();
                    $linked_videos_search_result = $linked_videos_search_stmt->get_result();
                    $linked_videos_search_stmt->close();

                    if ($linked_videos_search_result->num_rows > 0) {
                        echo "<tr><td>Linked Profiles::</td></tr>";
                        while ($row_linked_video = $linked_videos_search_result->fetch_assoc()) {
                            $profile_id = $row_linked_video['profile_id'];

                            echo "<tr>";
                            echo "<form method='post'>";
                            echo $input_video_id;
                            echo "<input type='hidden' name='profile_id' value=".$profile_id." />";

                            echo "<td><a href='categories.php?source=profile_update_athlete&profile_id=".$profile_id."'>".$profile_id."</a></td>";
                            echo "<td colspan=2 />";
                            echo "<td>";
            ?>
                            <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='unassign_linked_profile' value='Delete' />
            <?php
                            echo "</td>";
                            echo "</form>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td>NO LINKED PROFILES</td></tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>
