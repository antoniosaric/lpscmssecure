<!--
======================================================================
 PROFILE INFO
======================================================================
-->

<form action="" method="post">
    <div class="form-group">
        <?php
            updateAthleteInfo();

            if (isset($_GET['profile_id'])) {
                $profile_id = $_GET['profile_id'];

                $stmt = "SELECT DISTINCT *, profile.id AS profile_id, participant.firstName, participant.middle, participant.lastName,
                    participant.birthdate AS birthdate, participant.gender AS gender, profile.summary, profile.acclaim AS acclaim,
                    participant_suffix.suffix, participant_suffix.id AS suffixId, alternate_name.alternateName AS nickname,
                    profile.mainProfileType AS mainProfileType, profile.profileTypeId AS profileTypeId, profile.status AS status,
                    specialty.specialty AS specialty FROM participant
                    LEFT JOIN profile ON profile.participantId = participant.id
                    LEFT JOIN participant_suffix ON participant.participantSuffixId=participant_suffix.id
                    LEFT JOIN profile_alternate_name_sm ON profile_alternate_name_sm.profileId=profile.id
                    LEFT JOIN alternate_name ON profile_alternate_name_sm.alternateNameId = alternate_name.id
                    LEFT JOIN profile_specialty_sm ON profile_specialty_sm.profileId = profile.id
                    LEFT JOIN specialty ON specialty.id = profile_specialty_sm.specialtyId
                    WHERE profile.id=?";
                $prepared = $connection_production->prepare($stmt);
                $prepared->bind_param('i', $profile_id);
                $result = $prepared->execute();
                $result_of_query = $prepared->get_result();
                $row = $result_of_query->fetch_assoc();

                $profile_id_set = $row['profile_id'];
                $firstName = $row['firstName'];
                $lastName = $row['lastName'];
                $middle = $row['middle'];
                $nickname = $row['nickname'];
                $specialty = $row['specialty'];
                $suffix = $row['suffixId'];
                $status = $row['status'];
                $summary = $row['summary'];
                $acclaim = $row['acclaim'];
                $birthdate = $row['birthdate'];
                $gender = $row['gender'];
                $profileTypeId = $row['profileTypeId'];
        ?>
                <input type="hidden" name="profile_id_set" value="<?php echo $profile_id_set; ?>">
                <div class="row">
                    <div class="form-group col-xs-4">
                        <label for="firstName">First Name</label>
                        <input value="<?php echo $firstName; ?>" type="text" class="form-control" name="firstName" >
                    </div>
                    <div class="form-group col-xs-2">
                        <label for="middle">Middle Name</label>
                        <input value="<?php echo $middle; ?>" type="text" class="form-control" name="middle" >
                    </div>
                    <div class="form-group form-group col-xs-4">
                        <label for="lastName">Last Name</label>
                        <input value="<?php echo $lastName; ?>" type="text" class="form-control" name="lastName" >
                    </div>
                    <div class="form-group col-xs-1">
                        <label for="suffix">Suffix</label>
                        <select name='suffix'>;
                            <option value="">choose suffix</option>
                            <?php
                                $suffix_query = "SELECT * FROM suffix";
                                $all_suffix_query = mysqli_query($connection_production, $suffix_query);
                                while ($row = mysqli_fetch_assoc($all_suffix_query)) {
                            ?>
                                    <option <?php if ($suffix == $row['id']) echo 'SELECTED';?> value="<?php echo $row['id'];?>"> <?php echo $row['suffix'];?> </option>";
                            <?php
                                }
                             ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-3">
                        <?php
                            if ($profileTypeId == 2) {
                        ?>
                                <label for="specialty">Specialty</label>
                                <input value="<?php echo $specialty; ?>" type="text" class="form-control" name="specialty" >
                        <?php
                            } else {
                        ?>
                                <label for="nickname">Nickname</label>
                                <input value="<?php echo $nickname; ?>" type="text" class="form-control" name="nickname" >
                        <?php } ?>
                    </div>
                    <div class="form-group col-xs-3">
                        <label for="birthdate">Birthdate</label>
                        <input value="<?php echo $birthdate; ?>" type="date" class="form-control" name="birthdate" >
                    </div>
                    <div class="form-group col-xs-1">
                        <label for="status">Status</label>
                        <select name='status'>;
                            <option <?php if ($status == "complete") echo "SELECTED";?> value="complete">Complete</option>
                            <option <?php if ($status == "incomplete") echo "SELECTED";?> value="incomplete">Incomplete</option>
                            <option <?php if ($status == "disabled") echo "SELECTED";?> value="disabled">Disabled</option>
                        </select>
                    </div>
                    <div class="form-group col-xs-1">
                        <label for="gender">Gender</label>
                        <select name='gender'>;
                            <option <?php if ($gender == "M") echo "SELECTED";?> value="M">Male</option>
                            <option <?php if ($gender == "F") echo "SELECTED";?> value="F">Female</option>
                            <option <?php if ($gender == "O") echo "SELECTED";?> value="O">Other</option>
                        </select>
                    </div>
                    <div class="form-group col-xs-1">
                        <label for="profileTypeId">Type</label>
                        <select name='profileTypeId'>;
                            <option <?php if ($profileTypeId == 0) echo "SELECTED"; if ($profileTypeId != 0) echo "DISABLED";?> value="0">Default 0</option>
                            <option <?php if ($profileTypeId == 1) echo "SELECTED"; if ($profileTypeId == 2) echo "DISABLED"; ?> value="1">Athlete</option>
                            <option <?php if ($profileTypeId == 2) echo "SELECTED"; if ($profileTypeId != 2) echo "DISABLED";?> value="2">Coach</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="summary">Profile Summary</label>
                    <textarea class="form-control" name="summary" id="" cols="30" rows="10"><?php echo $summary; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="acclaim">Profile Acclaim</label>
                    <textarea class="form-control" name="acclaim" id="" cols="30" rows="10"><?php echo $acclaim; ?></textarea>
                </div>
                </div>
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" name="update_athlete_info" value="Update Info">
                </div>
            <?php } ?>
    </div>
</form>

<!--
======================================================================
 PROFILE IMAGE
======================================================================
-->

<div class="form-group">
    <label id="add_image">Add Image</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addImage("profile"); ?>
        <thead>
            <tr>
                <th class="col-md-1">Image ID (if exists)</th>
                <th>Image Name</th>
                <th class="col-md-1">Add image</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post'>
                    <input type="hidden" name="profile_id_set" value="<?php echo $_GET['profile_id']; ?>">
                    <td><input class="form-control" type='number' name='image_id' value=""></td>
                    <td><input class="form-control" type='text' name='image_name' value=""></td>
                    <td><input class='btn btn-primary' type='submit' name='add_image' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="image">Image</label>
    <table class="table table-striped table-bordered table-hover">
        <?php
            updateImage("profile");
            deleteImage("profile");
        ?>
        <thead>
            <tr>
                <th class="col-md-1">ID</th>
                <th>Image Name</th>
                <th class="col-md-1">Update</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $img_names = array();
                if (isset($_GET['profile_id'])) {
                    $profile_id = $_GET['profile_id'];

                    $stmt_image = "SELECT DISTINCT image.imageName AS imageName, image.id AS image_id,
                        profile_image_sm.id AS profile_image_sm_id FROM profile
                        LEFT JOIN profile_image_sm ON profile.id=profile_image_sm.profileId
                        LEFT JOIN image ON profile_image_sm.imageId=image.id
                        WHERE profile.id=?";
                    $prepared_image = $connection_production->prepare($stmt_image);
                    $prepared_image->bind_param('i', $profile_id);
                    $result_image = $prepared_image->execute();
                    $result_of_query_image = $prepared_image->get_result();

                    if (!!$result_image && $result_of_query_image->num_rows > 0) {
                        while ($row_image = $result_of_query_image->fetch_assoc()) {
                            if (!!$row_image['image_id']) {
                                $query_imageName = $row_image['imageName'];
                                $query_profile_image_sm_id = $row_image['profile_image_sm_id'];
                                $query_image_id = $row_image['image_id'];
                                array_push($img_names, $query_imageName);

                                echo "<form method='post'>";
                                echo "<tr>";
                                echo "<td>".$query_image_id."</td>";
                                echo "<td><div class='form-group'>";
                                echo "<input value='".$query_imageName."' type='text' class='form-control' name='image_name'>";
                                echo "</div></td>";

                                echo "<input type='hidden' name='image_id' value=".$query_image_id.">";
                                echo "<input type='hidden' name='profile_id_set' value=".$profile_id.">";
                                echo "<td><input class='btn btn-info' type='submit' name='update_image' value='Update'></td>";
                                echo "</form>";

                                echo "<form method='post'>";
                                echo "<input type='hidden' name='profile_id_set' value=".$profile_id.">";
                                echo "<input type='hidden' name='delete_image_id' value=".$query_image_id.">";
                                echo "<input type='hidden' name='delete_profile_image_sm_id' value=".$query_profile_image_sm_id.">";
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
    <?php showImagePreviews($img_names, "profile"); ?>
</div>

<!--
======================================================================
 PROFILE LOCATION
======================================================================
-->

<div class="form-group">
    <label id="add_location">Add Location</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addLocation('profile'); ?>
        <thead>
            <tr>
                <th>City</th>
                <th>State/province</th>
                <th>Country</th>
                <th class="col-md-1">Add location</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post'>
                    <input type="hidden" name="profile_id_set" value="<?php echo $_GET['profile_id']; ?>">
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
        <?php
            updateLocation("profile");
            deleteLocation("profile");
        ?>
        <thead>
            <tr>
                <th class="col-md-1">ID</th>
                <th>City</th>
                <th>State/province</th>
                <th>Country</th>
                <th>Update</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <form method='post'>
                <input type="hidden" name="profile_id_set" value="<?php echo $profile_id_set; ?>">
                <?php
                    $stmt_location = "SELECT DISTINCT location.id AS location_id, location.city AS city, location.stateProvince AS stateProvince,
                        location.country AS country, profile_location_sm.id AS profile_location_sm_id FROM profile
                        LEFT JOIN profile_location_sm ON profile_location_sm.profileId=profile.id
                        LEFT JOIN location ON profile_location_sm.locationId=location.id
                        WHERE profile.id=?";
                    $prepared_location = $connection_production->prepare($stmt_location);
                    $prepared_location->bind_param('i', $profile_id);
                    $result_location = $prepared_location->execute();
                    $result_of_query_location = $prepared_location->get_result();
                    if (!!$result_location && $result_of_query_location->num_rows > 0) {
                        while ($row_location = mysqli_fetch_assoc($result_of_query_location)) {
                            if (!!$row_location['location_id']) {
                                $location_id = $row_location['location_id'];
                                $query_profile_location_sm_id = $row_location['profile_location_sm_id'];
                                $city = $row_location['city'];
                                $stateProvince = $row_location['stateProvince'];
                                $country = $row_location['country'];

                                echo "<tr>";
                                echo "<td>".$location_id."</td>";
                                echo "<td><div class='form-group'><input value='".$city."' type='text' class='form-control' name='update_location_city'></div></td>";
                                echo "<td><div class='form-group'><input value='".$stateProvince."' type='text' class='form-control' name='update_location_state_province'></div></td>";
                                echo "<td><div class='form-group'><input value='".$country."' type='text' class='form-control' name='update_location_country' ></div></td>";
                                echo "<input type='hidden' name='update_location_id' value=".$location_id.">";
                                echo "<td><input class='btn btn-info' type='submit' name='update_location' value='Update'></td>";

                                echo "<form method='post'><input type='hidden' name='delete_location_id' value=".$location_id."><input type='hidden' name='delete_profile_location_sm_id' value=".$query_profile_location_sm_id."><input type='hidden' name='profile_id_set' value=".$profile_id_set.">";
                                echo "<td>";
                ?>
                                <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_location' value='Delete'>
                <?php
                                echo "</td>";
                                echo "</form>";
                                echo "</tr>";
                            }
                        }
                    }
                ?>
            </form>
        </tbody>
    </table>
</div>

<!--
======================================================================
 PROFILE ACTIVITY
======================================================================
-->

<div class="form-group">
    <label id="add_activity">Add Activity</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addAthleteActivity(); ?>
        <thead>
            <tr>
                <th class="col-md-2">Activity ID</th>
                <th>Add activity</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <td>
                <form method='post'>
                    <input type="hidden" name="profile_id_set" value="<?php echo $profile_id_set; ?>">
                    <select name='activity_id'>;
                        <option value="">Activity...</option>
                    <?php
                        $stmt_all_activity = "SELECT DISTINCT activity.activity AS activity, activity.id AS activity_id FROM activity";
                        $prepared_all_activity = $connection_production->prepare($stmt_all_activity);
                        $result_all_activity = $prepared_all_activity->execute();
                        $result_of_query_all_activity = $prepared_all_activity->get_result();
                        if (!!$result_all_activity && $result_of_query_all_activity->num_rows > 0) {
                            while ($row_all_activity = mysqli_fetch_assoc($result_of_query_all_activity)) {
                                if (!!$row_all_activity['activity_id']) {
                                    echo "<option value=".$row_all_activity['activity_id'].">".
                                        $row_all_activity['activity_id'].": ".$row_all_activity['activity']."</option>";
                                }
                            }
                        }
                    ?>
                    </select>
                </td>
                    <td><input class='btn btn-primary' type='submit' name='add_profile_activity' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="activities">Activities</label>
    <table class="table table-striped table-bordered table-hover">
        <?php deleteAthleteActivity(); ?>
        <thead>
            <tr>
                <th class="col-md-1">ID</th>
                <th>Activity Name</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $stmt_activity = "SELECT DISTINCT activity.activity AS activity, activity.id AS activityId,
                    profile_activity_sm.id AS profile_activity_sm_id FROM activity
                    LEFT JOIN profile_activity_sm ON profile_activity_sm.activityId=activity.id
                    LEFT JOIN profile ON profile_activity_sm.profileId=profile.id
                    WHERE profile.id=?";
                $prepared_activity = $connection_production->prepare($stmt_activity);
                $prepared_activity->bind_param('i', $profile_id);
                $result_activity = $prepared_activity->execute();
                $result_of_query_activity = $prepared_activity->get_result();
                if (!!$result_activity && $result_of_query_activity->num_rows > 0) {
                    while ($row_activity = mysqli_fetch_assoc($result_of_query_activity)) {
                        if (!!$row_activity['activityId']) {
                            $query_activity_id = $row_activity['activityId'];
                            $query_activity = $row_activity['activity'];
                            $query_profile_activity_sm_id = $row_activity['profile_activity_sm_id'];

                            echo "<tr>";
                            echo "<td><a href='categories.php?source=sports_update&activity_id=".$query_activity_id."'>".$query_activity_id."</a></td>";
                            echo "<td>".$query_activity."</td>";
                            echo "<form method='post'><input type='hidden' name='query_activity_id' value=".$query_activity_id."><input type='hidden' name='query_profile_activity_sm_id' value=".$query_profile_activity_sm_id."><input type='hidden' name='query_activity' value=".$query_activity."><input type='hidden' name='profile_id_set' value=".$profile_id_set.">";
                            echo "<td>";
                ?>
                            <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_profile_activity' value='Delete'>
                <?php
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
 PROFILE TEAM ASSOCIATION
======================================================================
-->

<div class="form-group">
    <label id="add_team">Add Team</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addTeamToProfile(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Team ID</th>
                <th>Add team</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post'>
                    <input type="hidden" name="profile_id_set" value="<?php echo $profile_id_set; ?>">
                    <td><input class="form-control" type='number' name='team_id' value=""></td>
                    <td><input class='btn btn-primary' type='submit' name='add_profile_team' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="teams">Teams</label>
    <table class="table table-striped table-bordered table-hover">
        <?php
            addAthleteTeamPosition();
            deleteAthleteTeamPosition();
            addCoachTeamTitle();
            deleteCoachTeamTitle();
            updateCoachTeamTitle();
            addAthleteTeamPeriod();
            deleteAthleteTeamPeriod();
            deleteAthleteFromTeam();
        ?>
        <thead>
            <tr>
                <th class="col-md-1">ID</th>
                <th class="col-sm">Team Locale</th>
                <th class="col-sm">Team Name</th>
                <th class="col-sm">Sport</th>
                <?php
                    $stmt_profile = "SELECT DISTINCT profile.profileTypeId AS profileTypeId FROM profile WHERE profile.id=?";
                    $prepared_profile = $connection_production->prepare($stmt_profile);
                    $prepared_profile->bind_param('i', $profile_id);
                    $result_profile = $prepared_profile->execute();
                    $result_of_query_profile = $prepared_profile->get_result();

                    if (!!$result_profile && $result_of_query_profile->num_rows > 0) {
                        $row_profile = mysqli_fetch_assoc($result_of_query_profile);
                        if ($row_profile['profileTypeId'] == 0 || $row_profile['profileTypeId'] == 1) {
                            echo "<th class='col-sm'>Position</th>";
                        } else {
                            echo "<th class='col-sm'>Title</th>";
                        }
                    }
                ?>
                <th class="col-sm">Period</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <form method='post'>
                <input type="hidden" name="profile_id_set" value="<?php echo $profile_id_set; ?>">
                <?php
                    $stmt_team = "SELECT DISTINCT activity.activity AS activity, activity.id AS activity_id, team.id AS teamId,
                        team.status AS teamStatus, team.locale AS locale, team.name AS teamname, entity.name AS league,
                        team.description AS teamdescription, profile.id AS profileId, profile_franchise_sm.id AS profile_franchise_sm_id,
                        franchise.id AS franchise_id FROM profile
                        LEFT JOIN profile_franchise_sm ON profile_franchise_sm.profileId=profile.id
                        LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
                        LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
                        LEFT JOIN team ON team.id=franchise_team_sm.teamId
                        LEFT JOIN partition_franchise_sm ON partition_franchise_sm.franchiseId=franchise.id
                        LEFT JOIN `partition` AS partition_LP ON partition_LP.id=partition_franchise_sm.partitionId
                        LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.partitionId=partition_LP.id
                        LEFT JOIN entity_activity_sm ON entity_activity_sm.id=entity_activity_sm_partition_sm.entityActivitySmId
                        LEFT JOIN entity ON entity.id = entity_activity_sm.entityId
                        LEFT JOIN activity ON activity.id=entity_activity_sm.activityId WHERE profile.id=?";
                    $prepared_team = $connection_production->prepare($stmt_team);
                    $prepared_team->bind_param('i', $profile_id);
                    $result_team = $prepared_team->execute();
                    $result_of_query_team = $prepared_team->get_result();

                    if (!!$result_team && $result_of_query_team->num_rows > 0) {
                        while ($row_team = mysqli_fetch_assoc($result_of_query_team)) {
                            $teamId = $row_team['teamId'];
                            $locale = $row_team['locale'];
                            $teamname = $row_team['teamname'];
                            $activity = $row_team['activity'];
                            $activity_id = $row_team['activity_id'];
                            $profile_franchise_sm_id = $row_team['profile_franchise_sm_id'];
                            $franchise_id = $row_team["franchise_id"];

                            echo "<tr>";
                            echo "<td><a href='categories.php?source=team_update&team_id=".$teamId."'>".$teamId."</a></td>";
                            echo "<td>".$locale."</td>";
                            echo "<td>".$teamname."</td>";
                            echo "<td>".$activity."</td>";

                            if ($profileTypeId == 1 || $profileTypeId == 0) {
                                echo "<td>";

                                $query_profile_franchise_sm_id = "SELECT DISTINCT activity_role.id AS activity_role_id, activity_role.role AS role,
                                    profile_franchise_sm_activity_role_sm.id AS p_f_sm_a_r_sm_id FROM activity_role
                                    LEFT JOIN profile_franchise_sm_activity_role_sm ON profile_franchise_sm_activity_role_sm.activityRoleId=activity_role.id
                                    WHERE activity_role.activityId=".$activity_id." AND profile_franchise_sm_activity_role_sm.profileFranchiseSmId=".$profile_franchise_sm_id;

                                $select_all_profile_franchise_sm_id = mysqli_query($connection_production, $query_profile_franchise_sm_id);

                                if (!!$select_all_profile_franchise_sm_id && mysqli_num_rows($select_all_profile_franchise_sm_id) > 0) {
                                    while ($row_profile_franchise_sm_id = mysqli_fetch_assoc($select_all_profile_franchise_sm_id)) {
                                        $p_f_sm_a_r_sm_id = $row_profile_franchise_sm_id['p_f_sm_a_r_sm_id'];
                                        $role_name = $row_profile_franchise_sm_id['role'];
                                        // echo "<p>".$role_name."</p>";

                                        echo "<form method='post'>";
                                            echo "<input type='hidden' name='p_f_sm_a_r_sm_id' value=".$p_f_sm_a_r_sm_id.">".$role_name."    ";
                                            echo "<input type='hidden' name='role_name' value=".$role_name.">";
                                            echo "<input type='hidden' name='profile_id_set' value=".$profile_id_set.">";
                                            echo "<input type='hidden' name='team_role_id' value=".$teamId.">";
                ?>
                                            <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_role' value='Delete'>
                <?php
                                        echo "</form>";
                                    }
                                }

                                echo "<form method='post' action=''>";
                                    echo "<input type='hidden' name='profile_id_set' value=".$profile_id_set.">";
                                    echo "<input type='hidden' name='team_role_id' value=".$teamId.">";
                                    echo "<input type='hidden' name='profile_franchise_sm_id' value=".$profile_franchise_sm_id.">";
                                    echo "<select name='post_role_info'>";
                                        echo "<option value=''>position...</option>";
                                        $query_role = "SELECT activity_role.id AS activity_role_id, activity_role.role AS role FROM activity_role
                                            WHERE activity_role.activityId=".$activity_id;
                                        $select_all_role = mysqli_query($connection_production, $query_role);

                                        while ($row_role = mysqli_fetch_assoc($select_all_role)) {
                                            $activity_role_id = $row_role['activity_role_id'];
                                            $role = $row_role['role'];
                                            echo "<option value=".$activity_role_id."|".$role.">".$role."</option>";
                                        }
                                    echo "</select>";

                                    echo "     <input class='btn btn-primary' type='submit' name='add_role' value='Add'>";
                                echo "</form>";
                                echo "</td>";
                            } else if ($profileTypeId == 2) {

                                echo "<td>";

                                $query_profile_franchise_sm_id = "SELECT DISTINCT title.title AS title, title.id as title_id,
                                    profile_franchise_sm_title_sm.id AS p_f_sm_t_sm_id FROM title
                                    LEFT JOIN profile_franchise_sm_title_sm ON profile_franchise_sm_title_sm.titleId=title.id
                                    LEFT JOIN profile_franchise_sm ON profile_franchise_sm_title_sm.profileFranchiseSmId=profile_franchise_sm.id
                                    LEFT JOIN profile ON profile.id = profile_franchise_sm.profileId
                                    LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=profile_franchise_sm.franchiseId
                                    LEFT JOIN team ON team.id=franchise_team_sm.teamId
                                    WHERE profile_franchise_sm_title_sm.profileFranchiseSmId=".$profile_franchise_sm_id." AND team.id=".$teamId;

                                $select_profile_franchise_sm_id = mysqli_query($connection_production, $query_profile_franchise_sm_id);

                                if (!!$select_profile_franchise_sm_id) {
                                    // if (mysqli_num_rows($select_profile_franchise_sm_id) > 0) {
                                        $row_profile_franchise_sm_id = mysqli_fetch_assoc($select_profile_franchise_sm_id);
                                        $p_f_sm_t_sm_id = $row_profile_franchise_sm_id['p_f_sm_t_sm_id'];
                                        $title = $row_profile_franchise_sm_id['title'];
                                        $title_id = $row_profile_franchise_sm_id['title_id'];
                                        echo "<form method='post'>";
                                        echo "<input type='text' name='title' value=".$title.">"."    ";
                                        echo "<input type='hidden' name='profile_id_set' value=".$profile_id_set.">";
                                        echo "<input type='hidden' name='team_title_id' value=".$teamId.">";

                                        if (!!$p_f_sm_t_sm_id) {
                                            echo "<input type='hidden' name='title_id' value=".$title_id.">";
                                            echo "<input type='hidden' name='p_f_sm_t_sm_id' value=".$p_f_sm_t_sm_id.">";
                ?>
                                            <input class='btn btn-primary' type='submit' name='update_title' value='update'>
                                            <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_title' value='Delete'>
                <?php
                                        } else {
                ?>
                                            <input class='btn btn-primary' type='submit' name='add_title' value='Add'>
                <?php
                                        }
                                    echo "</form>";
                                }
                                echo "</td>";
                            }

                            echo "<td>";
                                $query_profile_team_period_sm = "SELECT DISTINCT profile_franchise_sm.id AS profile_franchise_sm_id,
                                    profile_franchise_sm_period_sm.id AS profile_franchise_sm_period_sm_id, period.id AS period_id, period.start AS period_start,
                                    period.end AS period_end FROM profile
                                    LEFT JOIN profile_franchise_sm ON profile_franchise_sm.profileId=profile.id
                                    LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
                                    LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
                                    LEFT JOIN team ON team.id=franchise_team_sm.teamId
                                    LEFT JOIN profile_franchise_sm_period_sm ON profile_franchise_sm_period_sm.profileFranchiseSmId=profile_franchise_sm.id
                                    LEFT JOIN period ON period.id=profile_franchise_sm_period_sm.periodId
                                    WHERE profile.id=".$profile_id." AND team.id=".$teamId;

                                $select_all_profile_team_period_sm = mysqli_query($connection_production, $query_profile_team_period_sm);

                                if (!!$select_all_profile_team_period_sm && mysqli_num_rows($select_all_profile_team_period_sm) > 0) {
                                    while ($row_profile_team_period_sm = mysqli_fetch_assoc($select_all_profile_team_period_sm)) {

                                        $profile_franchise_sm_id = $row_profile_team_period_sm["profile_franchise_sm_id"];
                                        $profile_franchise_sm_period_sm_id = $row_profile_team_period_sm["profile_franchise_sm_period_sm_id"];
                                        $period_id = $row_profile_team_period_sm["period_id"];
                                        $period_start = $row_profile_team_period_sm["period_start"];
                                        $period_end = $row_profile_team_period_sm["period_end"];
                                        $period_time = $period_start." - ".$period_end;

                                        if (!!$period_id && ($period_id != null)) {
                                            echo "<form method='post'>";
                                                echo "<input type='hidden' name='delete_period_id' value=".$period_id.">".$period_start." - ".$period_end."    ";
                                                echo "<input type='hidden' name='period_time' value='".$period_time."'>";
                                                echo "<input type='hidden' name='profile_id_set' value=".$profile_id_set.">";
                                                echo "<input type='hidden' name='profile_franchise_sm_period_sm_id' value=".$profile_franchise_sm_period_sm_id."><input type='hidden' name='team_period_id' value=".$teamId.">";
                ?>
                                                <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_period_association' value='Delete'>
                <?php
                                            echo "</form>";
                                        }
                                    }
                                }

                                echo "<form method='post' action=''>";
                                    echo "<input type='hidden' name='profile_id_set' value=".$profile_id_set.">";
                                    echo "<input type='hidden' name='team_period_id' value=".$teamId.">";
                                    echo "<input type='hidden' name='profile_franchise_sm_id' value=".$profile_franchise_sm_id.">";
                                    echo "<select name='post_team_period_start'>";
                                        echo "<option value=''>start...</option>";

                                        $years = '';
                                        $startyear_start = date('Y'); // This year
                                        $endyear_start = date('Y', mktime(0,0,0,0,0,date('Y')-50)); // Three years ahead
                                        $startyear_end = date('Y'); // This year
                                        $endyear_end = date('Y', mktime(0,0,0,0,0,date('Y')-50)); // Three years ahead
                                        foreach (range($startyear_start, $endyear_start) as $year_start) {
                                            echo '<option value="'. $year_start . '">' . $year_start . "</option>";
                                        }

                                    echo "</select>";
                                    echo "     ";
                                    echo "<select name='post_team_period_end'>";
                                        echo "<option value=''>end...</option>";
                                        echo "<option value='Present'>Present</option>";

                                        $years = '';
                                        $startyear_end = date('Y'); // This year
                                        $endyear_end = date('Y', mktime(0,0,0,0,0,date('Y')-50)); // Three years ahead
                                        foreach (range($startyear_end, $endyear_end) as $year_end) {
                                            echo '<option value="'. $year_end . '">' . $year_end . "</option>";
                                        }

                                    echo "</select>";

                                    echo "     <input class='btn btn-primary' type='submit' name='add_team_period' value='Add'>";
                                echo "</form>";
                            echo "</td>";
                            echo "<td>";
                            echo "<form method='post'>";
                                echo "<input type='hidden' name='profile_id_set' value=".$profile_id_set.">";
                                echo "<input type='hidden' name='query_team_id' value=".$teamId.">";
                                echo "<input type='hidden' name='franchise_id' value=".$franchise_id.">";
                                echo "<input type='hidden' name='profile_franchise_sm_id' value=".$profile_franchise_sm_id.">";
                ?>
                                <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_team_association' value='Delete'>
                <?php
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </form>
        </tbody>
    </table>
</div>

<div class='form-group'>
    <label id="add_video">Add Video</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addVideoToProfile(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Video ID</th>
                <th>Activity ID</th>
                <th>Team ID</th>
                <th class="col-md-1">Add Video</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                    if (isset($_GET['profile_id'])) {
                ?>
                    <form method='post'>
                        <input type='hidden' name='profile_id' value=<?php echo $_GET['profile_id']; ?> />
                        <td><input class='form-control' type='number' name='video_id' value='' required /></td>
                        <td>
                            <?php
                                echo "<select name='activity_id'>";
                                    echo "<option value=''>select</option>";
                                    $stmt_activity = "SELECT DISTINCT activity.id AS activity_id, activity.activity AS activity FROM activity LEFT JOIN profile_activity_sm ON profile_activity_sm.activityId=activity.id LEFT JOIN profile ON profile_activity_sm.profileId=profile.id WHERE profile.id=?";

                                    $prepared_activity = $connection_production->prepare($stmt_activity);
                                    $prepared_activity->bind_param('i', $_GET['profile_id']);
                                    $result_activity = $prepared_activity->execute();
                                    $result_of_query_activity = $prepared_activity->get_result();

                                    while ($row_activity = mysqli_fetch_assoc($result_of_query_activity)) {
                                        $activity_id = $row_activity['activity_id'];
                                        $activity = $row_activity['activity'];
                                        echo "<option value=".$activity_id."|".$activity.">".$activity."</option>";
                                    }
                                echo "</select>";
                            ?>
                            <!--<input class='form-control' type='number' name='activity_id' value='' />-->
                        </td>
                        <td><input class='form-control' type='number' name='team_id' value='' /></td>
                        <td><input class='btn btn-primary' type='submit' name='add_video' value='Submit' /></td>
                    </form>
                <?php } ?>
            </tr>
        </tbody>
    </table>
</div>

<!--
======================================================================
 PROFILE TEAM VIDEOS
======================================================================
-->

<div class="form-group">
    <label id="team_videos">Team Videos</label>
    <table class="table table-striped table-bordered table-hover">
        <?php profileDeleteTeamVideo(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Video ID</th>
                <th class="col-sm">Team ID</th>
                <th class="col-sm">Team Name</th>
                <th class="col-sm">Video Title</th>
                <th class="col-sm">Video Status</th>
                <th class="col-sm">Source</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $stmt_team_video_list = "SELECT DISTINCT video.id AS video_id, team.id AS team_id, team.name AS team_name, video.reference AS reference,
                    video.title AS title, video.summary AS video_summary, profile.id AS video_profile_id,
                    video.videoStatus AS video_status, video.thumbString AS thumb_string, video.videoSourceId AS video_Source_id,
                    video_source.source AS video_source, profile_franchise_sm_video_sm.id AS p_f_sm_v_sm_id FROM video
                    LEFT JOIN video_source ON video_source.id = video.videoSourceId
                    LEFT JOIN profile_franchise_sm_video_sm ON video.id=profile_franchise_sm_video_sm.videoId
                    LEFT JOIN profile_franchise_sm ON profile_franchise_sm_video_sm.profileFranchiseSmId=profile_franchise_sm.id
                    LEFT JOIN profile ON profile_franchise_sm.profileId=profile.id
                    LEFT JOIN franchise ON franchise.id=profile_franchise_sm.franchiseId
                    LEFT JOIN franchise_team_sm ON franchise_team_sm.franchiseId=franchise.id
                    LEFT JOIN team ON team.id=franchise_team_sm.teamId
                    WHERE profile.id=?
                    ORDER BY video.title";

                $prepared_team_video_list = $connection_production->prepare($stmt_team_video_list);
                $prepared_team_video_list->bind_param('i', $profile_id);
                $result_team_video_list = $prepared_team_video_list->execute();
                $result_of_query_team_video_list = $prepared_team_video_list->get_result();

                while ($row_team_video_list = mysqli_fetch_assoc($result_of_query_team_video_list)) {
                    $team_video_id = $row_team_video_list['video_id'];
                    $team_video_team_name = $row_team_video_list['team_name'];
                    $team_id = $row_team_video_list['team_id'];
                    $team_video_title = $row_team_video_list['title'];
                    $team_video_video_status = $row_team_video_list['video_status'];
                    $team_video_video_source = $row_team_video_list['video_source'];
                    $p_f_sm_v_sm_id = $row_team_video_list['p_f_sm_v_sm_id'];

                    echo "<tr>";
                    echo "<td><a href='categories.php?source=update_video&video_id=".$team_video_id."'>".$team_video_id."</a></td>";
                    echo "<td><a href='categories.php?source=team_update&team_id=".$team_id."'>".$team_id."</a></td>";
                    echo "<td>".$team_video_team_name."</td>";
                    echo "<td>".$team_video_title."</td>";
                    echo "<td>".$team_video_video_status."</td>";
                    echo "<td>".$team_video_video_source."</td>";
                    echo "<td>";

                    echo "<td><form method='post'>";
                    echo "<input type='hidden' name='delete_video_id' value=".$team_video_id.">";
                    echo "<input type='hidden' name='profile_id_set' value=".$profile_id.">";
                    echo "<input type='hidden' name='p_f_sm_v_sm_id' value=".$p_f_sm_v_sm_id.">";
                    echo "<input type='hidden' name='team_id' value=".$team_id.">";
            ?>
                    <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that? This will delete the video.');" type='submit' name='delete_video_team' value='Delete'>
            <?php
                    echo "</form></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>

<!--
======================================================================
 PROFILE SOLO ACTIVITY VIDEOS
======================================================================
-->

<div class="form-group">
    <label id="activity_videos">Activity Videos</label>
    <table class="table table-striped table-bordered table-hover">
        <?php profileDeleteActivityVideo(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Video ID</th>
                <th class="col-sm">Activity ID</th>
                <th class="col-sm">Activity</th>
                <th class="col-sm">Video Title</th>
                <th class="col-sm">Video Status</th>
                <th class="col-sm">Source</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $stmt_solo_activity_videos = "SELECT DISTINCT video.id AS video_id, video.reference AS reference, video.title AS title,
                profile_activity_sm.activityId AS activity_id, activity.activity AS activity, video.summary AS video_summary, profile.id AS video_profile_id,
                video.videoStatus AS video_status, video.thumbString AS thumb_string, video.videoSourceId AS video_source_id,
                video_source.source AS video_source, profile_activity_sm_video_sm.id AS p_a_sm_v_sm_id FROM video
                LEFT JOIN video_source ON video_source.id = video.videoSourceId
                LEFT JOIN profile_activity_sm_video_sm ON video.id=profile_activity_sm_video_sm.videoId
                LEFT JOIN profile_activity_sm ON profile_activity_sm_video_sm.profileActivitySmId=profile_activity_sm.id
                LEFT JOIN profile ON profile_activity_sm.profileId = profile.id
                LEFT JOIN activity ON profile_activity_sm.activityId = activity.id
                WHERE profile.id=?
                ORDER BY video.title";

                $prepared_solo_activity_videos = $connection_production->prepare($stmt_solo_activity_videos);
                $prepared_solo_activity_videos->bind_param('i', $profile_id);
                $result_solo_activity_videos = $prepared_solo_activity_videos->execute();
                $result_of_query_solo_activity_videos = $prepared_solo_activity_videos->get_result();

                while ($row_solo_activity_videos = mysqli_fetch_assoc($result_of_query_solo_activity_videos)) {
                    $solo_video_id = $row_solo_activity_videos['video_id'];
                    $solo_video_activity = $row_solo_activity_videos['activity'];
                    $activity_id = $row_solo_activity_videos['activity_id'];
                    $solo_video_title = $row_solo_activity_videos['title'];
                    $solo_video_video_status = $row_solo_activity_videos['video_status'];
                    $solo_video_video_source = $row_solo_activity_videos['video_source'];
                    $p_a_sm_v_sm_id = $row_solo_activity_videos['p_a_sm_v_sm_id'];

                    echo "<tr>";
                    echo "<td><a href='categories.php?source=update_video&video_id=".$solo_video_id."'>".$solo_video_id."</a></td>";
                    echo "<td><a href='categories.php?source=sports_update&activity_id=".$activity_id."'>".$activity_id."</a></td>";
                    echo "<td>".$solo_video_activity."</td>";
                    echo "<td>".$solo_video_title."</td>";
                    echo "<td>".$solo_video_video_status."</td>";
                    echo "<td>".$solo_video_video_source."</td>";

                    echo "<td><form method='post'>";
                    echo "<input type='hidden' name='solo_video_activity' value=".$solo_video_activity.">";
                    echo "<input type='hidden' name='profile_id_set' value=".$profile_id.">";
                    echo "<input type='hidden' name='p_a_sm_v_sm_id' value=".$p_a_sm_v_sm_id.">";
                    echo "<input type='hidden' name='delete_video_id' value=".$solo_video_id.">";
            ?>
                    <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that? This will delete the video.');" type='submit' name='delete_video_activity' value='Delete'>
            <?php
                    echo "</form></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>

<!--
======================================================================
 PROFILE ASSOCIATED VIDEOS
======================================================================
-->

<div class="form-group">
    <label id="profile_videos">Profile Videos</label>
    <table class="table table-striped table-bordered table-hover">
        <?php profileDeleteIndivVideo(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Video ID</th>
                <th class="col-sm">Title</th>
                <th class="col-sm">Status</th>
                <th class="col-sm">Source</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $stmt_profile_activity_videos = "SELECT DISTINCT video.id AS video_id, video.reference AS reference, video.title AS title,
                video.summary AS video_summary, profile.id AS video_profile_id, video.videoStatus AS video_status, video.thumbString AS thumb_string,
                video.videoSourceId AS video_source_id, video_source.source AS video_source, profile_video_sm.id AS profile_video_sm_id
                FROM video LEFT JOIN video_source ON video_source.id = video.videoSourceId
                LEFT JOIN profile_video_sm ON video.id=profile_video_sm.videoId
                LEFT JOIN profile ON profile_video_sm.profileId=profile.id
                WHERE profile.id=?
                ORDER BY video.title";

                $prepared_profile_activity_videos = $connection_production->prepare($stmt_profile_activity_videos);
                $prepared_profile_activity_videos->bind_param('i', $profile_id);
                $result_profile_activity_videos = $prepared_profile_activity_videos->execute();
                $result_of_query_profile_activity_videos = $prepared_profile_activity_videos->get_result();

                while ($row_profile_activity_videos = mysqli_fetch_assoc($result_of_query_profile_activity_videos)) {
                    $profile_video_id = $row_profile_activity_videos['video_id'];
                    $profile_video_title = $row_profile_activity_videos['title'];
                    $profile_video_video_status = $row_profile_activity_videos['video_status'];
                    $profile_video_video_source = $row_profile_activity_videos['video_source'];
                    $profile_video_sm_id = $row_profile_activity_videos['profile_video_sm_id'];

                    echo "<tr>";
                    echo "<td><a href='categories.php?source=update_video&video_id=".$profile_video_id."'>".$profile_video_id."</a></td>";
                    echo "<td>".$profile_video_title."</td>";
                    echo "<td>".$profile_video_video_status."</td>";
                    echo "<td>".$profile_video_video_source."</td>";

                    echo "<td><form method='post'>";
                    echo "<input type='hidden' name='p_v_sm_id' value=".$profile_video_sm_id.">";
                    echo "<input type='hidden' name='profile_id_set' value=".$profile_id.">";
                    echo "<input type='hidden' name='delete_video_id' value=".$profile_video_id.">";
            ?>
                    <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that? This will delete the video.');" type='submit' name='delete_video_indiv' value='Delete'>
            <?php
                    echo "</form></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>

<div class='form-group'>
    <label id="linked_videos">Linked Videos</label>
    <table class="table table-striped table-bordered table-hover">
        <?php profileRemoveLinkedVideo(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Video ID</th>
                <th class="col-sm">Title</th>
                <th class="col-sm">Status</th>
                <th class="col-sm">Source</th>
                <th class="col-md-1">Un-link video</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $get_linked_videos_sql = "SELECT DISTINCT video.id AS video_id, video.title AS title, video.videoStatus AS video_status,
                    video_source.source AS video_source, video_linked.id AS v_l_id FROM video
                    LEFT JOIN video_linked ON video_linked.videoId=video.id
                    LEFT JOIN video_source ON video_source.id=video.videoSourceId
                    WHERE video_linked.profileId=?
                    ORDER BY video.title";
                $get_linked_videos_stmt = $connection_production->prepare($get_linked_videos_sql);
                $get_linked_videos_stmt->bind_param("i", $profile_id);
                $get_linked_videos_stmt->execute();
                $result_get_linked_videos = $get_linked_videos_stmt->get_result();
                $get_linked_videos_stmt->close();

                while ($row_linked_video = mysqli_fetch_assoc($result_get_linked_videos)) {
                    $linked_video_id = $row_linked_video['video_id'];
                    $linked_video_title = $row_linked_video['title'];
                    $linked_video_status = $row_linked_video['video_status'];
                    $linked_video_source = $row_linked_video['video_source'];
                    $v_l_id = $row_linked_video['v_l_id'];

                    echo "<tr>";
                    echo "<td><a href='categories.php?source=update_video&video_id=".$linked_video_id."'>".$linked_video_id."</a></td>";
                    echo "<td>".$linked_video_title."</td>";
                    echo "<td>".$linked_video_status."</td>";
                    echo "<td>".$linked_video_source."</td>";

                    echo "<td><form method='post'>";
                    echo "<input type='hidden' name='v_l_id' value=".$v_l_id.">";
                    echo "<input type='hidden' name='profile_id_set' value=".$profile_id.">";
                    echo "<input type='hidden' name='linked_video_id' value=".$linked_video_id.">";
            ?>
                    <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that? This removes the association, but does not delete the video.');" type='submit' name='unlink_video' value='Remove'>
            <?php
                    echo "</form></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>
