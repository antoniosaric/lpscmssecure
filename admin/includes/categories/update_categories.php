<form action="" method="post">
    <div class="form-group">
        <?php
            if(isset($_GET['category_id'])){
                $category_id = $_GET['category_id'];
                $stmt = "SELECT category_name FROM categories WHERE id=?";
                $prepared = $connection->prepare($stmt);
                $prepared->bind_param('i', $category_id);
                $result = $prepared->execute();
                $result_of_query = $prepared->get_result();
                $row = $result_of_query->fetch_assoc();
                $category_name = $row['category_name'];

                if(!$category_name){
                    echo "<h3 style='color:red;'>Category Not found </h3>";  
                }
        ?>
                    <div class="form-group">
                        <label for="category-name">Edit Category</label>
                        <input value="<?php echo $category_name; ?>" type="text" class="form-control" name="category_name" >
                    </div>
                    <div class="form-group">
                        <input class="btn btn-primary" type="submit" name="update_category" value="Update Category">
                    </div>
        <?php  
            }
            //delte query
            if(isset($_POST['update_category'])){
                $category_name = $_POST['category_name'];

                $update_string = 'category: '.$_POST['category_name'];

                insertChange( $_SESSION['account_id'], 'category', 'update', $category_id, $update_string);

                $stmt = "UPDATE categories SET category_name=? WHERE id=?";
                $prepared = $connection->prepare($stmt);
                $prepared->bind_param('si', $category_name, $category_id );
                if(!$prepared->execute()){
                    die('QUERY FAILED' . mysqli_error($connection));
                }

                header("location: categories.php");
            }
        ?>        
    </div>
</form>