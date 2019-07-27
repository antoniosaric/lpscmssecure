<?php include "includes/db.php"; ?>
<?php include "includes/header.php"; ?>

    <!-- Navigation -->
<?php include "includes/navigation.php"; ?>
<div class="row" style="margin-left:2vw">
    <?php include "includes/popup_saved_info.php"; ?>
</div>

    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-md-8">

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
                        }else{
                            // start loop
                            while($row = mysqli_fetch_assoc($search_query)){
                //end php
                ?>
                                <h1 class="page-header">
                                    Messages:
                                    <small></small>
                                </h1>

                                <!-- First Blog Post -->
                                <h2>
                                    <a href="#"><?php echo $row['post_title']?></a>
                                </h2>
                                <p class="lead">
                                    by <a href="index.php"><?php echo $row['first_name']." ".$row['last_name']?></a>
                                </p>
                                <p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo $row['post_date']?></p>
                                <hr>
                                <p><?php echo $row['post_content']?></p>
                                <a class="btn btn-primary" href="#">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>

                                <hr>

                                <!-- Pager -->
                                <ul class="pager">
                                    <li class="previous">
                                        <a href="#">&larr; Older</a>
                                    </li>
                                    <li class="next">
                                        <a href="#">Newer &rarr;</a>
                                    </li>
                                </ul>

                <!-- end loop php-->
                <?php }
                        }
                    }
                ?>

            </div>

            <!-- Blog Sidebar Widgets Column -->
            <?php include "includes/sidebar.php"; ?>

        </div>
        <!-- /.row -->

        <hr>

<?php include "includes/footer.php"; ?>
