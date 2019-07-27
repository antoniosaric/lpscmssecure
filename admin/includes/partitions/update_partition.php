<!--
======================================================================
 LEAGUE INFO
======================================================================
-->

<form action="" method="post">
    <div class="form-group">
        <?php
            updatePartitionInfo();

            if (isset($_GET['partition_id'])) {
                $partition_id_set = $_GET['partition_id'];

                $stmt = "SELECT partition_LP.id AS partition_id, partition_LP.partition AS partition_name,
                    partition_LP.description AS partition_description, alternate_name.alternateName AS alternate_name,
                    alternate_name.id AS partition_alternate_name_id, partition_alternate_name_sm.id AS partition_alternate_name_sm_id,
                    partition_LP.partitionTypeId AS partition_type_id FROM `partition` AS partition_LP
                    LEFT JOIN partition_alternate_name_sm ON partition_alternate_name_sm.partitionId=partition_LP.id
                    LEFT JOIN alternate_name ON alternate_name.id=partition_alternate_name_sm.alternateNameId
                    WHERE partition_LP.id=?";
                $prepared = $connection_production->prepare($stmt);
                $prepared->bind_param('i', $partition_id_set);
                $result = $prepared->execute();
                $result_of_query = $prepared->get_result();
                $row = $result_of_query->fetch_assoc();

                $partition_name = $row['partition_name'];
                $partition_description = $row['partition_description'];
                $partition_alternate_name = $row['alternate_name'];
                $partition_type_id = $row['partition_type_id'];
                $partition_alternate_name_id = $row['partition_alternate_name_id'];
                $partition_alternate_name_sm_id = $row['partition_alternate_name_sm_id'];
        ?>
                <input type="hidden" name="partition_id_set" value="<?php echo $partition_id_set; ?>">
                <input type="hidden" name="partition_alternate_name_id" value="<?php echo $partition_alternate_name_id; ?>">
                <input type="hidden" name="partition_type_sm_id" value="<?php echo $partition_type_id; ?>">
                <input type="hidden" name="partition_alternate_name_sm_id" value="<?php echo $partition_alternate_name_sm_id; ?>">
                <div class="row">
                    <div class="form-group col-xs-4">
                        <label for="partition_name">Name</label>
                        <input value="<?php echo $partition_name; ?>" type="text" class="form-control" name="partition_name" >
                    </div>
                    <div class="form-group col-xs-3">
                        <label for="partition_alternate_name">Alternate Name</label>
                        <input  value="<?php echo $partition_alternate_name; ?>" type="text" class="form-control" name="partition_alternate_name" >
                    </div>
		            <div class="form-group col-xs-2">
		                <label for="partition_type">Partition Type</label>
		                <select name='partition_type'>;
		                    <option value="">choose partition type</option>
		                    <?php
		                        $partition_type_query = "SELECT * FROM partition_type";
		                        $all_partition_type_query = mysqli_query($connection_production, $partition_type_query);
		                        while ($row = mysqli_fetch_assoc($all_partition_type_query)) {
		                        	if (substr($row['description'],0,24) != 'No Partition Association') {
		                        		($partition_type_id == $row['id']) ? $selected = 'SELECTED' : '';
			                            echo "<option ".$selected." value=".$row['id'].">".$row['type']."</option>";
			                        }
		                        }
		                     ?>
		                </select>
		            </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-9">
                        <label for="partition_description">Description</label>
                        <textarea class="form-control" name="partition_description" id="" cols="30" rows="10"><?php echo $partition_description; ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" name="update_partition_info" value="Update Info">
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
        <?php addImage("partition"); ?>
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
                    <input type="hidden" name="partition_id_set" value="<?php echo $_GET['partition_id']; ?>">
                    <td><input class="form-control" type='number' name='image_id' value=""></td>
                    <td><input class="form-control" type='text' name='image_name' value=""></td>
                    <td><input class='btn btn-primary' type='submit' name='add_image' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="image">Image</label>
    <table class="table table-striped table-bordered table-hover">
        <?php updateImage("partition"); ?>
        <?php deleteImage("partition"); ?>
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
                if (isset($_GET['partition_id'])) {
                    $partition_id = $_GET['partition_id'];

                    $stmt_image = "SELECT DISTINCT image.imageName AS imageName, image.id AS image_id,
                        partition_image_sm.id AS partition_image_sm_id FROM `partition` AS partition_LP
                        LEFT JOIN partition_image_sm ON partition_LP.id=partition_image_sm.partitionId
                        LEFT JOIN image ON partition_image_sm.imageId=image.id
                        WHERE partition_LP.id=?";
                    $prepared_image = $connection_production->prepare($stmt_image);
                    $prepared_image->bind_param('i', $partition_id);
                    $result_image = $prepared_image->execute();
                    $result_of_query_image = $prepared_image->get_result();

                    if (!!$result_image && $result_of_query_image->num_rows > 0) {
                        while ($row_image = $result_of_query_image->fetch_assoc()) {
                            if (!!$row_image['image_id']) {
                                $query_imageName = $row_image['imageName'];
                                $query_partition_image_sm_id = $row_image['partition_image_sm_id'];
                                $query_image_id = $row_image['image_id'];
                                array_push($img_names, $query_imageName);

                                echo "<form method='post'>";
                                echo "<tr>";
                                echo "<td>".$query_image_id."</td>";
                                echo "<td><div class='form-group'>";
                                echo "<input value='".$query_imageName."' type='text' class='form-control' name='image_name'>";
                                echo "</div></td>";

                                echo "<input type='hidden' name='image_id' value=".$query_image_id.">";
                                echo "<input type='hidden' name='partition_id_set' value=".$partition_id.">";
                                echo "<td><input class='btn btn-info' type='submit' name='update_image' value='Update'></td>";
                                echo "</form>";

                                echo "<form method='post'>";
                                echo "<input type='hidden' name='delete_image_id' value=".$query_image_id.">";
                                echo "<input type='hidden' name='delete_partition_image_sm_id' value=".$query_partition_image_sm_id.">";
                                echo "<input type='hidden' name='partition_id_set' value=".$partition_id.">";
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
    <?php showImagePreviews($img_names, "partition"); ?>
</div>

<!--
======================================================================
 LEAGUES
======================================================================
-->

<div class="form-group">
    <label id="add_partition_entity">Add Entity</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addEntityToPartition(); ?>
        <thead>
            <tr>
                <th class='col-md-1'>Entity ID</th>
                <th>Add Entity</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post'>
                    <input type='hidden' name='partition_id_set' value="<?php  echo $_GET['partition_id']?>">
                    <td><input type='number' name='entity_id' value='' required></td>
                    <td><input class='btn btn-primary' type='submit' name='add_entity_partition' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="partition_entity">Entities (leagues)</label>
    <table class="table table-striped table-bordered table-hover">
        <?php deleteEntityFromPartition(); ?>
        <thead>
            <tr>
                <th class="col-md-1">League ID</th>
                <th class="col-sm-2">League Name</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if (isset($_GET['partition_id'])) {
                    $partition_id = $_GET['partition_id'];

                    $stmt_partition_league = "SELECT DISTINCT entity.id AS entity_id, entity.name AS entity_name,
                        entity_activity_sm_partition_sm.id AS entity_activity_sm_partition_sm_id FROM entity
                        LEFT JOIN entity_activity_sm ON entity_activity_sm.entityId=entity.id
                        LEFT JOIN entity_activity_sm_partition_sm ON entity_activity_sm_partition_sm.entityActivitySmId=entity_activity_sm.id
                        LEFT JOIN `partition` AS partition_LP ON partition_LP.id=entity_activity_sm_partition_sm.partitionId
                        WHERE partition_LP.id=?";
                    $prepared_partition_league = $connection_production->prepare($stmt_partition_league);
                    $prepared_partition_league->bind_param('i', $partition_id);
                    $prepared_partition_league->execute();
                    $result_partition_league = $prepared_partition_league->get_result();
                    $prepared_partition_league->close();

                    while ($row_partition_league = $result_partition_league->fetch_assoc()) {
                        $entity_id = $row_partition_league['entity_id'];
                        $entity_name = $row_partition_league['entity_name'];
                        $e_a_sm_p_sm_id = $row_partition_league['entity_activity_sm_partition_sm_id'];

                        echo "<tr>";
                        echo "<td><a href='categories.php?source=league_update&entity_id=".$entity_id."'>".$entity_id."</a></td>";
                        echo "<td>".$entity_name."</td>";

                        echo "<td><form method='post'>";
                        echo "<input type='hidden' name='e_a_sm_p_sm_id' value=".$e_a_sm_p_sm_id.">";
                        echo "<input type='hidden' name='entity_id' value=".$entity_id.">";
                        echo "<input type='hidden' name='partition_id_set' value=".$partition_id.">";
            ?>
                        <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_entity_partition' value='Delete'>
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

<div class="form-group">
    <label id="add_partition_team">Add Team</label>
    <table class="table table-striped table-bordered table-hover">
        <?php addTeamToPartition(); ?>
        <thead>
            <tr>
                <th class='col-md-1'>Team ID</th>
                <th>Add Team</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <form method='post'>
                    <input type='hidden' name='partition_id_set' value="<?php  echo $_GET['partition_id']?>">
                    <td><input type='number' name='team_id' value='' required></td>
                    <td><input class='btn btn-primary' type='submit' name='add_team_partition' value='Add'></td>
                </form>
            </tr>
        </tbody>
    </table>

    <label id="partition_teams">Teams (conference, division, league, etc.)</label>
    <table class="table table-striped table-bordered table-hover">
        <?php deleteTeamFromPartition(); ?>
        <thead>
            <tr>
                <th class="col-md-1">Team ID</th>
                <th class="col-sm-2">Team Locale/Name</th>
                <th class="col-sm-2">Team Status</th>
                <th class="col-md-1">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if (isset($_GET['partition_id'])) {
                    $partition_id = $_GET['partition_id'];

                    $stmt_leagues_team = "SELECT DISTINCT team.id AS team_id, team.status AS team_status,
                        team.locale AS locale, team.name AS team_name, partition_franchise_sm.id AS partition_franchise_sm_id FROM team
                        LEFT JOIN franchise_team_sm ON franchise_team_sm.teamId=team.id
                        LEFT JOIN franchise ON franchise.id=franchise_team_sm.franchiseId
                        LEFT JOIN partition_franchise_sm ON partition_franchise_sm.franchiseId=franchise.id
                        LEFT JOIN `partition` AS partition_LP ON partition_LP.id=partition_franchise_sm.partitionId
                        WHERE partition_LP.id=?";
                    $prepared_leagues_team = $connection_production->prepare($stmt_leagues_team);
                    $prepared_leagues_team->bind_param('i', $partition_id);
                    $prepared_leagues_team->execute();
                    $result_leagues_team = $prepared_leagues_team->get_result();
                    $prepared_leagues_team->close();

                    while ($row_leagues_team = $result_leagues_team->fetch_assoc()) {
                        $team_id = $row_leagues_team['team_id'];
                        $team_status = $row_leagues_team['team_status'];
                        $locale = $row_leagues_team['locale'];
                        $team_name = $row_leagues_team['team_name'];
                        $p_f_sm_id = $row_leagues_team['partition_franchise_sm_id'];

                        echo "<tr>";
                        echo "<td><a href='categories.php?source=team_update&team_id=".$team_id."'>".$team_id."</a></td>";
                        echo "<td>".$locale." ".$team_name."</td>";
                        echo "<td>".$team_status."</td>";

                        echo "<td><form method='post'>";
                        echo "<input type='hidden' name='p_f_sm_id' value=".$p_f_sm_id.">";
                        echo "<input type='hidden' name='team_id' value=".$team_id.">";
                        echo "<input type='hidden' name='partition_id_set' value=".$partition_id.">";
            ?>
                        <input class='btn btn-danger' onClick="return confirm('Are you sure you want to do that?');" type='submit' name='delete_team_partition' value='Delete'>
            <?php
                        echo "</form></td>";
                        echo "</tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>
