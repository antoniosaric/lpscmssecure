<div class="container-fluid">
    <h4>Requests</h4>
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
                        <th>View/Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php findAllRequests(); ?>
                </tbody>
            </table>

        </div>
    </div>
</div>