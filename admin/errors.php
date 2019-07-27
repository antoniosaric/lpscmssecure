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
                                <a class="btn btn-primary main-links" href="errors.php">View Errors</a>
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

                                    case 'video_errors';
                                        include "includes/errors/video_errors.php";
                                        break;

                                    case 'profile_errors';
                                        include "includes/errors/profile_errors.php";
                                        break;

                                    case 'team_errors';
                                        include "includes/errors/team_errors.php";
                                        break;

                                    case 'league_errors';
                                        include "includes/errors/league_errors.php";
                                        break;

                                    case 'image_errors';
                                        include "includes/errors/page_specific_change.php";
                                        break;

                                    case 'sports_errors';
                                        include "includes/errors/page_specific_change.php";
                                        break;

                                    default:
                                        include "errors.php";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </duv>
<?php include "includes/admin_footer.php" ?>
