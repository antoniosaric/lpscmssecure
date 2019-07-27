<?php insertActivity(); ?>

<form action="" method="post">

	<div class="form-group">
		<label for="activity_title">Activity Name</label>
		<input type="text" name="activity_name" class="form-control" required pattern='.*\S+.*'>
	</div>

	<div class="form-group">
		<label for="activity_tags">Activity Image</label>
		<input type="text" name="activity_image" class="form-control">
	</div>

	<div class="form-group">
		<label for="activity_description">Activity Description</label>
		<textarea class="form-control" name="activity_description" id="" cols="30" rows="10"></textarea>
	</div>

	<div class="form-group">
	    <input class="btn btn-primary" type="submit" name="create_activity" value="Create Activity">
	</div>
</form>


