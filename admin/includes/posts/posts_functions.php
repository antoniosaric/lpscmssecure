<?php

//======================================================================
// INSERT POST
//======================================================================

	function insertPost(){

		global $connection;

	    if(isset($_POST['create_post'])){
		    $post_title = $_POST['post_title'];
		    $category_id = $_POST['post_category'];
		    $post_author_id = $_SESSION['account_id'];
		    $post_content = $_POST['post_content'];
		    $post_tags = $_POST['post_tags'];
		    $post_status = $_POST['post_status'];
		    $zero = 0;

		    $query = "INSERT INTO posts(post_category_id, post_title, post_account_id, post_content, post_tags, post_comment_count, post_status) VALUES (".$category_id.", '".$post_title."', ".$post_author_id.", '".$post_content."', '".$post_tags."', ".$zero." , '".$post_status."') ";

			$insert_post = mysqli_query($connection, $query);
			$last_post_id = $connection->insert_id;
			queryCheck($insert_post);
			echo "<p class='bg-success'> POST ADDED.   <a href='../post.php?post_id=".$last_post_id."'>View Post</a>  or  <a href='posts.php?source=edit_post&post_id=".$last_post_id."'> edit last post</a> </p>";
			// header("location: posts.php");
	    }
	}

//======================================================================
// FIND ALL POSTS
//======================================================================

	function findAllPosts(){

		global $connection;

        $query = "SELECT *, posts.id AS post_id FROM posts LEFT JOIN accounts ON accounts.id=posts.post_account_id LEFT JOIN categories ON categories.id=posts.post_category_id ORDER BY posts.id DESC";
        $select_all_posts_query = mysqli_query($connection, $query);

        // start loop
        while($row = mysqli_fetch_assoc($select_all_posts_query)){
            $post_id = $row['post_id'];
            $category_name = $row['category_name'];
            $post_title = $row['post_title'];
            $full_name = $row['first_name']." ".$row['last_name'];
            $post_date = $row['post_date'];
            $post_content = $row['post_content'];
            $post_tags = $row['post_tags'];
            $post_comment_count = $row['post_comment_count'];
            $post_status = $row['post_status'];

            echo "<tr>";
            echo "<td>".$post_id."</td>";
            echo "<td>".$category_name."</td>";
            echo "<td><a href='../post.php?post_id=".$post_id."'>".$post_title."</a></td>";
            echo "<td>".$full_name."</td>";
            echo "<td>".$post_date."</td>";
            echo "<td>".$post_tags."</td>";
            echo "<td>".$post_comment_count."</td>";
            echo "<td>".$post_status."</td>";
            echo "<td><a href='posts.php?source=edit_post&post_id=".$post_id."'>Edit</a></td>";
            echo "<td><a onClick=\"javascript: return confirm('Are you sure you want to delete?');\" href='posts.php?delete=".$post_id."'>DELETE</a></td>";
            echo "</tr>";

        }                     
	}	

//======================================================================
// DELETE POST
//======================================================================

	function deletePost(){

		global $connection;

		if(isset($_GET['delete'])){
			$post_id_to_delete = $_GET['delete'];
	        $query = "DELETE FROM posts WHERE id=".$post_id_to_delete;
	        $delete_query = mysqli_query($connection, $query);
	        queryCheck($delete_query);
	        header("location: posts.php");
		}

	}

?>