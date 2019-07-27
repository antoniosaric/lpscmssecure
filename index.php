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

                    $post_query_count = "SELECT * FROM posts";
                    $post_find_count = mysqli_query($connection, $post_query_count);
                    $count = mysqli_num_rows($post_find_count);
                    $count = ceil($count/5);


                    if(isset($_GET['pageinate'])){
                        $pageinate = $_GET['pageinate'];
                    }else{
                        $pageinate = "";
                    }

                    if($pageinate == "" || $pageinate == 1 ){
                        $pageinate_1 = 0;
                    }else{
                        $pageinate_1 = ($pageinate * 5) - 5;
                    }

                    $query = "SELECT *, posts.id AS post_id, accounts.id AS account_id FROM posts LEFT JOIN accounts ON accounts.id=posts.post_account_id ORDER BY posts.id DESC LIMIT ".$pageinate_1.", 5";
                    $select_all_posts_query = mysqli_query($connection, $query);

                    // start loop
                    while($row = mysqli_fetch_assoc($select_all_posts_query)){
                        $post_id = $row['post_id'];
                        $account_id = $row['account_id'];
                        $post_title = $row['post_title'];
                        $full_name =  $row['first_name'].' '.$row['last_name'];
                        $post_date = $row['post_date'];
                        $post_content = substr($row['post_content'], 0, 100).'...';
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
                            by <a href="accounts.php?account_id=<?php echo $account_id ; ?>"><?php echo $full_name ; ?></a>
                        </p>
                        <p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo $post_date;?></p>
                        <hr>
                        <p><?php echo $post_content;?></p>
                        <a class="btn btn-primary" href="post.php?post_id=<?php echo $post_id; ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>

                        <hr>

                    <!-- end loop -->
                    <?php } ?>
            </div>

            <!-- Blog Sidebar Widgets Column -->
            <?php include "includes/sidebar.php"; ?>

        </div>
        <!-- /.row -->

        <hr>

        <ul class="pager">
            <?php
                for($i = 1; $i <= $count; $i++){
                    if($i == $pageinate || $pageinate == 0){
                        echo "<li><a class='active_link' href='index.php?pageinate=".$i."'>".$i."</ul></a></ul></li>";
                    }else{
                        echo "<li><a href='index.php?pageinate=".$i."'>".$i."</ul></a></ul></li>";
                    }
                }
            ?>
        </ul>

<?php include "includes/footer.php"; ?>
