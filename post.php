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

                    if(isset($_GET['post_id'])){
                        $post_id = $_GET['post_id'];
                    }


                    $stmt = mysqli_prepare($connection, "SELECT posts.id AS post_id, accounts.id AS account_id, posts.post_title AS post_title, accounts.first_name AS first_name, accounts.last_name AS last_name, posts.post_date AS post_date, posts.post_content AS post_content FROM posts LEFT JOIN accounts ON accounts.id=posts.post_account_id WHERE posts.id=?");

                    mysqli_stmt_bind_param($stmt, 'i', $post_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_Stmt_bind_Result($stmt, $post_id, $account_id, $post_title, $first_name, $last_name, $post_date, $post_content);

                    // start loop
                    while(mysqli_stmt_fetch($stmt)){
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
                            <?php
                                echo "by <a href='accounts.php?account_id=".$account_id."'>".$first_name." ".$last_name."</a>";

                                if(isset($_GET['post_id'])){
                                    $post_id = $_GET['post_id'];
                                    if( $_SESSION['access'] === 'admin' ){
                                        echo " <a href='admin/posts.php?source=edit_post&post_id=".$post_id."'><i class='fa fa-edit'></i></a>";
                                    }
                                }
                            ?>
                        </p>
                        <p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo $post_date;?></p>
                        <hr>
                        <p><?php echo $post_content;?></p>
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
                    <?php } ?>

                <!-- Blog Comments -->
                <?php
                    if(isset($_POST['create_comment'])){

                        if(!!$_POST['comment_content']){
                            $post_id = $_GET['post_id'];
                            $comment_account_id = $_SESSION['account_id'];
                            $comment_content = $_POST['comment_content'];

                            $query = "INSERT INTO comments(comment_post_id, comment_account_id, comment_content) VALUES( ".$post_id.", ".$comment_account_id.", '".$comment_content."')";

                            $query_add_comment = mysqli_query($connection, $query);

                            if(!$query_add_comment){
                                die('QUERY FAILED' . mysqli_error($connection));
                            }else{
                                $query = "SELECT count(*) FROM comments LEFT JOIN posts on posts.id=comments.comment_post_id WHERE posts.id=".$post_id;
                                $increment_comment_count_query = mysqli_query($connection, $query);
                                $comment_count = mysqli_fetch_assoc($increment_comment_count_query);
                                $comment_count_new = $comment_count['count(*)'] + 1;

                                $query = "UPDATE posts SET post_comment_count=".$comment_count_new." WHERE posts.id=".$post_id;
                                $update_comment_count_query = mysqli_query($connection, $query);
                                if(!$update_comment_count_query){
                                    die('QUERY FAILED' . mysqli_error($connection));
                                }
                            }
                        }else{
                            echo "<p class='bg-warning' style='color:red;'>Comment Field Cannot Be Empty</p>";
                        }
                    }
                ?>

                <!-- Comments Form -->
                <div class="well">
                    <h4>Leave a Comment:</h4>
                    <form action="" method="post" role="form">
                        <label for="Comment">Comment</label>
                        <div class="form-group">
                            <textarea name="comment_content" class="form-control" id="editor"></textarea>
                        </div>
                        <button type="submit" name="create_comment" class="btn btn-primary">Submit</button>
                    </form>
                </div>

                <hr>

                <!-- Posted Comments -->

                <!-- Comment -->
                <div class="media">
                    <h2>Comments:</h2><hr>
                    <div class="media-body">
                        <?php

                            $post_id = $_GET['post_id'];
                            $query = "SELECT * FROM comments LEFT JOIN accounts on comments.comment_account_id=accounts.id LEFT JOIN posts on posts.id=comments.comment_post_id WHERE posts.id=".$post_id;
                            $query_all_post_comments = mysqli_query($connection, $query);

                            while($row = mysqli_fetch_assoc($query_all_post_comments)){
                                $full_name = $row['first_name'].' '.$row['last_name'];
                                $comment_content = $row['comment_content'];
                                $comment_date = $row['comment_date'];

                                echo "<h4 class='media-heading'>".$full_name."<small>"."  -  ".$comment_date."</small></h4>";
                                echo "<p>".$comment_content."</p>";

                            }

                        ?>
                    </div>
                </div>

            </div>

            <!-- Blog Sidebar Widgets Column -->
            <?php include "includes/sidebar.php"; ?>

        </div>
        <!-- /.row -->

        <hr>

<?php include "includes/footer.php"; ?>
