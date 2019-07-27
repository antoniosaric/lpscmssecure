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

                if(isset($_GET['category'])){
                    $post_category_id = $_GET['category'];
                }

                    $query = "SELECT *, posts.id AS post_id, categories.id AS category_id, accounts.id AS account_id FROM posts LEFT JOIN accounts ON accounts.id=posts.post_account_id LEFT JOIN categories ON categories.id=posts.post_category_id WHERE categories.id=".$post_category_id." ORDER BY posts.id DESC";
                    $select_all_posts_query = mysqli_query($connection, $query);

                    if($select_all_posts_query->num_rows > 0){

                        // start loop
                        while($row = mysqli_fetch_assoc($select_all_posts_query)){
                            $post_id = $row['post_id'];
                            $account_id = $row['account_id'];
                            $post_title = $row['post_title'];
                            $full_name = $row['first_name']." ".$last_name = $row['last_name'];
                            $post_date = $row['post_date'];
                            $post_content = substr($row['post_content'], 0, 100);
                ?>

                            <h1 class="page-header">
                                Messages:
                                <small></small>
                            </h1>

                            <!-- First Blog Post -->
                            <h2>
                                <a href="post.php?post_id=<?php echo $post_id; ?>"><?php echo $post_title; ?></a>
                            </h2>
                            <p class="lead">
                                by <a href="accounts.php?account_id=<?php echo $account_id;?>"><?php echo $full_name; ?></a>
                            </p>
                            <p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo $post_date;?></p>
                            <hr>
                            <p><?php echo $post_content;?></p>
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

                        <!-- end loop -->
                        <?php }

                    }else{
                        echo "NO POSTS FOUND UNDER THAT CATEGORY";
                    }

                    ?>
            </div>

            <!-- Blog Sidebar Widgets Column -->
            <?php include "includes/sidebar.php"; ?>

        </div>
        <!-- /.row -->

        <hr>

<?php include "includes/footer.php"; ?>
