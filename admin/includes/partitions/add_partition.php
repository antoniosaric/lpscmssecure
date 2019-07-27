
<form action="" method="post">
    <?php addPartitionInfo(); ?> 
    <div class="form-group">
        <div class="row">
            <div class="form-group col-xs-4">
                <label for="partition_name">Partition Name</label>
                <input value="" type="text" class="form-control" name="partition_name" >
            </div>
            <div class="form-group col-xs-3">
                <label for="partition_alternate_name">Alternate Name</label>
                <input value="" type="text" class="form-control" name="partition_alternate_name" >
            </div>
            <div class="form-group col-xs-2">
                <label for="partition_type">Partition Type</label>
                <select name='partition_type'>;
                    <option value="">choose partition type</option>
                    <?php 
                        $partition_type_query = "SELECT * FROM partition_type";
                        $all_partition_type_query = mysqli_query($connection_production, $partition_type_query);
                        while($row = mysqli_fetch_assoc($all_partition_type_query)){
                        	if(substr($row['description'],0,24) != 'No Partition Association'){
	                            echo "<option value=".$row['id'].">".$row['type']."</option>";
	                        }
                        }
                     ?>
                </select>
            </div> 
        </div>
        <div class="row">
            <div class="form-group col-lg-9" >
                <label for="partition_description">Description</label>
                <textarea class="form-control" name="partition_description" id="" cols="30" rows="10"></textarea>
            </div>
        </div>
        <div class="form-group">
            <input class="btn btn-primary" type="submit" name="add_partition_info" value="Add Partition">
        </div>
    </div>
</form>