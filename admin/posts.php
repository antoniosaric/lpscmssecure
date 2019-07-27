<?php include "includes/admin_header.php" ?>
<?php include "includes/posts/posts_functions.php"; ?>


    <div id="wrapper">

        <!-- Navigation -->
<?php include "includes/admin_navigation.php" ?>
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <?php include "../includes/popup_saved_info.php"; ?>
                </div>
                <!-- Page Heading -->
                <div class="row">

                    <?php include "includes/page_header.php"; ?>

                    <nav class="navbar" role="navigation">
                        <ul class="nav top-nav">
                            <li>
                                <a class="btn btn-primary main-links" href="posts.php?source=add_post">Add Post</a>
                            </li>
                            <li>
                                <a class="btn btn-primary main-links" href="posts.php">View Posts</a>
                            </li>
                            <li>
                                <a class="btn btn-primary main-links" href="posts.php?source=comments">View Comments</a>
                            </li>
                        </ul>
                    </nav>

                    <?php

                        if(isset($_GET['source'])){
                            $source = $_GET['source'];
                        }else{
                            $source = "";
                        }

                        switch($source){
                            case 'add_post';
                                include "includes/posts/add_post.php";
                                break;
                            case 'edit_post';
                                include "includes/posts/update_post.php";
                                break;
                            case 'comments';
                                include "includes/posts/view_all_comments.php";
                                break;
                            case '36';
                                echo "very nice";
                                break;

                            default:
                                include "includes/posts/view_all_posts.php";
                        }


                    ?>
                    <?php deletePost(); ?>
                    <?php deleteComment(); ?>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->
<?php include "includes/admin_footer.php" ?>
