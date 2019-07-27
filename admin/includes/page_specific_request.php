<?php
    if(isset($_GET['request_id'])){

        $query = "SELECT *, requests.id AS request_id, accounts.first_name AS first_name, accounts.last_name AS last_name FROM requests LEFT JOIN accounts on accounts.id=requests.account_id WHERE requests.id=?";
        $prepare_requests_by_id = $connection->prepare($query);
        $prepare_requests_by_id->bind_param('i', $_GET['request_id']);
        $prepare_requests_by_id->execute();
        $result_requests_by_id = $prepare_requests_by_id->get_result();
        $row = mysqli_fetch_assoc($result_requests_by_id);
        $prepare_requests_by_id->close();

        echo '<h3>Id</h3>';
        echo '<p>'.$row['request_id'].'</p>';
        echo '<h3>Status</h3>';        
        echo '<p>'.$row['status'].'</p>';
        echo '<h3>requested By</h3>';
        echo '<p>'.$row['first_name'].' '.$row['last_name'].'</p>';
        echo '<h3>Category Type</h3>';
        echo '<p>'.$row['category_type'].'</p>';
        echo '<h3>Reason/Explanation</h3>';
        echo '<p>'.$row['given_reason'].'</p>';
        echo '<h3>Affected Id</h3>';
        echo '<p>'.$row['category_id'].'</p>';


   }
?> 