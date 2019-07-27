<?php  

//======================================================================
// CHANGE CATEGORY STATUS
//======================================================================

	function changeCategoryStatus(){
		global $connection;

        if(isset($_POST['update_activity_status'])){
	        $status_changed_to = $_POST['status_changed_to'];
	        $category_id = $_POST['category_id'];

			$stmt = "UPDATE categories SET status=? WHERE id=?";
            $prepared = $connection->prepare($stmt);
            $prepared->bind_param('si', $status_changed_to, $category_id);
            $result = $prepared->execute();
			queryCheck($result);
			header("location: categories.php");
        }
	}

//======================================================================
// INSERT CATEGORY
//======================================================================

	function insertCategories(){

		global $connection;

	    if(isset($_POST['submit'])){
	        $category_name = $_POST['category_name'];
	        if($category_name =="" || empty($category_name)){
	            echo "This field should not be empty";
	        }else{
	            $stmt = "INSERT INTO categories(category_name) VALUE(?)";
	            $prepared = $connection->prepare($stmt);
            	$prepared->bind_param('s', $category_name);
            	$result = $prepared->execute();
				queryCheck($result);
            	header("location: categories.php");
	        }
	    }
	}

//======================================================================
// FIND ALL CATEGORIES
//======================================================================

	function findAllCategories(){

		global $connection;

        //find all catergories query
        $query = "SELECT * FROM categories";
        $select_categories_sidebar = mysqli_query($connection, $query); 

        while($row = mysqli_fetch_assoc($select_categories_sidebar)){
            echo "<tr>";
            echo "<td>".$row['id']."</td>";
            echo "<td><a href='categories.php?source=".$row['category_name']."'>".$row['category_name']."</a></td>";
            echo "<td>".$row['status']."</td>";
            if($row['status']=='approved'){
	            echo "<form method='post'><input type='hidden' name='category_id' value=".$row['id']."><input type='hidden' name='status_changed_to' value='unapproved'>";
	            echo "<td><input class='btn btn-primary' type='submit' name='update_activity_status' value='unapproved'></td>";
	            echo "</form>";



	            // echo "<td><a class='btn btn-primary' href='categories.php?update_activity_status=unapproved&activity_id=".$row['id']."'>unapprove</a></td>";
            }else{
	            echo "<form method='post'><input type='hidden' name='category_id' value=".$row['id']."><input type='hidden' name='status_changed_to' value='approved'>";
	            echo "<td><input class='btn btn-warning' type='submit' name='update_activity_status' value='approved'></td>";
	            echo "</form>";

	            // echo "<td><a class='btn btn-warning' href='categories.php?update_activity_status=approved&activity_id=".$row['id']."'>approve</a></td>";
            }
            // echo "<td><a href='categories.php?source=update_categories&edit=".$row['id']."'>Edit</a></td>";
            // echo "<td><a onClick=\"javascript: return confirm('Are you sure you want to delete?');\" href='categories.php?delete=".$row['id']."'>DELETE</a></td>";

            echo "<td><a class='btn btn-info' href='categories.php?source=update_categories&category_id=".$row['id']."'>Edit</a></td>";
            echo "<form method='post'><input type='hidden' name='category_id' value=".$row['id'].">";
            echo "<td><input class='btn btn-danger' type='submit' name='delete_category' value='delete'></td>";
            echo "</form>";
            echo "</tr>";
        }                       
	}

//======================================================================
// DELETE CATEGORY
//======================================================================

	function deleteCategory(){

		global $connection;

        if(isset($_POST['delete_category'])){
        	$category_id = $_POST['category_id'];
            $stmt = "DELETE FROM categories WHERE id=?";
            $prepared = $connection->prepare($stmt);
        	$prepared->bind_param('i', $category_id);
        	$result = $prepared->execute();
			queryCheck($result);

            $update_string = 'category: '.$_POST['category_name'];

            insertChange( $_SESSION['account_id'], 'category', 'update', $category_id, $update_string);


            header("location: categories.php");
        }

	}




?>