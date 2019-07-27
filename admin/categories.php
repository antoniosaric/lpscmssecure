<?php include "includes/admin_header.php" ?>
<?php include "includes/categories/categories_functions.php"; ?>
<?php include "includes/activities/activities_functions.php"; ?>
<?php include "includes/teams/teams_functions.php"; ?>
<?php include "includes/leagues/leagues_functions.php"; ?>
<?php include "includes/partitions/partitions_functions.php"; ?>
<?php include "includes/videos/videos_functions.php"; ?>
<?php include "includes/profiles/profiles_functions.php"; ?>
<?php include "includes/images/images_functions.php"; ?>

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
                    <?php
                        if (isset($_GET['source'])) {
                            $source = $_GET['source'];
                        } else {
                            $source = "";
                        }

                        echo "<nav class='navbar' role='navigation'>";
                            echo "<ul class='nav top-nav'>";
                                switch ($source) {
                                    case 'profiles';
                                    case 'profile_add_athlete';
                                    case 'profile_update_athlete';
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=profile_add_athlete'>Add Profile</a></li>";
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=profiles'>View Profiles</a></li>";
                                        break;
                                    case 'leagues';
                                    case 'league_add';
                                    case 'league_update';
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=league_add'>Add League</a></li>";
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=leagues'>View Leagues</a></li>";
                                        break;

                                    case 'teams';
                                    case 'team_add';
                                    case 'team_update';
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=team_add'>Add Team</a></li>";
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=teams'>View Teams</a></li>";
                                        break;
                                    case 'activity';
                                    case 'activity_add';
                                    case 'sports_update';
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=activity_add'>Add Activity</a></li>";
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=activity'>View Activities</a></li>";
                                        break;

                                    case 'category';
                                    case 'category_add';
                                    case 'update_categories';
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=category_add'>Add Category</a></li>";
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php'>View Category</a></li>";
                                        break;

                                    case 'videos';
                                    case 'video_add';
                                    case 'update_video';
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=video_add'>Add Videos</a></li>";
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=videos'>View Videos</a></li>";
                                        break;

                                    case 'partitions';
                                    case 'partition_add';
                                    case 'update_partition';
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=partition_add'>Add Partitions</a></li>";
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=partitions'>View Partitions</a></li>";
                                        break;

                                    case 'images';
                                    case 'image_add';
                                    case 'update_image';
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=image_add'>Add Image</a></li>";
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=images'>View Images</a></li>";
                                        break;

                                    default:
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php?source=category_add'>Add Category</a></li>";
                                        echo "<li><a class='btn btn-primary main-links' href='categories.php'>View Category</a></li>";
                                }
                            echo "</ul>";
                        echo "</nav>";

                        switch ($source) {
                            case 'profiles';
                                include "includes/profiles/view_all_profiles.php";
                                break;
                            case 'profile_add_athlete';
                                include "includes/profiles/add_profile.php";
                                break;
                            case 'profile_update_athlete';
                                include "includes/profiles/update_profile.php";
                                break;
                            case 'leagues';
                                include "includes/leagues/view_all_leagues.php";
                                break;
                            case 'league_add';
                                include "includes/leagues/add_league.php";
                                break;
                            case 'league_update';
                                include "includes/leagues/update_league.php";
                                break;
                            case 'teams';
                                include "includes/teams/view_all_teams.php";
                                break;
                            case 'team_add';
                                include "includes/teams/add_team.php";
                                break;
                            case 'team_update';
                                include "includes/teams/update_team.php";
                                break;
                            case 'view_team_athletes';
                                include "includes/teams/view_team_athletes.php";
                                break;
                            case 'view_team_activity';
                                include "includes/teams/view_team_activity.php";
                                break;
                            case 'view_team_entity';
                                include "includes/teams/view_team_entity.php";
                                break;
                            case 'view_team_vieos';
                                include "includes/teams/view_team_vieos.php";
                                break;
                            case 'activity';
                                include "includes/activities/view_all_activity.php";
                                break;
                            case 'activity_add';
                                include "includes/activities/add_activity.php";
                                break;
                            case 'sports_update';
                                include "includes/activities/update_activity.php";
                                break;
                            case 'category';
                                include "includes/categories/view_all_categories.php";
                                break;
                            case 'category_add';
                                include "includes/categories/add_category.php";
                                break;
                            case 'update_categories';
                                include "includes/categories/update_categories.php";
                                break;
                            case 'videos';
                                include "includes/videos/view_all_videos.php";
                                break;
                            case 'video_add';
                                include "includes/videos/add_video.php";
                                break;
                            case 'update_video';
                                include "includes/videos/update_video.php";
                                break;
                            case 'partitions';
                                include "includes/partitions/view_all_partitions.php";
                                break;
                            case 'partition_add';
                                include "includes/partitions/add_partition.php";
                                break;
                            case 'update_partition';
                                include "includes/partitions/update_partition.php";
                                break;
                            case 'images';
                                include "includes/images/view_all_images.php";
                                break;
                            case 'image_add';
                                include "includes/images/add_image.php";
                                break;
                            case 'update_image';
                                include "includes/images/update_image.php";
                                break;
                            default:
                                include "includes/categories/view_all_categories.php";
                        }
                    ?>
                    <?php deleteCategory(); ?>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->
<?php include "includes/admin_footer.php" ?>
