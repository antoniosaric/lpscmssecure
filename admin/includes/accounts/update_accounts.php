<?php include '../includes/passwordHash.php' ?>
<form action="" method="post">
    <div class="form-group">
        <?php
            if(isset($_GET['edit'])){
                $account_id = $_GET['edit'];
                $stmt = "SELECT first_name, last_name, email, access_id FROM accounts WHERE id=?";
                $prepared = $connection->prepare($stmt);
                $prepared->bind_param('i', $account_id);
                $result = $prepared->execute();
                if(!$result){
                    die('QUERY FAILED' . mysqli_error($connection));
                }else{                                            
                    $getresult = $prepared->get_result();
                    $row = $getresult->fetch_assoc();
                }
                $prepared->close();
                // $new_password = generate_hash($_POST['password']);
            ?>
                    <div class="form-group">
                        <label for="First Name">First Name</label>
                        <div class="form-group">
                            <input value="<?php echo $row['first_name']; ?>" type="text" class="form-control" name="first_name" >
                        </div>
                        <br>
                        <label for="Last Name">Last Name</label>
                        <div class="form-group">
                            <input value="<?php echo $row['last_name']; ?>" type="text" class="form-control" name="last_name" >
                        </div>
                        <br>
                        <label for="Email">Email</label>
                        <div class="form-group">
                            <input value="<?php echo $row['email']; ?>" type="text" class="form-control" name="email" >
                        </div>
                        <br>
                        <label for="Access">Access</label>
                        <div class="form-group">
                            <select name="access_id">
									<option <?php if($row['access_id'] == "2") echo "SELECTED";?> value="2">restricted</option>
									<option <?php if($row['access_id'] == "1") echo "SELECTED";?> value="1">admin</option>
							</select>
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="btn btn-primary" type="submit" name="update_account" value="Update Account">
                    </div>
        <?php  }  ?>

        <?php  
            if(isset($_POST['update_account'])){
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $email = $_POST['email'];
                $access_id = $_POST['access_id'];

                // include '../includes/passwordHash.php';
                $stmt = "UPDATE accounts SET first_name=?, last_name=?, email=?, access_id=? WHERE id=?";
                $prepared = $connection->prepare($stmt);
                $prepared->bind_param('sssii', $first_name, $last_name, $email, $access_id, $account_id);
                // $new_password = generate_hash($_POST['password']);
                if(!$prepared->execute()){
                    die('QUERY FAILED' . mysqli_error($connection));
                }else{
                    header("location: accounts.php");
                }
            }
        ?>        
    </div>
</form>