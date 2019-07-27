<form action="" method="post" enctype="multipart/form-data">
    <?php
        updateImageFile();

        if (!empty($_GET['image_id'])) {
            $image_id = $_GET['image_id'];

            $img_sql = "SELECT image.id AS image_id, image.imageName AS image_name, image_type.id AS image_type_id,
                activity_image_sm.activityId AS activity_id, entity_activity_sm_image_sm.entityActivitySmId AS e_a_sm_id,
                entity_image_sm.entityId AS entity_id, institution_image_sm.institutionId AS institution_id,
                participant_image_sm.participantId AS participant_id, partition_image_sm.partitionId AS partition_id,
                profile_activity_sm_image_sm.profileActivitySmId AS p_a_sm_id, profile_franchise_sm_image_sm.profileFranchiseSmId AS p_f_sm_id,
                profile_entity_activity_sm_partition_sm_sm_image_sm.profileEntityActivitySmPartitionSmSmId AS p_e_a_sm_p_sm_id,
                profile_image_sm.profileId AS profile_id, profile_partition_sm_image_sm.profilePartitionSmId AS p_p_sm_id,
                profile_team_sm_image_sm.profileTeamSmId AS p_t_sm_id, school_image_sm.schoolId AS school_id,
                team_image_sm.teamId AS team_id FROM image
                LEFT JOIN image_type ON image_type.id = image.imageTypeId
                LEFT JOIN activity_image_sm ON activity_image_sm.imageId = image.id
                LEFT JOIN entity_activity_sm_image_sm ON entity_activity_sm_image_sm.imageId = image.id
                LEFT JOIN entity_image_sm ON entity_image_sm.imageId = image.id
                LEFT JOIN institution_image_sm ON institution_image_sm.imageId = image.id
                LEFT JOIN participant_image_sm ON participant_image_sm.imageId = image.id
                LEFT JOIN partition_image_sm ON partition_image_sm.imageId = image.id
                LEFT JOIN profile_activity_sm_image_sm ON profile_activity_sm_image_sm.imageId = image.id
                LEFT JOIN profile_entity_activity_sm_partition_sm_sm_image_sm ON profile_entity_activity_sm_partition_sm_sm_image_sm.imageId = image.id
                LEFT JOIN profile_franchise_sm_image_sm ON profile_franchise_sm_image_sm.imageId = image.id
                LEFT JOIN profile_image_sm ON profile_image_sm.imageId = image.id
                LEFT JOIN profile_partition_sm_image_sm ON profile_partition_sm_image_sm.imageId = image.id
                LEFT JOIN profile_team_sm_image_sm ON profile_team_sm_image_sm.imageId = image.id
                LEFT JOIN school_image_sm ON school_image_sm.imageId = image.id
                LEFT JOIN team_image_sm ON team_image_sm.imageId = image.id
                WHERE image.id=?";
            $img_stmt = $connection_production->prepare($img_sql);
            $img_stmt->bind_param("i", $image_id);
            $img_stmt->execute();
            $row_img = $img_stmt->get_result()->fetch_assoc();
            $img_stmt->close();

            $image_name = $row_img['image_name'];
            $image_type_id = $row_img['image_type_id'];

            $href_link = "";
            if (!!$row_img['activity_id']) {
                $linked_id = $row_img['activity_id'];
                $folder = "activity";
                $href_link = "<a href='categories.php?source=sports_update&activity_id=".$linked_id."'>";
            } else if (!!$row_img['e_a_sm_id']) {
                $linked_id = $row_img['e_a_sm_id'];
                $folder = "entity_activity_sm";
            } else if (!!$row_img['entity_id']) {
                $linked_id = $row_img['entity_id'];
                $folder = "entity";
                $href_link = "<a href='categories.php?source=league_update&entity_id=".$linked_id."'>";
            } else if (!!$row_img['institution_id']) {
                $linked_id = $row_img['institution_id'];
                $folder = "institution";
            } else if (!!$row_img['participant_id']) {
                $linked_id = $row_img['participant_id'];
                $folder = "participant";
            } else if (!!$row_img['partition_id']) {
                $linked_id = $row_img['partition_id'];
                $folder = "partition";
                $href_link = "<a href='categories.php?source=update_partition&partition_id=".$linked_id."'>";
            } else if (!!$row_img['p_a_sm_id']) {
                $linked_id = $row_img['p_a_sm_id'];
                $folder = "profile_activity_sm";
            } else if (!!$row_img['p_f_sm_id']) {
                $linked_id = $row_img['p_f_sm_id'];
                $folder = "profile_franchise_sm";
            } else if (!!$row_img['p_e_a_sm_p_sm_id']) {
                $linked_id = $row_img['p_e_a_sm_p_sm_id'];
                $folder = "profile_entity_activity_sm_partition_sm_sm";
            } else if (!!$row_img['profile_id']) {
                $linked_id = $row_img['profile_id'];
                $folder = "profile";
                $href_link = "<a href='categories.php?source=profile_update_athlete&profile_id=".$linked_id."'>";
            } else if (!!$row_img['p_p_sm_id']) {
                $linked_id = $row_img['p_p_sm_id'];
                $folder = "profile_partition_sm";
            } else if (!!$row['p_t_sm_id']) {
                $linked_id = $row_img['p_t_sm_id'];
                $folder = "profile_team_sm";
            } else if (!!$row_img['school_id']) {
                $linked_id = $row_img['school_id'];
                $folder = "school";
            } else if (!!$row_img['team_id']) {
                $linked_id = $row_img['team_id'];
                $folder = "team";
                $href_link = "<a href='categories.php?source=team_update&team_id=".$linked_id."'>";
            } else {
                $linked_id = NULL;
                $folder = "None";
            }
    ?>
            <div class="form-group">
                <input type="hidden" name="image_id" value="<?php echo $image_id; ?>" />
                <input type="hidden" name="folder" value="<?php echo $folder; ?>" />
                <div class="row">
                    <div class="form-group col-xs-3">
                        <label for="image_name">Image name</label>
                        <input class="form-control" type="text" name="image_name" value="<?php echo $image_name; ?>" required pattern='.*\S+.*' />
                    </div>
                    <div class="form-group col-xs-3">
                        <label for="image_assoc">Image association (assets/images subdirectory)</label>
                        <input class="form-control" type="text" name="image_assoc" value="<?php echo $folder; ?>" disabled />
                    </div>
                    <div class="form-group col-xs-1">
                        <label for="linked_id">Linked ID</label>
                        <?php if (!empty($href_link)) echo $href_link; ?>
                        <input class="form-control" type="number" name="linked_id" value="<?php echo $linked_id; ?>" disabled />
                        <?php if (!empty($href_link)) echo "</a>"; ?>
                    </div>
                    <div class="form-group col-xs-1">
                        <label for="type_id">Image type</label>
                        <select name="type_id" required>
                            <?php
                                $type_sql = "SELECT id, type FROM image_type";
                                $type_result = mysqli_query($connection_production, $type_sql);
                                while ($row = mysqli_fetch_assoc($type_result)) {
                                    $type_id = $row['id'];
                                    $type_name = $row['type'];
                                    echo "<option value=".$type_id.($type_id == $image_type_id ? " selected" : "").">".$type_id.": ".$type_name."</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
                    $activity_bool = strcasecmp(trim($folder), "activity") == 0;
                    showImagePreviews(array($image_name), $folder);
                ?>
                <div class="row">
                    <div class="form-group col-xs-<?php echo ($activity_bool ? 3 : 2); ?>">
                        <label>Upload new
                        <?php echo ($activity_bool ? "activity-black" : "")." image:</label>"; ?>
                        <input type="file" name="image_file[]" accept="image/*" />
                    </div>

                    <?php if ($activity_bool) { ?>
                        <div class="form-group col-xs-2">
                        <label>Upload new activity-blue image:</label>
                        <input type="file" name="image_file[]" accept="image/*" />
                    <?php } ?>
                </div>
            </div>
            <div class="form-group">
                <input class="btn btn-primary" type="submit" name="update_image" value="Update image" />
            </div>
    <?php
        } else {
            echo "<h3 style='color:red;'>please select an image</h3>";
        }
    ?>
</form>
