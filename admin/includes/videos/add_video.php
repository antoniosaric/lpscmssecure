<form action="" method="post">
    <?php addVideoInfo(); ?>
    <div class="form-group">
        <div class="row">
            <div class="form-group col-xs-4">
                <label for="reference">Reference</label>
                <input class="form-control" type="text" name="reference" value="" required pattern='.*\S+.*' />
            </div>
            <div class="form-group col-xs-1">
                <label for="source_id">Source ID</label>
                <select name="source_id" required>
                    <option value="" disabled selected>Choose source</option>
                    <?php
                        $source_query = "SELECT id, source FROM video_source
                            ORDER BY video_source.id ASC";
                        $source_result = mysqli_query($connection_production, $source_query);
                        while ($row = mysqli_fetch_assoc($source_result)) {
                            $source_id = $row['id'];
                            $source_name = $row['source'];
                            echo "<option value=".$source_id.">".$source_id.": ".$source_name."</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-4">
                <label for="title">Title</label>
                <input class="form-control" type="text" name="title" value="" required pattern='.*\S+.*' />
            </div>
            <div class="form-group col-xs-4">
                <label for="summary">Summary</label>
                <input class="form-control" type="text" name="summary" value="" />
            </div>
            <div class="form-group col-xs-1">
                <label for="status">Status</label>
                <select name="status" required>
                    <option value="complete" selected>Complete</option>
                    <option value="incomplete">Incomplete</option>
                    <option value="deadlink">Dead link</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="add_video_info" value="Add video" />
    </div>
</form>
