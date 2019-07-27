

<?php 

	if(isset($_GET['post_id'])){
        $post_id_to_update = $_GET['post_id'];
		
        $query = "SELECT *, posts.id AS post_id, categories.id AS category_id, accounts.id AS account_id FROM posts LEFT JOIN accounts ON accounts.id=posts.post_account_id LEFT JOIN categories ON categories.id=posts.post_category_id WHERE posts.id=".$post_id_to_update;
        $select_post_for_update = mysqli_query($connection, $query);
        $row = mysqli_fetch_assoc($select_post_for_update);
        $post_id = $row['post_id'];
        $category_name = $row['category_name'];
        $category_id_set = $row['category_id'];
        $post_title = $row['post_title'];
        $account_id_set = $row['account_id'];
        $full_name = $row['first_name']." ".$row['last_name'];
        $post_date = $row['post_date'];
        $post_content = $row['post_content'];
        $post_tags = $row['post_tags'];
        $post_comment_count = $row['post_comment_count'];
        $post_status = $row['post_status'];

    }

	if(isset($_POST['update_post'])){
    	$post_id = $post_id_to_update;
	    $category_id = $_POST['post_category'];
	    $post_title = $_POST['post_title'];
	    $post_author_id = $_POST['post_author'];
	    $post_content = $_POST['post_content'];
	    $post_tags = $_POST['post_tags'];
	    $post_status = $_POST['post_status'];

	    $query = "UPDATE posts SET post_category_id=".$category_id.", post_title='".$post_title."', post_account_id=".$post_author_id.", post_content='".$post_content."', post_tags='".$post_tags."', post_status='".$post_status."' WHERE id=".$post_id;

		$update_post = mysqli_query($connection, $query);
		queryCheck($update_post);

		header("location: posts.php");
    }
?>

<form action="" method="post">
	
	<div class="form-group">
		<label for="post_title">Post Title</label>
		<input type="text" name="post_title" class="form-control" value="<?php echo $post_title; ?>">
	</div>
	
	<div class="form-group">
		<label for="post_category">Post Category</label>
	    <select name="post_category" value="<?php echo $category_id_set; ?>">
				<option value="">category...</option>
				<?php 
					$query = "SELECT * FROM categories";
					$select_all_categories = mysqli_query($connection, $query);

					echo "inside";
                    // start loop
                    while($row = mysqli_fetch_assoc($select_all_categories)){
                    	$category_id = $row['id'];
                    	$category_name = $row['category_name'];
                    	$category_id_set == $category_id ? $selected = "SELECTED" : $selected = "" ;
                    	echo "<option ".$selected." value=".$category_id.">".$category_name." </option>";
                    }
				?>
		</select>
	</div>
	<br>
	<div class="form-group">
		<label for="post_author">Post Author</label>
	    <select name="post_author" value="<?php echo $account_id_set; ?>">
				<option value="">account...</option>
				<?php 
					$query = "SELECT * FROM accounts";
					$select_all_accounts = mysqli_query($connection, $query);

                    // start loop
                    while($row = mysqli_fetch_assoc($select_all_accounts)){
                    	$account_id = $row['id'];
                    	$full_name = $row['first_name']." ".$row['last_name'];
                    	$account_id_set == $account_id ? $selected = "SELECTED" : $selected = "" ;
                    	echo "<option ".$selected." value=".$account_id.">".$full_name." </option>";
                    }
				?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="post_content">Post Content</label>
		<textarea class="form-control" name="post_content" id="editor"><?php echo $post_content; ?></textarea>
	</div>
	
	<div class="form-group">
		<label for="post_tags">Post Tags</label>
		<input type="text" name="post_tags" class="form-control" value="<?php echo $post_tags; ?>">
	</div>
	
	<div class="form-group">
		<label for="post_status">Post Status</label>
		<input type="text" name="post_status" class="form-control" value="<?php echo $post_status; ?>">
	</div>

	<div class="form-group">
	    <input class="btn btn-primary" type="submit" name="update_post" value="Update Message">
	</div>
</form>