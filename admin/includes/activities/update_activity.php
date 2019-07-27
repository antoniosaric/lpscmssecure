<form action="" method="post">
    <?php updateActivity(); ?>
    <div class="form-group">
        <?php
            if (isset($_GET['activity_id'])) {
                $activity_id = $_GET['activity_id'];

                $stmt = "SELECT *, activity.id AS activity_id, activity.activity AS activity,
                    activity_image_sm.id AS activity_image_sm_id, image.id AS image_id,
                    activity.description AS activity_description FROM activity
                    LEFT JOIN activity_image_sm ON activity_image_sm.activityId = activity.id
                    LEFT JOIN image ON image.id=activity_image_sm.imageId
                    WHERE activity.id=?";
                $prepared = $connection_production->prepare($stmt);
                $prepared->bind_param('i', $activity_id);
                $result = $prepared->execute();
                $result_of_query = $prepared->get_result();

                $row = $result_of_query->fetch_assoc();
                $activity_image_sm_id = $row['activity_image_sm_id'];
                $image_id = $row['image_id'];
                $activity_id = $row['activity_id'];
                $activity_description = $row['activity_description'];
                $activity = $row['activity'];
                $image_name = $row['imageName'];

                if (!$activity) {
                    echo "<h3 style='color:red;'>Activity Not found </h3>";
                }
        ?>
                <div class="form-group">
                    <label for="activity">Edit Activity</label>
                    <input value="<?php echo $activity; ?>" type="text" class="form-control" name="activity" >
                </div>
                <div class="form-group">
                    <label for="image_name">Image name</label>
                    <input value="<?php echo $image_name; ?>" type="text" class="form-control" name="image_name" style="margin-bottom:1vh" />

                    <?php
                        if (!empty($image_name)) {
                            showImagePreviews(array($image_name), "activity");
                        }
                    ?>
                </div>
                <div class="form-group">
                    <label for="activity_description">Activity Description</label>
                    <textarea class="form-control" name="activity_description" id="" cols="30" rows="10"><?php echo $activity_description; ?></textarea>
                </div>
                </div>
                <div class="form-group">
                    <input type='hidden' name="activity_id" value=<?php echo $activity_id; ?> >
                    <input type='hidden' name="image_id" value=<?php echo $image_id; ?> >
                    <input class="btn btn-primary" type="submit" name="update_activity" value="Update Activity">
                </div>
        <?php }  ?>
    </div>
</form>

<div class="form-group">
    <label id="add_profile">Add Profile</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addProfileToActivity(); ?>
        <thead>
            <tr>
                <th class='col-md-1'>Profile ID</th>
                <th>Add Profile</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                    if (isset($_GET['activity_id'])) {
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='activity_id' value=".$_GET['activity_id'].">";
                        echo "<td><input type='number' name='profile_id' value='' required></td>";
                        echo "<td><input class='btn btn-primary' type='submit' name='add_profile' value='Add'>";
                        echo "</form>";
                    }
                ?>
            </tr>
        </tbody>
    </table>

    <label id="profiles">Profiles</label>
    <table class="table table-striped table-bordered table-hover">
        <?php
            removeProfileFromActivity();
        ?>
        <thead>
            <tr>
                <th class="col-md-1">Profile ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th class="col-md-1">Remove</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if (isset($_GET['activity_id'])) {
                    $activity_id = $_GET['activity_id'];

                    $get_profiles_sql = "SELECT DISTINCT profile.id AS profile_id, participant.firstName AS first_name,
                        participant.lastName AS last_name, profile_activity_sm.id AS p_a_sm_id FROM profile
                        LEFT JOIN participant ON participant.id=profile.participantId
                        LEFT JOIN profile_activity_sm ON profile_activity_sm.profileId=profile.id
                        WHERE profile_activity_sm.activityId=?
                        ORDER BY last_name ASC";
                    $get_profiles_stmt = $connection_production->prepare($get_profiles_sql);
                    $get_profiles_stmt->bind_param("i", $activity_id);
                    $get_profiles_stmt->execute();
                    $get_profiles_result = $get_profiles_stmt->get_result();
                    $get_profiles_stmt->close();

                    while ($row_profile = $get_profiles_result->fetch_assoc()) {
                        $profile_id = $row_profile['profile_id'];
                        $p_a_sm_id = $row_profile['p_a_sm_id'];
                        $first_name = $row_profile['first_name'];
                        $last_name = $row_profile['last_name'];

                        echo "<tr>";
                        echo "<td><a href='categories.php?source=profile_update_athlete&profile_id=".$profile_id."'>".$profile_id."</a></td>";
                        echo "<td>".$first_name."</td>";
                        echo "<td>".$last_name."</td>";

                        echo "<td><form method='post'>";
                        echo "<input type='hidden' name='p_a_sm_id' value=".$p_a_sm_id.">";
                        echo "<input type='hidden' name='profile_id' value=".$profile_id.">";
                        echo "<input type='hidden' name='activity_id' value=".$activity_id.">";
            ?>
                        <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='remove_profile' value='Remove'>
            <?php
                        echo "</form></td>";
                        echo "</tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>

<div class="form-group">
    <label id="add_league">Add League</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addEntityToActivity(); ?>
        <thead>
            <tr>
                <th class='col-md-1'>League ID</th>
                <th>Add League</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if (isset($_GET['activity_id'])) {
                    echo "<tr>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='activity_id' value=".$_GET['activity_id'].">";
                    echo "<td><input type='number' name='entity_id' value='' required></td>";
                    echo "<td><input class='btn btn-primary' type='submit' name='add_entity' value='Add'>";
                    echo "</form>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>

    <label id="leagues">Leagues</label>
    <table class="table table-striped table-bordered table-hover">
        <?php removeEntityFromActivity(); ?>
        <thead>
            <tr>
                <th class="col-md-1">League ID</th>
                <th>League Name</th>
                <th>League Description</th>
                <th class='col-md-1'>Remove</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if (isset($_GET['activity_id'])) {
                    $activity_id = $_GET['activity_id'];

                    $get_entities_sql = "SELECT DISTINCT entity.id AS entity_id, entity.name AS entity_name,
                        entity.description AS entity_description, entity_activity_sm.id AS e_a_sm_id FROM entity
                        LEFT JOIN entity_activity_sm ON entity_activity_sm.entityId=entity.id
                        WHERE entity_activity_sm.activityId=?
                        ORDER BY entity_name ASC";
                    $get_entities_stmt = $connection_production->prepare($get_entities_sql);
                    $get_entities_stmt->bind_param("i", $activity_id);
                    $get_entities_stmt->execute();
                    $get_entities_result = $get_entities_stmt->get_result();
                    $get_entities_stmt->close();

                    while ($row_entity = $get_entities_result->fetch_assoc()) {
                        $entity_id = $row_entity['entity_id'];
                        $entity_name = $row_entity['entity_name'];
                        $entity_description = $row_entity['entity_description'];
                        $e_a_sm_id = $row_entity['e_a_sm_id'];

                        echo "<tr>";
                        echo "<td><a href='categories.php?source=league_update&entity_id=".$entity_id."'>".$entity_id."</a></td>";
                        echo "<td>".$entity_name."</td>";
                        echo "<td>".$entity_description."</td>";

                        echo "<td><form method='post'>";
                        echo "<input type='hidden' name='entity_id' value=".$entity_id.">";
                        echo "<input type='hidden' name='activity_id' value=".$activity_id.">";
                        echo "<input type='hidden' name='e_a_sm_id' value=".$e_a_sm_id.">";
            ?>
                        <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='remove_entity' value='Remove'>
            <?php
                        echo "</form></td>";
                        echo "</tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>
