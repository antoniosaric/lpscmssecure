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
                    <nav class="navbar" role="navigation">
                        <ul class="nav top-nav">
                            <li>
                                <a class='btn btn-primary main-links' href="changes.php">View Changes</a>
                            </li>
                        </ul>
                    </nav>
                        <div class="col-xs-12">
                            <?php

                                if(isset($_GET['source'])){
                                    $source = $_GET['source'];
                                }else{
                                    $source = "";
                                }

                                switch($source){
                                    case 'view_change';
                                        include "includes/page_specific_change.php";
                                        break;
                                    default:
                                        include "includes/view_all_changes.php";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </duv>
<?php include "includes/admin_footer.php" ?>
