<?php include "includes/admin_header.php" ?>

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
                </div>
                <nav class="navbar" role="navigation">
                    <ul class="nav top-nav">
                        <li>
                            <a class="btn btn-primary main-links"href="charts.php?source=charts">View Charts</a>
                        </li>
                        <li>
                            <a class="btn btn-primary main-links"href="charts.php?source=changes">View Changes</a>
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
                        case 'changes';
                            include "includes/view_all_changes.php";
                            break;
                        case 'charts';
                            include "includes/view_all_charts.php";
                            break;
                        case 'analysis';
                            include "includes/view_all_analysis.php";
                            break;

                        default:
                            include "includes/view_all_charts.php";
                    }
                ?>

                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->
<?php include "includes/admin_footer.php" ?>
