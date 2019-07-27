<?php insertPost(); ?>

<form action="" method="post">
	
	<div class="form-group">
		<label for="post_title">Post Title</label>
		<input type="text" name="post_title" class="form-control">
	</div>
	
	<div class="form-group">
		<label for="post_category">Post Category</label>
	    <select name="post_category">
			<option value="">category...</option>
			<?php 
				$query = "SELECT * FROM categories";
				$select_all_categories = mysqli_query($connection, $query);

                // start loop
                while($row = mysqli_fetch_assoc($select_all_categories)){
                	$category_id = $row['id'];
                	$category_name = $row['category_name'];
                	echo "<option value=".$category_id.">".$category_name."</option>";
                }
			?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="post_content">Post Content</label>
		<textarea class="form-control" name="post_content" id="editor" ></textarea>
	</div>
	
	<div class="form-group">
		<label for="post_tags">Post Tags</label>
		<input type="text" name="post_tags" class="form-control">
	</div>
	
	<div class="form-group">
		<label for="post_status">Post Status</label>
		<input type="text" name="post_status" class="form-control">
	</div>

	<div class="form-group">
	    <input class="btn btn-primary" type="submit" name="create_post" value="Create Message">
	</div>
</form>