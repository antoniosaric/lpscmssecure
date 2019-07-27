    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">LPS-CMS</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-left">
                    <li class=""><a href='requests_view.php'>requests</a></li>";
                    <?php 
                        $query = "SELECT * FROM categories";
                        $select_all_categories_query = mysqli_query($connection, $query);
                        while($row = mysqli_fetch_assoc($select_all_categories_query)){

                        }
                    ?>   
                        <?php 
                            $query = "SELECT * FROM categories";
                            $select_categories_sidebar = mysqli_query($connection, $query); 
                            while($row = mysqli_fetch_assoc($select_categories_sidebar)){
                                $category_name = $row['category_name'];
                                $category_id = $row['id'];

                                if($row['status'] == 'approved'){
                                $pageName = basename($_SERVER['PHP_SELF']);
                                $class_active = $pageName == $category_name.'.php' ? "active" : '';

                                    echo "<li class=".$class_active."><a href='".$category_name.".php'>".$category_name."</a></li>";
                                }
                                

                            }                       
                        ?>
                </ul>                
                <ul class="nav navbar-nav navbar-right">
            
                    <?php  
                        if($_SESSION['access'] == 'admin'){
                            echo "<li><a href='admin'>Admin</a></li>";
                        }
                    ?>
                    
<!--                     <li>
                        <a href="#">Services</a>
                    </li>    -->             
                    <li>
                        <a href="includes/logout.php">logout</a>
                    </li>



                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

