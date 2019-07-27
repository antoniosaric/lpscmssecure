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
                        echo "<li><a class='active_link' href='index.php?pageinate=".$i."'>".$i."</ul>a></ul>li>";
                    }else{
                        echo "<li><a href='index.php?pageinate=".$i."'>".$i."</ul>a></ul>li>";
                    }
                }
            ?>
        </ul>

<?php include "includes/footer.php"; ?>
