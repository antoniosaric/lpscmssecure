<?php  


//======================================================================
// INSERT ACCOUNT
//======================================================================

	function insertAccount(){

		global $connection;

	    if(isset($_POST['submit_account'])){
	        $first_name = $_POST['first_name'];
	        $last_name = $_POST['last_name'];
	        $password = generate_hash($_POST['password']);
	        $email = $_POST['email'];
	        $access_id = $_POST['access_id'];

	        if($first_name == "" || $last_name == "" || $access_id == "" || empty($first_name) || empty($last_name) || empty($access_id)){
	            echo "This field should not be empty";
	        }else{
	            $stmt = "INSERT INTO accounts(first_name, last_name, password, email, access_id) VALUE( ?, ?, ?, ?, ? )";
                $prepared = $connection->prepare($stmt);
                $prepared->bind_param('ssssi', $first_name, $last_name, $password, $email, $access_id);
                $result = $prepared->execute();
                $prepared->close();
                queryCheck($result);                                          
                header("location: accounts.php");
	        }
	    }
	}

//======================================================================
// FIND ALL ACCOUNTS
//======================================================================

	function findAllAccounts(){

		global $connection;

        //find all catergories query
        $query = "SELECT *, accounts.id AS account_id FROM accounts LEFT JOIN access on accounts.access_id=access.id";
        $select_categories_sidebar = mysqli_query($connection, $query); 

        while($row = mysqli_fetch_assoc($select_categories_sidebar)){
            echo "<tr>";
            echo "<td>".$row['account_id']."</td>";
            echo "<td>".$row['first_name']."</td>";
            echo "<td>".$row['last_name']."</td>";
            echo "<td>".$row['email']."</td>";
            echo "<td>".$row['access']."</td>";
            echo "<td><a class='btn btn-info' href='accounts.php?source=edit_account&edit=".$row['account_id']."'>Edit</a></td>";
            echo "<form method='post'><input type='hidden' name='account_id' value=".$row['account_id'].">";
            echo "<td><input class='btn btn-danger' type='submit' name='delete' value='delete'></td>";
			/*
	            <a onClick=\"javascript: return confirm('Are you sure you want to delete?');\" href='accounts.php?delete=".$row['account_id']."'>DELETE</a></td>";
	        */    
            echo "</form>";
            echo "</tr>";
        }                       
	}

//======================================================================
// DELETE ACCOUNT
//======================================================================

	function deleteAccount(){

		global $connection;

        if(isset($_POST['delete'])){
        	$account_id = $_POST['account_id'];
            $stmt = "DELETE FROM accounts WHERE id=?";
            $prepared = $connection->prepare($stmt);
            $prepared->bind_param('i', $account_id);
            $result = $prepared->execute();
            $prepared->close();
			queryCheck($result);                                           
            header("location: accounts.php");
        }

	}



?>