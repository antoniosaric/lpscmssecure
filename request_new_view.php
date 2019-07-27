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
                <!-- Blog Comments -->
                <?php
                    if (isset($_GET['source'])) {
                        $source = $_GET['source'];
                    } else {
                        $source = "";
                    }

                    if($source == "request_view"){

                        if(isset($_GET['request_id'])){

                            $query = "SELECT * FROM requests WHERE requests.id=?";
                            $prepare_requests_by_id = $connection->prepare($query);
                            $prepare_requests_by_id->bind_param('i', $_GET['request_id']);
                            $prepare_requests_by_id->execute();
                            $result_requests_by_id = $prepare_requests_by_id->get_result();
                            $row = mysqli_fetch_assoc($result_requests_by_id);
                            $prepare_requests_by_id->close();

                            echo '<h4>View Request</h4>';
                            echo '<hr>';
                            echo '<div class="container-fluid">';
                            echo '<h3>Id</h3>';
                            echo '<p>'.$row['id'].'</p>';
                            echo '<h3>category id</h3>';
                            echo '<p>'.$row['category_id'].'</p>';
                            echo '<h3>category type</h3>';
                            echo '<p>'.$row['category_type'].'</p>';
                            echo '<h3>given reason</h3>';
                            echo '<p>'.$row['given_reason'].'</p>';
                            echo '</div>';

                        }

                    }else if($source == "request_new"){ ?>

                        <div class="container-fluid">
                            <h4>New Requests</h4>
                            <hr>
                            <form action="" method="post">
                                <div class="form-group">
                                    <div class="row">
                                        <input value="<?php echo $_SESSION['account_id'] ?>" type="hidden" class="form-control" name="account_id" >
                                        <div class="form-group col-xs-2">
                                            <label for="category_id">Category Id</label>
                                            <input value="" type="number" class="form-control" name="category_id" >
                                        </div>
                                        <div class="form-group col-xs-3">
                                            <label for="category_type">Category Type</label>
                                            <select name='category_type'>;
                                                <option value="">choose type</option>
                                                <?php
                                                    $category_query = "SELECT * FROM categories WHERE status = 'approved'";
                                                    $all_category_query = mysqli_query($connection, $category_query);
                                                    while($row = mysqli_fetch_assoc($all_category_query)){
                                                        echo "<option value=".$row['category_name'].">".$row['category_name']."</option>";
                                                    }
                                                 ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="given_reason">Given Reason</label>
                                        <textarea class="form-control" name="given_reason" id="" cols="30" rows="10" value=""></textarea>
                                    </div>
                                </div>
                                <input class='btn btn-primary' type='submit' name='add_request' value='add'>
                            </form>
                            <?php
                                if(isset($_POST['add_request'])){

                                    $category_id = $_POST['category_id'];
                                    $category_type = $_POST['category_type'];
                                    $given_reason = $_POST['given_reason'];
                                    $account_id = $_SESSION['account_id'];

                                    if( !!$category_id && !!$category_type && !!$given_reason ){

                                        $query = "INSERT INTO requests(category_id, category_type, given_reason, account_id ) VALUES( ?,?,?,? )";

                                        $stmt_request = $connection->prepare($query);
                                        $stmt_request->bind_param("issi", $category_id, $category_type, $given_reason, $account_id);
                                        $stmt_request_result = $stmt_request->execute();
                                        $stmt_request->close();

                                        $email = "rob@levelplaysports.com";
                                        $subject = 'Request to Delete Entry';
                                        $message = " account id: ".$account_id."\r\n"." has a delete request!"."\r\n"."Thank You,"."\r\n"."CMS TEAM";
                                        $headers = 'From: webmaster@example.com' . "\r\n" .
                                            'Reply-To: webmaster@example.com' . "\r\n" .
                                            'X-Mailer: PHP/' . phpversion();

                                        mail($email, $subject, $message, $headers);

                                        header("location: requests_view.php");

                                    }else{
                                        echo "<p class='bg-warning' style='color:red;'>No fields can be left empty</p>";
                                    }
                                }
                            ?>
                        </div>
                    <?php }
                ?>
            </div>

            <!-- Blog Sidebar Widgets Column -->
            <?php include "includes/sidebar.php"; ?>

        </div>
        <!-- /.row -->

        <hr>

<?php include "includes/footer.php"; ?>
