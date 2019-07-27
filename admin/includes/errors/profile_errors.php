<?php include "includes/errors/error_functions.php" ?>
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th>id</th>
            <th>category</th>
            <th>error</th>
            <th>edit</th>
        </tr>
    </thead>
    <tbody>
        <?php findAllDatabaseErrorsProfileImage(); ?>                  
    </tbody>
</table>
<br>
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th>id</th>
            <th>category</th>
            <th>error</th>
            <th>edit</th>
        </tr>
    </thead>
    <tbody>
        <?php findAllDatabaseErrorsProfiles(); ?>                  
    </tbody>
</table>