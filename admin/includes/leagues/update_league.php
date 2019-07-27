<!--
======================================================================
 LEAGUE INFO
======================================================================
-->
<form action="" method="post">
    <div class="form-group">
        <?php
            updateEntityInfo();

            if (isset($_GET['entity_id'])) {
                $entity_id_set = $_GET['entity_id'];

                $stmt = "SELECT entity.id AS entity_id, entity.name AS entity_name, entity.description AS entity_description,
                    entity_activity_sm.activityId AS entity_activity_id, entity_activity_sm.id AS entity_activity_sm_id,
                    alternate_name.alternateName AS alternateName, alternate_name.id AS entity_alternate_name_id,
                    entity_alternate_name_sm.id AS entity_alternate_name_sm_id FROM entity
                    LEFT JOIN entity_activity_sm ON entity_activity_sm.entityId=entity.id
                    LEFT JOIN entity_alternate_name_sm ON entity_alternate_name_sm.entityId=entity.id
                    LEFT JOIN alternate_name ON alternate_name.id=entity_alternate_name_sm.alternateNameId
                    WHERE entity.id=?";
                $prepared = $connection_production->prepare($stmt);
                $prepared->bind_param('i', $entity_id_set);
                $result = $prepared->execute();
                $result_of_query = $prepared->get_result();
                $row = $result_of_query->fetch_assoc();

                $entity_name = $row['entity_name'];
                $entity_description = $row['entity_description'];
                $entity_alternate_name = $row['alternateName'];
                $entity_activity_id = $row['entity_activity_id'];
                $entity_alternate_name_id = $row['entity_alternate_name_id'];
                $entity_activity_sm_id = $row['entity_activity_sm_id'];
                $entity_alternate_name_sm_id = $row['entity_alternate_name_sm_id'];

                // $leagueType = $row['leagueTypeId'] == 1  || $row['leagueTypeId'] == 2 ? 'athlete' : 'coach';
        ?>
                <input type="hidden" name="entity_id_set" value="<?php echo $entity_id_set; ?>">
                <input type="hidden" name="entity_alternate_name_id" value="<?php echo $entity_alternate_name_id; ?>">
                <input type="hidden" name="entity_activity_sm_id" value="<?php echo $entity_activity_sm_id; ?>">
                <input type="hidden" name="entity_alternate_name_sm_id" value="<?php echo $entity_alternate_name_sm_id; ?>">
                <div class="row">
                    <div class="form-group col-xs-4">
                        <label for="entity_name">Name</label>
                        <input value="<?php echo $entity_name; ?>" type="text" class="form-control" name="entity_name" >
                    </div>
                    <div class="form-group col-xs-3">
                        <label for="entity_alternate_name">Alternate Name</label>
                        <input  value="<?php echo $entity_alternate_name; ?>" type="text" class="form-control" name="entity_alternate_name" >
                    </div>
                    <div class="form-group col-xs-1">
                        <label for="activity">Activity</label>
                        <select name='activity'>;
                            <option value="">choose activity</option>
                            <?php
                                $activity_query = "SELECT * FROM activity";
                                $all_activity_query = mysqli_query($connection_production, $activity_query);
                                while ($row = mysqli_fetch_assoc($all_activity_query)) {
                            ?>
                                    <option <?php if ($entity_activity_id == $row['id']) echo 'SELECTED';?> value="<?php echo $row['id'];?>"> <?php echo $row['activity'];?> </option>";
                            <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-9">
                        <label for="entity_description">Description</label>
                        <textarea class="form-control" name="entity_description" id="" cols="30" rows="10"><?php echo $entity_description; ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" name="update_league_info" value="Update Info">
                </div>
            <?php } ?>
    </div>
</form>

<!--
======================================================================
 IMAGE
======================================================================
-->
<div class="form-group">
    <label id="add_image">Add Image</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addImage("entity"); ?>
        <thead>
            <tr>
                <th class="col-md-2">Image ID (if exists)</th>
                <th>Image Name</th>
                <th class="col-md-2">Add image</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post'>
                    <input type="hidden" name="entity_id_set" value="<?php echo $_GET['entity_id']; ?>">
                    <td><input class="form-control" type='number' name='image_id' value=""></td>
                    <td><input class="form-control" type='text' name='image_name' value=""></td>
                    <td><input class='btn btn-primary' type='submit' name='add_image' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="image">Image</label>
    <table class="table table-striped table-bordered table-hover">
        <?php updateImage("entity"); ?>
        <?php deleteImage("entity"); ?>
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
                if (isset($_GET['entity_id'])) {
                    $entity_id = $_GET['entity_id'];

                   $stmt_image = "SELECT DISTINCT image.imageName AS imageName, image.id AS image_id,
                        entity_image_sm.id AS entity_image_sm_id FROM entity
                        LEFT JOIN entity_image_sm ON entity.id=entity_image_sm.entityId
                        LEFT JOIN image ON entity_image_sm.imageId=image.id
                        WHERE entity.id=?";
                    $prepared_image = $connection_production->prepare($stmt_image);
                    $prepared_image->bind_param('i', $entity_id);
                    $result_image = $prepared_image->execute();
                    $result_of_query_image = $prepared_image->get_result();

                    if (!!$result_image && $result_of_query_image->num_rows > 0) {
                        while ($row_image = $result_of_query_image->fetch_assoc()) {
                            if (!!$row_image['image_id']) {
                                $query_imageName = $row_image['imageName'];
                                $query_entity_image_sm_id = $row_image['entity_image_sm_id'];
                                $query_image_id = $row_image['image_id'];
                                array_push($img_names, $query_imageName);

                                echo "<form method='post'>";
                                echo "<tr>";
                                echo "<td>".$query_image_id."</td>";
                                echo "<td><div class='form-group'>";
                                echo "<input value='".$query_imageName."' type='text' class='form-control' name='image_name'>";
                                echo "</div></td>";

                                echo "<input type='hidden' name='image_id' value=".$query_image_id.">";
                                echo "<input type='hidden' name='entity_id_set' value=".$entity_id.">";
                                echo "<td><input class='btn btn-info' type='submit' name='update_image' value='Update'></td>";
                                echo "</form>";

                                echo "<form method='post'>";
                                echo "<input type='hidden' name='delete_image_id' value=".$query_image_id.">";
                                echo "<input type='hidden' name='delete_entity_image_sm_id' value=".$query_entity_image_sm_id.">";
                                echo "<input type='hidden' name='entity_id_set' value=".$entity_id.">";
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
    <?php showImagePreviews($img_names, "entity"); ?>
</div>


<!--
======================================================================
LEAGUE LOCATION
======================================================================
-->
<div class="form-group">
    <label id="add_location">Add Location</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addLocation("entity"); ?>
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
                    <input type="hidden" name="entity_id_set" value="<?php echo $_GET['entity_id']; ?>">
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
        <?php updateLocation("entity"); ?>
        <?php deleteLocation("entity"); ?>
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
                $entity_id_set = $_GET['entity_id'];

                $stmt_location = "SELECT DISTINCT location.id AS location_id, location.city AS city, location.stateProvince AS stateProvince,
                    location.country AS country, entity_location_sm.id AS entity_location_sm_id FROM entity
                    LEFT JOIN entity_location_sm ON entity_location_sm.entityId=entity.id
                    LEFT JOIN location ON entity_location_sm.locationId=location.id
                    WHERE entity.id=?";
                $prepared_location = $connection_production->prepare($stmt_location);
                $prepared_location->bind_param('i', $entity_id);
                $result_location = $prepared_location->execute();
                $result_of_query_location = $prepared_location->get_result();
                if (!!$result_location && $result_of_query_location->num_rows > 0) {
                    while ($row_location = mysqli_fetch_assoc($result_of_query_location)) {
                        if (!!$row_location['location_id']) {
                            $location_id = $row_location['location_id'];
                            $query_entity_location_sm_id = $row_location['entity_location_sm_id'];
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
                            echo "<td><input class='btn btn-info' type='submit' name='update_location' value='Update'></td>";

                            echo "<form method='post'><input type='hidden' name='delete_location_id' value=".$location_id."><input type='hidden' name='delete_entity_location_sm_id' value=".$query_entity_location_sm_id."><input type='hidden' name='entity_id_set' value=".$entity_id_set.">";
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
 PARTITIONS
======================================================================
-->
<div class="form-group">
    <label id="add_partition">Add Partition</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addPartitionToLeague(); ?>
        <thead>
            <tr>
                <th class='col-md-1'>Partition ID</th>
                <th>Add Partition</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post'>
                    <input type='hidden' name='entity_id_set' value="<?php  echo $_GET['entity_id']?>">
                    <td><input type='number' name='partition_id' value='' required></td>
                    <td><input class='btn btn-primary' type='submit' name='add_partition_league' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="partitions">Partitions (conference, division, league, etc.)</label>
    <table class="table table-striped table-bordered table-hover">
        <?php deletePartitionFromLeague(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Partition ID</th>
                <th class="col-sm-2">Partition Name</th>
                <th class="col-sm-2">Partition Desecription</th>
                <th class="col-md-1">Partition Type ID</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if (isset($_GET['entity_id'])) {
                    $entity_id = $_GET['entity_id'];

                    $get_partition_sql = "SELECT DISTINCT lp_partition.partition AS p_partition, lp_partition.description AS p_description,
                        lp_partition.id AS p_id, partition_type.type AS p_type, entity_activity_sm_partition_sm.id AS e_a_sm_p_sm_id FROM entity
                        LEFT JOIN entity_activity_sm ON entity_activity_sm.id = entity.id
                        LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.entityActivitySmId = entity_activity_sm.id
                        LEFT JOIN `partition` AS lp_partition ON lp_partition.id = entity_activity_sm_partition_sm.partitionId
                        LEFT JOIN partition_type ON partition_type.id=lp_partition.partitionTypeId
                        WHERE entity.id=?
                        ORDER BY entity_activity_sm_partition_sm.id ASC";
                    $get_partition_stmt = $connection_production->prepare($get_partition_sql);
                    $get_partition_stmt->bind_param("i", $entity_id);
                    $get_partition_stmt->execute();
                    $get_partition_result = $get_partition_stmt->get_result();
                    $get_partition_stmt->close();

                    while ($row_partition = $get_partition_result->fetch_assoc()) {
                        $p_id = $row_partition['p_id'];
                        $p_partition = $row_partition['p_partition'];
                        $p_description = $row_partition['p_description'];
                        $p_type = $row_partition['p_type'];
                        $e_a_sm_p_sm_id = $row_partition['e_a_sm_p_sm_id'];

                        echo "<tr>";
                        echo "<td><a href='categories.php?source=update_partition&partition_id=".$p_id."'>".$p_id."</a></td>";
                        echo "<td>".$p_partition."</td>";
                        echo "<td>".$p_description."</td>";
                        echo "<td>".$p_type."</td>";

                        echo "<td><form method='post'>";
                        echo "<input type='hidden' name='e_a_sm_p_sm_id' value=".$e_a_sm_p_sm_id.">";
                        echo "<input type='hidden' name='p_id' value=".$p_id.">";
                        echo "<input type='hidden' name='entity_id_set' value=".$entity_id.">";
            ?>
                        <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_partition_league' value='Delete'>
            <?php
                        echo "</form></td>";
                        echo "</tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>

<!--
======================================================================
 TEAMS
======================================================================
-->
<div class="form-group" id="entity_teams">
    <label for="teams">Teams (conference, division, league, etc.)</label>
    <table class="table table-striped table-bordered table-hover" name="partition">
        <thead>
            <tr>
                <th class="col-md-1">Team ID</th>
                <th class="col-sm-2">Team Locale/Name</th>
                <th class="col-sm-2">Team Status</th>
                <!-- <th class="col-md-1">Delete</th> -->
            </tr>
        </thead>
        <tbody>
            <?php
                if (isset($_GET['entity_id'])) {
                    $entity_id = $_GET['entity_id'];

                    $stmt_leagues_team = "SELECT DISTINCT team.id AS team_id,
                        team.status AS team_status, team.locale AS locale, team.name AS team_name FROM team
                        LEFT JOIN franchise_team_sm ON franchise_team_sm.teamId=team.id
                        LEFT JOIN franchise ON franchise.id=franchise_team_sm.franchiseId
                        LEFT JOIN partition_franchise_sm ON partition_franchise_sm.franchiseId=franchise.id
                        LEFT JOIN `partition` AS partition_LP ON partition_LP.id=partition_franchise_sm.partitionId
                        LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id
                        LEFT JOIN entity_activity_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
                        LEFT JOIN entity ON entity.id = entity_activity_sm.entityId
                        WHERE entity.id=?";
                    $prepared_leagues_team = $connection_production->prepare($stmt_leagues_team);
                    $prepared_leagues_team->bind_param('i', $entity_id);
                    $prepared_leagues_team->execute();
                    $result_leagues_team = $prepared_leagues_team->get_result();
                    $prepared_leagues_team->close();

                    while ($row_leagues_team = $result_leagues_team->fetch_assoc()) {
                        $team_id = $row_leagues_team['team_id'];
                        $team_status = $row_leagues_team['team_status'];
                        $locale = $row_leagues_team['locale'];
                        $team_name = $row_leagues_team['team_name'];

                        echo "<tr>";
                        echo "<td><a href='categories.php?source=team_update&team_id=".$team_id."'>".$team_id."</a></td>";
                        echo "<td>".$locale." ".$team_name."</td>";
                        echo "<td>".$team_status."</td>";
                        echo "</tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>

<!--
======================================================================
LEAGUE VIDEOS
======================================================================
-->
<div class="form-group">
    <label for="teams">Profile Videos</label>
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="col-sm">Video ID</th>
                <th class="col-sm">Profile ID</th>
                <th class="col-sm">Video Title</th>
                <th class="col-sm">Video Status</th>
                <th class="col-sm">Source</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $entity_id = $_GET['entity_id'];
                $sql_entity_videos = "SELECT DISTINCT video.id AS video_id, video.title AS title, profile.id AS video_profile_id,
                    video.videoStatus AS video_status, video_source.source AS video_source FROM video
                    LEFT JOIN video_source ON video_source.id = video.videoSourceId
                    LEFT JOIN profile_franchise_sm_video_sm ON video.id=profile_franchise_sm_video_sm.videoId
                    LEFT JOIN profile_franchise_sm ON profile_franchise_sm_video_sm.profileFranchiseSmId=profile_franchise_sm.id
                    LEFT JOIN profile ON profile_franchise_sm.profileId=profile.id
                    LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
                    LEFT JOIN partition_franchise_sm ON partition_franchise_sm.franchiseId=franchise.id
                    LEFT JOIN `partition` AS partition_LP ON partition_LP.id=partition_franchise_sm.partitionId
                    LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id
                    LEFT JOIN entity_activity_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
                    LEFT JOIN entity ON entity.id=entity_activity_sm.entityId WHERE entity.id=?";

                $prepared_sql_entity_videos = $connection_production->prepare($sql_entity_videos);
                $prepared_sql_entity_videos->bind_param('i', $entity_id);
                $result_sql_entity_videos = $prepared_sql_entity_videos->execute();
                $result_of_query_sql_entity_videos = $prepared_sql_entity_videos->get_result();

                while ($row_sql_entity_videos = mysqli_fetch_assoc($result_of_query_sql_entity_videos)) {
                    $video_id = $row_sql_entity_videos['video_id'];
                    $video_profile_id = $row_sql_entity_videos['video_profile_id'];
                    $video_title = $row_sql_entity_videos['title'];
                    $video_status = $row_sql_entity_videos['video_status'];
                    $video_source = $row_sql_entity_videos['video_source'];

                    echo "<tr>";
                    echo "<td><a href='categories.php?source=update_video&video_id=".$video_id."'>".$video_id."</a></td>";
                    echo "<td><a href='categories.php?source=profile_update_athlete&profile_id=".$video_profile_id."'>".$video_profile_id."</a></td>";
                    echo "<td>".$video_title."</td>";
                    echo "<td>".$video_status."</td>";
                    echo "<td>".$video_source."</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>
