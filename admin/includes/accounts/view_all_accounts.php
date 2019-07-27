<div class="container-fluid">
    <h4>Added Accounts</h4>
    <div class="row">
        <div class="col-xs-12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>First Name</th>
                        <th>last Name</th>
                        <th>Email</th>
                        <th>Access</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php findAllAccounts(); ?>
                    <?php deleteAccount(); ?>
                </tbody>
            </table>

        </div>
    </div>
</div>