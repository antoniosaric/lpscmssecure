<div class="col-lg-12">
    <h1 class="page-header">
        <?php
            $page = basename($_SERVER['PHP_SELF']);
            switch ($page) {
                case 'accounts.php';
                    echo "Accounts";
                    break;
                case 'changes.php';
                    echo "Recent Changes";
                    break;
                case 'posts.php';
                    echo "Posts";
                    break;
                case 'categories.php';

                    if (isset($_GET['source'])) {
                        switch ($_GET['source']) {
                            case 'profiles';
                                echo "Profiles";
                                break;
                            case 'profile_update_athlete';
                                echo "Update Profile";
                                break;
                            case 'profile_add_athlete';
                                echo "Add Profile";
                                break;
                            case 'teams';
                                echo "Teams";
                                break;
                            case 'team_update';
                                echo "Team Update";
                                break;
                            case 'team_add';
                                echo "Add Team";
                                break;
                            case 'league_add';
                                echo "Add League";
                                break;
                            case 'league_update';
                                echo "Update League";
                                break;
                            case 'leagues';
                                echo "Leagues";
                                break;
                            case 'videos';
                                echo "Videos";
                                break;
                            case 'video_add';
                                echo "Add Video";
                                break;
                            case 'update_video';
                                echo "Update Video";
                                break;
                            case 'activity';
                                echo "Activities";
                                break;
                            case 'activity_add';
                                echo "Add Activity";
                                break;
                            case 'sports_update';
                                echo "Update Activity";
                                break;
                            case 'category_add';
                                echo "Add Category";
                                break;
                            case 'partitions';
                                echo "Partitions";
                                break;
                            case 'partition_add';
                                echo "Add Partition";
                                break;
                            case 'update_partition';
                                echo "Update Partition";
                                break;
                            case 'images';
                                echo "Images";
                                break;
                            case 'image_add';
                                echo "Add Image";
                                break;
                            case 'update_image';
                                echo "Update Image";
                                break;

                            default:
                                echo "Categories";
                        }
                    } else {
                        echo "Categories";
                    }
                    break;
                case 'errors.php';
                    echo "Errors";
                    break;
                case 'charts.php';
                    echo "Charts";
                    break;
                case 'requests.php';
                    echo "Requests";
                    break;    
                case 'index.php';
                    echo "Welcome   "."<small>".$_SESSION['full_name']."</small>";
                    break;

                default:
                    echo "Welcome   "."<small>".$_SESSION['full_name']."</small>";
            }
                // echo "<h1>".basename($_SERVER['PHP_SELF'])."</h1>";
        ?>
    </h1>
</div>
