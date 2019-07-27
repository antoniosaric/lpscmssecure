<?php

//======================================================================
// QUERY CHECK
//======================================================================

	function queryCheck($result){
		global $connection;

		if(!$result){
			die("QUERY FAILED " . mysqli_error($connection));
		}
	}

//======================================================================
// INSERT CHANGES
//======================================================================

	function insertChange($account_id, $category, $type ,$affected_id, $change_record){
		global $connection;

        $stmt = "INSERT INTO changes(account_id, category_changed, change_type, affected_id, change_record) VALUE(?, ?, ?, ?, ?)";
        $prepared = $connection->prepare($stmt);
        $prepared->bind_param('issis', $account_id, $category, $type, $affected_id, $change_record);
        $result = $prepared->execute();
        queryCheck($result);
	}

//======================================================================
// WIDGET COUNT
//======================================================================

	function widgetCount($target){
		global $connection;
		$new_target = mysqli_real_escape_string($connection, $target);
		$stmt = "SELECT * FROM ".$new_target;
		$select_all_count = mysqli_query($connection, $stmt);
		queryCheck($select_all_count);
		return mysqli_num_rows($select_all_count);
	}

//======================================================================
// FIND ALL CHANGES
//======================================================================

	function findAllChanges(){

		global $connection;

        //find all catergories query
        $query = "SELECT *, changes.id AS change_id, accounts.id AS account_id FROM changes LEFT JOIN accounts on accounts.id=changes.account_id ORDER BY changes.id DESC";
        $find_all_changes = mysqli_query($connection, $query);

        while($row = mysqli_fetch_assoc($find_all_changes)){
        	$changed_record = substr($row['change_record'], 0, 50);

            echo "<tr>";
            echo "<td>".$row['change_id']."</td>";
            echo "<td>".$row['first_name']." ".$row['last_name']."</td>";
            echo "<td>".$row['category_changed']."</td>";
            echo "<td>".$row['change_type']."</td>";
            if($row['category_changed'] == 'profile'){
            	echo "<td><a href='categories.php?source=profile_update_athlete&profile_id=".$row['affected_id']."'>".$row['affected_id']."</a></td>";
            }else if($row['category_changed'] == 'category'){
            	echo "<td><a href='categories.php?source=update_categories&category_id=".$row['affected_id']."'>".$row['affected_id']."</a></td>";
            }else if($row['category_changed'] == 'activity'){
            	echo "<td><a href='categories.php?source=sports_update&activity_id=".$row['affected_id']."'>".$row['affected_id']."</a></td>";
            }else if($row['category_changed'] == 'team'){
                echo "<td><a href='categories.php?source=team_update&team_id=".$row['affected_id']."'>".$row['affected_id']."</a></td>";
            }else if($row['category_changed'] == 'video'){
				echo "<td><a href='categories.php?source=update_video&video_id=".$row['affected_id']."'>".$row['affected_id']."</a></td>";
			}else if($row['category_changed'] == 'entity'){
                echo "<td><a href='categories.php?source=league_update&entity_id=".$row['affected_id']."'>".$row['affected_id']."</a></td>";
            }else if($row['category_changed'] == 'partition'){
                echo "<td><a href='categories.php?source=update_partition&partition_id=".$row['affected_id']."'>".$row['affected_id']."</a></td>";
            } else if ($row['category_changed'] == 'image') {
				echo "<td><a href='categories.php?source=update_image&image_id=".$row['affected_id']."'>".$row['affected_id']."</a></td>";
			} else {
	            echo "<td>".$row['affected_id']."</td>";
            }
            echo "<td>".$changed_record."</td>";
            echo "<td><a href='changes.php?source=view_change&change_id=".$row['change_id']."'>View Change</a></td>";
            echo "</tr>";
        }
	}

//======================================================================
// FIND ALL REQUESTS
//======================================================================

    function findAllRequests(){

        global $connection;

        //find all requests query
        $query = "SELECT *, requests.id AS request_id, accounts.first_name AS first_name, accounts.last_name AS last_name FROM requests LEFT JOIN accounts on accounts.id=requests.account_id ORDER BY status DESC";
        $select_requests_sidebar = mysqli_query($connection, $query); 

        while($row = mysqli_fetch_assoc($select_requests_sidebar)){
            echo "<tr>";
            echo "<td>".$row['request_id']."</td>";
            if($row['category_type'] == 'profile'){
                echo "<td><a href='categories.php?source=profile_update_athlete&profile_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
            }else if($row['category_type'] == 'category'){
                echo "<td><a href='categories.php?source=update_categories&category_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
            }else if($row['category_type'] == 'activity'){
                echo "<td><a href='categories.php?source=sports_update&activity_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
            }else if($row['category_type'] == 'teams'){
                echo "<td><a href='categories.php?source=team_update&team_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
            }else if($row['category_type'] == 'videos'){
                echo "<td><a href='categories.php?source=update_video&video_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
            }else if($row['category_type'] == 'leagues'){
                echo "<td><a href='categories.php?source=league_update&entity_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
            }else if($row['category_type'] == 'partitions'){
                echo "<td><a href='categories.php?source=update_partition&partition_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
            } else if ($row['category_type'] == 'images') {
                echo "<td><a href='categories.php?source=update_image&image_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
            } else {
                echo "<td>".$row['category_id']."</td>";
            }
            echo "<td>".$row['category_type']."</td>";
            echo "<td>".$row['first_name']." ".$row['last_name']."</td>";
            echo "<td><div class='noverflow'>".$row['given_reason']."</div></td>";
            echo "<td>".$row['status']."</td>";
            echo "<td><a class='btn btn-info' href='requests.php?source=view_request&category_type=".$row['category_type']."&category_id=".$row['category_id']."&request_id=".$row['request_id']."'>Edit</a></td>";
            echo "</tr>";
        }                       
    }


//======================================================================
// INSERT COMMENT
//======================================================================

	function insertComment(){

		global $connection;

	    if(isset($_POST['create_post'])){
		    $post_title = $_POST['post_title'];
		    $category_id = $_POST['post_category'];
		    $post_author_id = $_POST['post_author'];
		    $post_content = $_POST['post_content'];
		    $post_tags = $_POST['post_tags'];
		    $post_status = $_POST['post_status'];
		    $zero = 0;

		    $query = "INSERT INTO posts(post_category_id, post_title, post_account_id, post_content, post_tags, post_comment_count, post_status) VALUES (".$category_id.", '".$post_title."', ".$post_author_id.", '".$post_content."', '".$post_tags."', ".$zero." , '".$post_status."') ";

			$insert_post = mysqli_query($connection, $query);
			queryCheck($insert_post);
			header("location: posts.php");
	    }
	}

//======================================================================
// FIND ALL COMMENTS
//======================================================================

	function findAllComments(){

		global $connection;

        $query = "SELECT *, comments.id AS comment_id, posts.post_title AS post_title, accounts.first_name AS first_name, accounts.last_name AS last_name, comments.comment_date AS comment_date, comments.comment_content AS comment_content FROM comments LEFT JOIN accounts ON accounts.id=comments.comment_account_id LEFT JOIN posts ON posts.id=comments.comment_post_id ORDER BY comments.id DESC";
        $select_all_comments_query = mysqli_query($connection, $query);

        // start loop
        while($row = mysqli_fetch_assoc($select_all_comments_query)){
            $comment_id = $row['comment_id'];
            $comment_post_id = $row['comment_post_id'];
            $post_title = $row['post_title'];
            $full_name = $row['first_name']." ".$row['last_name'];
            $comment_date = $row['comment_date'];
            $comment_content = $row['comment_content'];

            echo "<tr>";
            echo "<td>".$comment_id."</td>";
            echo "<td><a href='../post.php?post_id=".$comment_post_id."'>".$post_title." </a></td>";
            echo "<td>".$full_name."</td>";
            echo "<td>".$comment_date."</td>";
            echo "<td>".$comment_content."</td>";
            echo "<td><a href='posts.php?source=edit_comment&comment_id=".$comment_id."'>Edit</a></td>";
            echo "<td><a onClick=\"javascript: return confirm('Are you sure you want to delete?');\" href='posts.php?delete_comment=".$comment_id."&post_id=".$comment_post_id."'>DELETE</a></td>";
            echo "</tr>";

        }
	}

//======================================================================
// DELETE COMMENT
//======================================================================

	function deleteComment(){

		global $connection;

		if(isset($_GET['delete_comment'])){
			$comment_id_to_delete = $_GET['delete_comment'];
			$post_id = $_GET['post_id'];

	        $query = "DELETE FROM comments WHERE id=".$comment_id_to_delete;
	        $delete_query = mysqli_query($connection, $query);

	        queryCheck($delete_query);

            $query = "SELECT count(*), posts.id AS post_id FROM comments LEFT JOIN posts on posts.id=comments.comment_post_id WHERE posts.id=".$post_id;
            $increment_comment_count_query = mysqli_query($connection, $query);
            $comment_count = mysqli_fetch_assoc($increment_comment_count_query);
            $comment_count_new = $comment_count['count(*)'] - 1;

            $query = "UPDATE posts SET post_comment_count=".$comment_count_new." WHERE posts.id=".$post_id;
            $update_comment_count_query = mysqli_query($connection, $query);

	        queryCheck($update_comment_count_query);

	        header("location: posts.php?source=comments");
		}

	}


?>
