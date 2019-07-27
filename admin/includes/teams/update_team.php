<!--
======================================================================
 TEAM INFO
======================================================================
-->
<form action="" method="post">
    <div class="form-group">
        <?php
            if (isset($_GET['team_id'])) {
                $team_id = $_GET['team_id'];

                $stmt = "SELECT DISTINCT team.id AS team_id, team.name AS team_name, team.locale AS locale, team.description AS team_description, alternate_name.alternateName AS nickname, team.status AS team_status, alternate_name.id AS alternate_name_id
                    FROM team
                    LEFT JOIN team_alternate_name_sm ON team_alternate_name_sm.teamId = team.id
                    LEFT JOIN alternate_name ON alternate_name.id = team_alternate_name_sm.alternateNameId
                    WHERE team.id=?";

                // $stmt = "SELECT DISTINCT team.id AS team_id, team.name AS team_name, team.locale AS locale, team.description AS team_description, alternate_name.alternateName AS nickname, activity.id AS activity_id, activity.activity AS activity, entity.name AS entity_name, entity.id AS entity_id
                //     FROM image
                //     LEFT JOIN activity_image_sm ON image.id=activity_image_sm.imageId AND imageTypeId=3
                //     LEFT JOIN activity ON activity.id=activity_image_sm.activityId
                //     LEFT JOIN entity_activity_sm ON entity_activity_sm.activityId=activity.id
                //     LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
                //     LEFT JOIN entity ON entity.id=entity_activity_sm.entityId
                //     LEFT JOIN team ON team.entityActivitySmPartitionSmId=entity_activity_sm_partition_sm.id
                //     LEFT JOIN team_location_sm ON team.id=team_location_sm.teamId
                //     LEFT JOIN location ON team_location_sm.locationId=location.id
                //     LEFT JOIN team_alternate_name_sm ON team_alternate_name_sm.teamId = team.id
                //     LEFT JOIN  alternate_name ON alternate_name.id = team_alternate_name_sm.alternateNameId
                //     LEFT JOIN franchise_team_sm ON team.id=franchise_team_sm.teamId
                //     WHERE team.id=?";

                $prepared = $connection_production->prepare($stmt);
                $prepared->bind_param('i', $team_id);
                $result = $prepared->execute();
                $result_of_query = $prepared->get_result();

                while ($row = $result_of_query->fetch_assoc()) {
                    $team_id = $row['team_id'];
                    $locale = $row['locale'];
                    $team_name = $row['team_name'];
                    $nickname = $row['nickname'];
                    $team_description = $row['team_description'];
                    $team_status = $row['team_status'];
                    $alternate_name_id = $row['alternate_name_id'];
            ?>
                    <input type="hidden" name="team_id" value="<?php echo $team_id; ?>">
                    <input type="hidden" name="alternate_name_id" value="<?php echo $alternate_name_id; ?>">
                    <div class="row">
                        <div class="form-group col-xs-4">
                            <label for="locale">Locale</label>
                            <input value="<?php echo $locale; ?>" type="text" class="form-control" name="locale" >
                        </div>
                        <div class="form-group col-xs-4">
                            <label for="name">Name</label>
                            <input value="<?php echo $team_name; ?>" type="text" class="form-control" name="name" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-3">
                            <label for="nickname">Nickname</label>
                            <input value="<?php echo $nickname; ?>" type="text" class="form-control" name="nickname" >
                        </div>
                        <div class="form-group col-xs-1">
                            <label for="team_status">Status</label>
                            <select name='team_status'>;
                                <option value="complete" <?php if ($team_status == "complete") echo "SELECTED";?> >Complete</option>
                                <option value="incomplete" <?php if ($team_status == "incomplete") echo "SELECTED";?> >Incomplete</option>
                                <option value="disabled" <?php if ($team_status == "disabled") echo "SELECTED";?> >Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="team_description">team Description</label>
                        <textarea class="form-control" name="team_description" id="" cols="30" rows="10"><?php echo $team_description; ?></textarea>
                    </div>
                    <div class="form-group">
                        <input class="btn btn-primary" type="submit" name="update_team" value="Update team">
                    </div>
        <?php } }  ?>
        <?php updateTeamInfo(); ?>
    </div>
</form>

<!--
======================================================================
 PARTITION
======================================================================
-->
<div class="form-group">
    <label id="add_league_partition">Add Team Partition</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addLeaguePartition(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Team Entity</th>
                <th class="col-md-1">Check Partition</th>
                <th class="col-md-1">Team Partition</th>
                <th class="col-md-1">Add</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post' action="#add_league_partition">
                    <input type="hidden" name="team_id" value="<?php echo $_GET['team_id']; ?>">
                    <td>
                        <div class="form-group col-xs-3">
                            <select name='league'>;
                                <option value="">choose league</option>
                                <?php
                                    $league_query = "SELECT * FROM entity";
                                    $all_league_query = mysqli_query($connection_production, $league_query);
                                    while ($row = mysqli_fetch_assoc($all_league_query)) {
                                        if (substr($row['description'],0,22) != 'No entity association ') {
                                            $selected = "";
                                            if (isset($_POST['check_team_partition'])) {
                                                if (!!(int)$_POST['league']) {
                                                    $league_check_id = (int)$_POST['league'];
                                                    $league_check_id == $row['id'] ? $selected = "SELECTED" : $selected = "" ;
                                                }
                                            }
                                            echo "<option ".$selected." value=".$row['id'].">".$row['name']." </option>";
                                        }
                                    }
                                 ?>
                            </select>
                        </div>
                    </td>
                    <td>
                        <input class='btn btn-primary' type='submit' name='check_team_partition' value='Check partition'>
                    </td>
                    <?php
                        if (isset($_POST['check_team_partition'])) {
                            if (!!(int)$_POST['league']) {
                                $league_check_id = (int)$_POST['league'];
                    ?>
                    <td>
                        <div class="form-group col-xs-3">
                            <select name='team_partition'>;
                                <option value="">choose partition</option>
                                <?php
                                    $team_partition_query = "SELECT partition_LP.id AS partition_id, partition_LP.partition AS partition_name FROM `partition` AS partition_LP
                                    LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id
                                    LEFT JOIN entity_activity_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
                                    LEFT JOIN entity ON entity.id=entity_activity_sm.entityId WHERE entity.id=".$league_check_id;
                                    $all_team_partition_query = mysqli_query($connection_production, $team_partition_query);
                                    while ($row = mysqli_fetch_assoc($all_team_partition_query)) {
                                        echo "<option value=".$row['partition_id'].">".$row['partition_name']."</option>";
                                    }
                                 ?>
                            </select>
                        </div>
                    </td>
                    <td>
                        <input class='btn btn-primary' type='submit' name='add_team_entity_partition' value='Add'>
                    </td>
                    <?php
                            } else {
                                echo "<h3 style='color:red;'>A league must be chosen</h3>";
                            }
                        }
                    ?>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="league_partition">Team League/Partition</label>
    <table class="table table-striped table-bordered table-hover">
        <?php deleteLeaguePartition(); ?>
        <thead>
            <tr>
                <th>League</th>
                <th>Partition</th>
                <th>Activity</th>
                <th class="col-md-2">Delete</th>
            </tr>
        </thead>
        <tbody>
            <form method='post'>
                <input type="hidden" name="team_id" value="<?php echo $team_id; ?>">
                <?php
                    $stmt = "SELECT DISTINCT activity.activity AS activity, activity.id AS activity_id, partition_LP.partition AS partition_name, partition_LP.id AS partition_id, entity.name AS entity_name, entity.id AS entity_id, franchise.id AS franchise_id, partition_franchise_sm.id AS partition_franchise_sm_id
                    FROM team
                    LEFT JOIN franchise_team_sm ON franchise_team_sm.teamId=team.id
                    LEFT JOIN franchise ON franchise.id=franchise_team_sm.franchiseId
                    LEFT JOIN partition_franchise_sm ON partition_franchise_sm.franchiseId=franchise.id
                    LEFT JOIN `partition` AS partition_LP ON partition_LP.id=partition_franchise_sm.partitionId
                    LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id
                    LEFT JOIN entity_activity_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
                    LEFT JOIN entity ON entity.id = entity_activity_sm.entityId
                    LEFT JOIN activity ON activity.id=entity_activity_sm.activityId
                    WHERE team.id=?";

                    $prepared = $connection_production->prepare($stmt);
                    $prepared->bind_param('i', $team_id);
                    $result = $prepared->execute();
                    $result_of_query = $prepared->get_result();

                    if (!!$result && $result_of_query->num_rows > 0) {
                        while ($row = $result_of_query->fetch_assoc()) {
                            $entity_name = $row['entity_name'];
                            $entity_id = $row['entity_id'];
                            $partition_name = $row['partition_name'];
                            $partition_id = $row['partition_id'];
                            $activity = $row['activity'];
                            $activity_id = $row['activity_id'];
                            $franchise_id = $row['franchise_id'];
                            $partition_franchise_sm_id = $row['partition_franchise_sm_id'];

                            echo "<tr>";
                            echo "<td>".$entity_name."</td>";
                            echo "<td>".$partition_name."</td>";
                            echo "<td>".$activity."</td>";
                            echo "<form method='post'>";
                            echo "<input type='hidden' name='team_id' value=".$team_id.">";
                            echo "<input type='hidden' name='entity_id' value=".$entity_id.">";
                            echo "<input type='hidden' name='entity_name' value=".$entity_name.">";
                            echo "<input type='hidden' name='partition_id' value=".$partition_id.">";
                            echo "<input type='hidden' name='partition_name' value=".$partition_name.">";
                            echo "<input type='hidden' name='activity_id' value=".$activity_id.">";
                            echo "<input type='hidden' name='activity' value=".$activity.">";
                            echo "<input type='hidden' name='franchise_id' value=".$franchise_id.">";
                            echo "<input type='hidden' name='partition_franchise_sm_id' value=".$partition_franchise_sm_id.">";
                            echo "<td>";
                ?>
                            <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_team_entity_partition' value='Delete'>
                <?php
                            echo "</td>";
                            echo "</form>";
                            echo "</tr>";
                        }
                    }
                ?>
            </form>
        </tbody>
    </table>
</div>

<!--
======================================================================
 IMAGE
======================================================================
-->
<div class="form-group">
    <label id="add_image">Add Image</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addImage("team"); ?>
        <thead>
            <tr>
                <th class="col-md-1">Image Id (if exists)</th>
                <th>Image Name</th>
                <th class="col-md-2">Add image</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post'>
                    <input type="hidden" name="team_id_set" value="<?php echo $_GET['team_id']; ?>">
                    <td><input class="form-control" type='number' name='image_id' value=""></td>
                    <td><input class="form-control" type='text' name='image_name' value=""></td>
                    <td><input class='btn btn-primary' type='submit' name='add_image' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="image">Image</label>
    <table class="table table-striped table-bordered table-hover">
        <?php updateImage("team"); ?>
        <?php deleteImage("team"); ?>
        <thead>
            <tr>
                <th class="col-md-2">ID</th>
                <th>Image Name</th>
                <th class="col-md-2">Update</th>
                <th class="col-md-2">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $img_names = array();
                if (isset($_GET['team_id'])) {
                    $team_id = $_GET['team_id'];

                    $stmt_image = "SELECT DISTINCT image.imageName AS imageName, image.id AS image_id, team_image_sm.id AS team_image_sm_id FROM team
                        LEFT JOIN team_image_sm ON team.id=team_image_sm.teamId
                        LEFT JOIN image ON team_image_sm.imageId=image.id
                        WHERE team.id=?";
                    $prepared_image = $connection_production->prepare($stmt_image);
                    $prepared_image->bind_param('i', $team_id);
                    $result_image = $prepared_image->execute();
                    $result_of_query_image = $prepared_image->get_result();

                    if (!!$result_image && $result_of_query_image->num_rows > 0) {
                        while ($row_image = $result_of_query_image->fetch_assoc()) {
                            if (!!$row_image['image_id']) {
                                $query_imageName = $row_image['imageName'];
                                $query_team_image_sm_id = $row_image['team_image_sm_id'];
                                $query_image_id = $row_image['image_id'];
                                array_push($img_names, $query_imageName);

                                echo "<form method='post'>";
                                echo "<tr>";
                                echo "<td>".$query_image_id."</td>";
                                echo "<td><div class='form-group'>";
                                echo "<input value='".$query_imageName."' type='text' class='form-control' name='image_name'>";
                                echo "</div></td>";

                                echo "<input type='hidden' name='image_id' value=".$query_image_id.">";
                                echo "<input type='hidden' name='team_id_set' value=".$team_id.">";
                                echo "<td><input class='btn btn-info' type='submit' name='update_image' value='update'></td>";
                                echo "</form>";

                                echo "<form method='post'>";
                                echo "<input type='hidden' name='delete_image_id' value=".$query_image_id.">";
                                echo "<input type='hidden' name='delete_team_image_sm_id' value=".$query_team_image_sm_id.">";
                                echo "<input type='hidden' name='team_id_set' value=".$team_id.">";
                                echo "<td>";
            ?>
                                <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_image' value='Delete'>
            <?php
                                echo "</td>";
                                echo "</form>";
                                echo "</tr>";
                            }
                        }
                    }
                }
            ?>
        </tbody>
    </table>
    <?php showImagePreviews($img_names, "team"); ?>
</div>

<!--
======================================================================
 LOCATION
======================================================================
-->
<div class="form-group">
    <label id="add_location">Add Location</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addLocation("team"); ?>
        <thead>
            <tr>
                <th>City</th>
                <th>State/province</th>
                <th>Country</th>
                <th class="col-md-2">Add location</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post'>
                    <input type="hidden" name="team_id_set" value="<?php echo $_GET['team_id']; ?>">
                    <td><input class="form-control" type='text' name='add_location_city' value=""></td>
                    <td><input class="form-control" type='text' name='add_location_state_province' value=""></td>
                    <td><input class="form-control" type='text' name='add_location_country' value=""></td>
                    <td><input class='btn btn-primary' type='submit' name='add_location' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="location">Location</label>
    <table class="table table-striped table-bordered table-hover">
        <?php updateLocation("team"); ?>
        <?php deleteLocation("team"); ?>
        <thead>
            <tr>
                <th>ID</th>
                <th>City</th>
                <th>State/province</th>
                <th>Country</th>
                <th>Update</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $team_id_set = $_GET['team_id'];

                $stmt_location = "SELECT DISTINCT location.id AS location_id, location.city AS city, location.stateProvince AS stateProvince, location.country AS country, team_location_sm.id AS team_location_sm_id FROM team LEFT JOIN team_location_sm ON team_location_sm.teamId=team.id LEFT JOIN location ON team_location_sm.locationId=location.id WHERE team.id=?";
                $prepared_location = $connection_production->prepare($stmt_location);
                $prepared_location->bind_param('i', $team_id);
                $result_location = $prepared_location->execute();
                $result_of_query_location = $prepared_location->get_result();
                if (!!$result_location && $result_of_query_location->num_rows > 0) {
                    while ($row_location = mysqli_fetch_assoc($result_of_query_location)) {
                        if (!!$row_location['location_id']) {
                            $location_id = $row_location['location_id'];
                            $query_team_location_sm_id = $row_location['team_location_sm_id'];
                            $city = $row_location['city'];
                            $stateProvince = $row_location['stateProvince'];
                            $country = $row_location['country'];

                            echo "<tr>";
                            echo "<form method='post'>";
                            echo "<td>".$location_id."</td>";
                            echo "<td><div class='form-group'><input value='".$city."' type='text' class='form-control' name='update_location_city'></div></td>";
                            echo "<td><div class='form-group'><input value='".$stateProvince."' type='text' class='form-control' name='update_location_state_province'></div></td>";
                            echo "<td><div class='form-group'><input value='".$country."' type='text' class='form-control' name='update_location_country' ></div></td>";
                            echo "<input type='hidden' name='update_location_id' value=".$location_id.">";
                            echo "<td><input class='btn btn-info' type='submit' name='update_location' value='update'></td>";

                            echo "<form method='post'><input type='hidden' name='delete_location_id' value=".$location_id."><input type='hidden' name='delete_team_location_sm_id' value=".$query_team_location_sm_id."><input type='hidden' name='team_id_set' value=".$team_id_set.">";
                            echo "<td>";
            ?>
                            <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_location' value='Delete'>
            <?php
                            echo "</form>";
                            echo "</td>";
                            echo "</form>";
                            echo "</tr>";
                        }
                    }
                }
            ?>
        </tbody>
    </table>
</div>


<!--
======================================================================
 PROFILES ASSOCIATED WITH TEAM
======================================================================
-->
<div class="form-group">
    <label id="add_profile">Add Profile</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addProfileToTeam(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Profile ID</th>
                <th>Add profile</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post'>
                    <input type="hidden" name="team_id_set" value="<?php echo $_GET['team_id']; ?>">
                    <td><input class="form-control" type='number' name='profile_id' value=""></td>
                    <td><input class='btn btn-primary' type='submit' name='add_profile_to_team' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="profiles">Profiles</label>
    <table class="table table-striped table-bordered table-hover">
        <?php //addTeamTeamPosition(); ?>
        <?php //deleteTeamTeamPosition(); ?>
        <?php //addTeamTeamPeriod(); ?>
        <?php //deleteTeamTeamPeriod(); ?>
        <?php deleteAthleteFromTeamProfile(); ?>
        <thead>
            <tr>
                <th class="col-sm">ID</th>
                <th class="col-sm">Name</th>
                <th class="col-sm">Roll</th>
                <th class="col-sm">Position/specialty</th>
                <th class="col-sm">Period</th>
                <th class="col-sm">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $team_id = $_GET['team_id'];
                $team_profile_count_sql = "SELECT DISTINCT profile.id AS profile_id, participant.firstName AS first_name,
                    participant.lastName AS last_name, participant_suffix.suffix AS suffix, profile.profileTypeId AS profile_type,
                    profile_franchise_sm.id AS profile_franchise_sm_id FROM profile
                    LEFT JOIN profile_franchise_sm ON profile_franchise_sm.profileId=profile.id
                    LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
                    LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
                    LEFT JOIN team ON team.id=franchise_team_sm.teamId
                    LEFT JOIN participant ON profile.participantId=participant.id
                    LEFT JOIN participant_suffix ON participant_suffix.id=participant.participantSuffixId
                    WHERE team.id = ?";
                $team_profile_count_stmt = $connection_production->prepare($team_profile_count_sql);
                $team_profile_count_stmt->bind_param("i", $team_id);
                $team_profile_count_stmt->execute();
                $team_profile_count_result = $team_profile_count_stmt->get_result();
                $team_profile_count_stmt->close();

                $count = mysqli_num_rows($team_profile_count_result);
                $count = ceil($count/20);

                if (isset($_GET['profile_page'])) {
                    $pageinate = $_GET['profile_page'];
                } else {
                    $pageinate = "";
                }
                if ($pageinate == "" || $pageinate == 1) {
                    $pageinate_1 = 0;
                } else {
                    $pageinate_1 = ($pageinate * 20) - 20;
                }
                findAllTeamProfiles($team_id, $activity_id, $pageinate_1);
            ?>
        </tbody>
    </table>
    <?php
        $video_page_param = isset($_GET['video_page']) ? "&video_page=".$_GET['video_page'] : "";
        echo "<ul class='pager'>";
            for ($i = 1; $i <= $count; $i++) {
               if ($i == $pageinate || $pageinate == '0') {
                    echo "<li><a class='active_link' href='categories.php?source=team_update&profile_page=".$i.$video_page_param."&team_id=".$team_id."#profiles'>".$i."</a></li>";
                } else {
                    echo "<li><a href='categories.php?source=team_update&profile_page=".$i.$video_page_param."&team_id=".$team_id."#profiles'>".$i."</a></li>";
                }
            }
        echo "</ul>";
    ?>
</div>

<!--
======================================================================
 TEAM VIDEOS
======================================================================
-->
<div class="form-group">
    <label id="add_video">Add Team Video</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addVideoToTeam(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Video ID</th>
                <th class="col-md-1">Profile ID</th>
                <th>Add Video</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                    if (isset($_GET['team_id'])) {
                ?>
                    <form method='post'>
                        <input type='hidden' name='team_id' value=<?php echo $_GET['team_id']; ?> />
                        <td><input class='form-control' type='number' name='video_id' value='' required /></td>
                        <td><input class='form-control' type='number' name='profile_id' value='' required /></td>
                        <td><input class='btn btn-primary' type='submit' name='add_team_video' value='Add' /></td>
                    </form>
                <?php } ?>
            </tr>
        </tbody>
    </table>

    <label id="videos">Team Videos</label>
    <table class="table table-striped table-bordered table-hover">
        <?php deleteTeamVideoAssignedToTeam(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Video ID</th>
                <th class="col-md-1">Profile ID</th>
                <th class="col-sm-2">Video Title</th>
                <th>Video Summary</th>
                <th class="col-sm-1">Video Status</th>
                <th class="col-sm-1">Source</th>
                <th class="col-sm-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $team_video_count_sql = "SELECT DISTINCT video.id AS video_id, profile_franchise_sm.profileId AS video_profile_id,
        			video.reference AS reference, video.title AS title, video.summary AS video_summary, video.videoStatus AS video_status,
        			video_source.source AS video_source, profile_franchise_sm_video_sm.id AS p_f_sm_v_sm_id FROM video
        			LEFT JOIN video_source ON video_source.id = video.videoSourceId
        			LEFT JOIN profile_franchise_sm_video_sm ON profile_franchise_sm_video_sm.videoId = video.id
        			LEFT JOIN profile_franchise_sm ON profile_franchise_sm.id = profile_franchise_sm_video_sm.profileFranchiseSmId
        			LEFT JOIN franchise ON franchise.id = profile_franchise_sm.franchiseId
        			LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId = franchise.id
        			WHERE franchise_team_sm.teamId = ?";
                $team_video_count_stmt = $connection_production->prepare($team_video_count_sql);
                $team_video_count_stmt->bind_param("i", $team_id);
                $team_video_count_stmt->execute();
                $team_video_count_result = $team_video_count_stmt->get_result();
                $team_video_count_stmt->close();

                $count = mysqli_num_rows($team_video_count_result);
                $count = ceil($count/20);

                if (isset($_GET['video_page'])) {
                    $pageinate = $_GET['video_page'];
                } else {
                    $pageinate = "";
                }
                if ($pageinate == "" || $pageinate == 1) {
                    $pageinate_1 = 0;
                } else {
                    $pageinate_1 = ($pageinate * 20) - 20;
                }
                findAllTeamVideos($team_id, $pageinate_1);
            ?>
        </tbody>
    </table>
    <?php
        echo "<ul class='pager'>";
            $profile_page_param = isset($_GET['profile_page']) ? "&profile_page=".$_GET['profile_page'] : "";
            for ($i = 1; $i <= $count; $i++) {
               if ($i == $pageinate || $pageinate == '0') {
                    echo "<li><a class='active_link' href='categories.php?source=team_update&video_page=".$i.$profile_page_param."&team_id=".$team_id."#videos'>".$i."</a></li>";
                } else {
                    echo "<li><a href='categories.php?source=team_update&video_page=".$i.$profile_page_param."&team_id=".$team_id."#videos'>".$i."</a></li>";
                }
            }
        echo "</ul>";
    ?>
</div>
