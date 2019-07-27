
                <div class="container-fluid">
                    <h4>Added Categories</h4>
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Category Name</th>
                                        <th>Status</th>
                                        <th>Change To</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php changeCategoryStatus(); ?>
                                    <?php findAllCategories(); ?>
                                    <?php deleteCategory(); ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>


