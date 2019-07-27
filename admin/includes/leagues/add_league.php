
<form action="" method="post">
    <?php addLeagueInfo(); ?> 
    <div class="form-group">
        <!-- <input type="hidden" name="league_id_set" value="<?php echo $league_id_set; ?>"> -->
        <div class="row">
            <div class="form-group col-xs-4">
                <label for="entity_name">Name</label>
                <input value="" type="text" class="form-control" name="entity_name" >
            </div>
            <div class="form-group col-xs-3">
                <label for="entity_alternate_name">Alternate Name</label>
                <input value="" type="text" class="form-control" name="entity_alternate_name" >
            </div>
            <div class="form-group col-xs-1">
                <label for="activity">Activity</label>
                <select name='activity'>;
                    <option value="">choose activity</option>
                    <?php 
                        $activity_query = "SELECT * FROM activity";
                        $all_activity_query = mysqli_query($connection_production, $activity_query);
                        while($row = mysqli_fetch_assoc($all_activity_query)){
                            echo "<option value=".$row['id'].">".$row['activity']."</option>";
                        }
                     ?>
                </select>
            </div> 
        </div>
        <div class="row">
            <div class="form-group col-lg-9" >
                <label for="entity_description">Description</label>
                <textarea class="form-control" name="entity_description" id="" cols="30" rows="10"></textarea>
            </div>
        </div>
        <div class="form-group">
            <input class="btn btn-primary" type="submit" name="add_league_info" value="Add League">
        </div>
    </div>
</form>