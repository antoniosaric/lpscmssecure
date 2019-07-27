                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-xs-6">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="Enter Password">Enter Password</label>
                                    <input type="number" class="form-control" name="account_id" placeholder="account id">
                                    <br>
                                    <input type="password" class="form-control" name="new_password" placeholder="new password">
                                </div>
                                <div class="form-group">
                                    <input class="btn btn-primary" type="submit" name="submit" value="Generate Password">
                                </div>
                            </form>
                            <?php  
                                if(isset($_POST['new_password'])){
                                    include '../includes/passwordHash.php';
                                    $stmt = "UPDATE accounts SET password=? WHERE id=?";
                                    $new_password = generate_hash($_POST['new_password']);
                                    $account_id = $_POST['account_id'];
                                    $prepared = $connection->prepare($stmt);
                                    $prepared->bind_param('si', $new_password, $account_id);
                                    $result = $prepared->execute();
                                    $prepared->close();
                                    if(!$result){
                                        die('QUERY FAILED' . mysqli_error($connection));
                                    }else{                                            
                                        header("location: accounts.php");
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>