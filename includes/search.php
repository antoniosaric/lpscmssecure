				<?php 

					if(isset($_POST['search'])){

						$query_string = preg_replace('/\s+/', '|', trim($_POST['search']));

						$query = "SELECT DISTINCT *, posts.post_tags AS post_tags, accounts.first_name AS first_name, accounts.last_name AS last_name, posts.post_content AS post_content FROM posts LEFT JOIN accounts ON accounts.id=posts.post_account_id WHERE posts.post_tags RLIKE '".$query_string."' OR accounts.first_name RLIKE '".$query_string."' OR accounts.last_name RLIKE '".$query_string."' OR posts.post_content RLIKE '".$query_string."'";

						$search_query = mysqli_query($connection, $query);

						if(!$search_query){
							die("QUERY FAILED" . mysqli_error($connection));

						}

						$count = mysqli_num_rows($search_query);
						if($count == 0){
							echo "<h1> NO RESULT</h1>";
						}
					}


				?>