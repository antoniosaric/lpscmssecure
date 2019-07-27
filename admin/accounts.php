<?php include "includes/admin_header.php" ?>
<?php include "includes/accounts/accounts_functions.php"; ?>

    <div id="wrapper">

        <!-- Navigation -->
<?php include "includes/admin_navigation.php" ?>
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <?php include "../includes/popup_saved_info.php"; ?>
                </div>
                <!-- Page Heading -->
                <div class="row">
                    <?php include "includes/page_header.php"; ?>
                </div>
                <nav class="navbar" role="navigation">
                    <ul class="nav top-nav">
                        <li>
                            <a class='btn btn-primary main-links' href="accounts.php?source=add_account">Add account</a>
                        </li>
                        <li>
                            <a class='btn btn-primary main-links' href="accounts.php">View Accounts</a>
                        </li>
                        <li>
                            <a class='btn btn-primary main-links' href="accounts.php?source=generate">Generate Password</a>
                        </li>
                    </ul>
                </nav>
                <?php

                    if(isset($_GET['source'])){
                        $source = $_GET['source'];
                    }else{
                        $source = "";
                    }

                    switch($source){
                        case 'add_account';
                            include "includes/accounts/add_account.php";
                            break;
                        case 'edit_account';
                            include "includes/accounts/update_accounts.php";
                            break;
                        case 'generate';
                            include "includes/generate_password.php";
                            break;

                        default:
                            include "includes/accounts/view_all_accounts.php";
                    }
                ?>

                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->
<?php include "includes/admin_footer.php" ?>
