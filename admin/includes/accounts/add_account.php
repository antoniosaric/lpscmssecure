<?php include '../includes/passwordHash.php' ?>
<div class="row">
    <div class="col-lg-12">
        <div class="col-xs-6">

            <?php insertAccount(); ?>

            <form action="" method="post">
                <div class="form-group">
                    <label for="First Name">First Name</label>
                    <div class="form-group">
                        <input placeholder="first name" type="text" class="form-control" name="first_name">
                    </div>
                    <br>
                    <label for="Last Name">Last Name</label>
                    <div class="form-group">
                        <input placeholder="last name" type="text" class="form-control" name="last_name">
                    </div>
                    <br>
                    <label for="Password">Password</label>
                    <div class="form-group">
                        <input placeholder="password" type="text" class="form-control" name="password">
                    </div>
                    <br>
                    <div class="form-group">
                    	<label for="Email">Email</label>
                        <input placeholder="email" type="text" class="form-control" name="email">
                    </div>
                    <br>
                    <label for="Access Level">Access</label>
                    <div class="form-group">
                        <select name="access_id">
								<option value="2">restricted</option>
								<option value="1">admin</option>
						</select>
					</div>

                </div>
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" name="submit_account" value="Add Account">
                </div>
            </form>
        </div>
    </div>
</div>