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
                <div class="container-fluid">
                    <h4>Requests</h4>
                    <hr>
                    <a class='btn btn-primary main-links' href='request_new_view.php?source=request_new'>Add Request</a>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Category Id</th>
                                        <th>Category Type</th>
                                        <th>Requestor Name</th>
                                        <th>Request Reason</th>
                                        <th>Status</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                        $query = "SELECT *, requests.id AS request_id, accounts.first_name AS first_name, accounts.last_name AS last_name FROM requests LEFT JOIN accounts on accounts.id=requests.account_id WHERE accounts.id=".$_SESSION['account_id']." ORDER BY status DESC";
                                        $select_requests_sidebar = mysqli_query($connection, $query);

                                        while($row = mysqli_fetch_assoc($select_requests_sidebar)){
                                            echo "<tr>";
                                            echo "<td>".$row['request_id']."</td>";
                                            if($row['category_type'] == 'profile'){
                                                echo "<td><a href='categories.php?source=profile_update_athlete&profile_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
                                            }else if($row['category_type'] == 'activity'){
                                                echo "<td><a href='categories.php?source=sports_update&activity_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
                                            }else if($row['category_type'] == 'teams'){
                                                echo "<td><a href='categories.php?source=team_update&team_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
                                            }else if($row['category_type'] == 'videos'){
                                                echo "<td><a href='categories.php?source=update_video&video_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
                                            }else if($row['category_type'] == 'leagues'){
                                                echo "<td><a href='categories.php?source=league_update&entity_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
                                            }else if($row['category_type'] == 'partitions'){
                                                echo "<td><a href='categories.php?source=update_partition&partition_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
                                            } else if ($row['category_type'] == 'images') {
                                                echo "<td><a href='categories.php?source=update_image&image_id=".$row['category_id']."'>".$row['category_id']."</a></td>";
                                            } else {
                                                echo "<td>".$row['category_id']."</td>";
                                            }
                                            echo "<td>".$row['category_type']."</td>";
                                            echo "<td>".$row['first_name']." ".$row['last_name']."</td>";
                                            echo "<td><div class='noverflow'>".substr($row['given_reason'], 0, 100).'...'."</div></td>";
                                            echo "<td>".$row['status']."</td>";
                                            echo "<td><a class='btn btn-info' href='request_new_view.php?source=request_view&category_type=".$row['category_type']."&category_id=".$row['category_id']."&request_id=".$row['request_id']."'>View</a></td>";
                                            echo "</tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Blog Sidebar Widgets Column -->
            <?php include "includes/sidebar.php"; ?>

        </div>
        <!-- /.row -->

        <hr>

<?php include "includes/footer.php"; ?>
