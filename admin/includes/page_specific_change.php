
<?php
    if(isset($_GET['change_id'])){

        $query = "SELECT changes.id AS changesId, accounts.first_name AS first_name, accounts.last_name AS last_name, changes.category_changed AS category_changed, changes.change_type AS change_type, changes.affected_id AS affected_id, changes.change_record AS change_record FROM changes LEFT JOIN accounts ON accounts.id = changes.account_id WHERE changes.id=?";
        $prepare_changes_by_id = $connection->prepare($query);
        $prepare_changes_by_id->bind_param('i', $_GET['change_id']);
        $prepare_changes_by_id->execute();
        $result_changes_by_id = $prepare_changes_by_id->get_result();
        $row = mysqli_fetch_assoc($result_changes_by_id);
        $prepare_changes_by_id->close();

        echo '<h3>Id</h3>';
        echo '<p>'.$row['changesId'].'</p>';
        echo '<h3>Changed By</h3>';
        echo '<p>'.$row['first_name'].' '.$row['last_name'].'</p>';
        echo '<h3>Changed</h3>';
        echo '<p>'.$row['category_changed'].'</p>';
        echo '<h3>Type</h3>';
        echo '<p>'.$row['change_type'].'</p>';
        echo '<h3>Affected Id</h3>';
        echo '<p>'.$row['affected_id'].'</p>';
        echo '<h3>Change Record</h3>';
        echo '<p>'.$row['change_record'].'</p>';

   }
?> 

