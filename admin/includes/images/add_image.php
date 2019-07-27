<form action="" method="post" enctype="multipart/form-data">
    <?php uploadImage(); ?>
    <div class="form-group">
        <div class="row">
            <div class="form-group col-xs-3">
                <label for="image_name">Image name</label>
                <input class="form-control" type="text" name="image_name" value="" required pattern='.*\S+.*' />
            </div>
            <div class="form-group col-xs-3">
                <script>
                    function activToggle(selectIndex) {
                        var elems = document.getElementsByClassName('activDiv');
                        for (var i = 0; i < elems.length; i++) {
                            if (selectIndex == 1) elems[i].style.display = 'inline-block';
                            else elems[i].style.display = 'none';
                        }
                        document.getElementById('blue_input').required = (selectIndex == 1) ? true : false;
                    }
                </script>
                <label for="image_assoc">Image association (assets/images subdirectory)</label>
                <select name="image_assoc" onchange="activToggle(this.selectedIndex);" required>
                    <option value="" disabled selected>Choose type</option>
                    <option value="activity:activityId:activity_image_sm">Activity</option>
                    <option value="entity:entityId:entity_image_sm">Entity</option>
                    <option value="partition:partitionId:partition_image_sm">Partition</option>
                    <option value="profile:profileId:profile_image_sm">Profile</option>
                    <option value="participant:participantId:participant_image_sm">Participant</option>
                    <option value="team:teamId:team_image_sm">Team</option>
                    <option value="institution:institutionId:institution_image_sm">Institution</option>
                    <option value="school:schoolId:school_image_sm">School</option>
                    <option value="" disabled />
                    <option value="entity_activity_sm:entityActivitySmId:entity_activity_sm_image_sm">entity_activity_sm</option>
                    <option value="profile_activity_sm:profileActivitySmId:profile_activity_sm_image_sm">profile_activity_sm</option>
                    <option value="profile_franchise_sm:profileFranchiseSmId:profile_franchise_sm_image_sm">profile_franchise_sm</option>
                    <option value="profile_entity_activity_sm_partition_sm_sm:profileEntityActivitySmPartitionSmSmId:profile_entity_activity_sm_partition_sm_sm_image_sm">
                        profile_entity_activity_sm_partition_sm_sm</option>
                    <option value="profile_partition_sm:profilePartitionSmId:profile_partition_sm_image_sm">profile_partition_sm</option>
                    <option value="profile_team_sm:profileTeamSmId:profile_team_sm_image_sm">profile_team_sm</option>
                </select>
            </div>
            <div class="form-group col-xs-1">
                <label for="linked_id">Linked ID</label>
                <input class="form-control" type="number" name="linked_id" value="" required min="1" />
            </div>
            <div class="form-group col-xs-1">
                <label for="type_id">Image type</label>
                <select name="type_id" required>
                    <option value="" disabled selected>Choose type</option>
                    <?php
                        $type_sql = "SELECT id, type FROM image_type";
                        $type_result = mysqli_query($connection_production, $type_sql);
                        while ($row = mysqli_fetch_assoc($type_result)) {
                            $type_id = $row['id'];
                            $type_name = $row['type'];
                            echo "<option value=".$type_id.">".$type_id.": ".$type_name."</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-2" style="margin-right:3vw">
                <label>Select image to upload:</label>
                <label class="activDiv" style="display:none">Activity-black image:</label>
                <input type="file" name="image_file[]" accept="image/*" required />
            </div>
            <div class="activDiv form-group col-xs-2" style="display:none">
                <label><br>Activity-blue image:</label>
                <input type="file" name="image_file[]" id="blue_input" accept="image/*" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="upload_image" value="Upload image" />
    </div>
</form>
