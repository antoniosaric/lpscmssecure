            <div class="col-md-4">

            	<?php include "includes/search.php"; ?>

                <!-- Blog Search Well -->
                <div class="well">
                    <h4>Post Search</h4>
                    <form action="search.php" method="post">
                        <div class="input-group">
                            <input name="search" type="text" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">
                                    <span class="glyphicon glyphicon-search"></span>
                                </button>
                            </span>
                        </div>
                    </form><!-- search form    --> 
                        <!-- /.input-group -->
                </div>

                <div class="well">
                    <h4>Categories</h4>
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="list-unstyled">
                            	<?php 
                            		$query = "SELECT * FROM categories";
                					$select_categories_sidebar = mysqli_query($connection, $query); 
                     				while($row = mysqli_fetch_assoc($select_categories_sidebar)){
                                        $category_name = $row['category_name'];
                                        $category_id = $row['id'];
                                        if($row['status'] == 'approved'){
                                            echo "<li><a href='category.php?category=".$category_id."'>".$category_name."</a></li>";
                                        }
                					}                      	
                            	?>
                            </ul>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>


                <!-- Side Widget Well -->
                <?php include "widget.php"; ?>

            </div>