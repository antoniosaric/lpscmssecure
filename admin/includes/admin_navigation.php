
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">LPS CMS Admin</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li><a href="../index.php">Home Page</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $_SESSION['full_name'] ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#"><i class="fa fa-fw fa-user"></i> Profile</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fw fa-gear"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="../includes/logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li>
                        <a href="./index.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="charts.php"><i class="fa fa-fw fa-bar-chart-o"></i> Charts</a>
                    </li>
                    <li>
                        <a href="accounts.php"><i class="fa fa-fw fa-user"></i> Account Control</a>
                    </li>
                    <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-fw fa-arrows-v"></i> DB Control <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="demo" class="collapse">
                            <li><a href='categories.php?source=category'>category control</a></li>
		                	<?php
		                		$query = "SELECT * FROM categories";
                                $select_all_categories = mysqli_query($connection, $query);
		                		while($row = mysqli_fetch_assoc($select_all_categories)){
                                    $category_id = $row['id'];
                                    $category_name = $row['category_name'];
		                			echo "<li><a href='categories.php?source=".$category_name."'>".$category_name." control</a></li>";
		                		}
		                	?>
                        </ul>
                    </li>
                    <li>
                        <a href="changes.php"><i class="fa fa-fw fa-desktop"></i> Recent Changes</a>
                    </li>
                    <li>
                        <a href="requests.php"><i class="fa fa-fw fa-tasks"></i> Requests</a>
                    </li>
                    <li>
                        <a href="./posts.php"><i class="fa fa-fw fa-edit"></i> Message Control</a>
                    </li>
                    <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#errors"><i class="fa fa-fw fa-exclamation-triangle"></i> Database Errors <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="errors" class="collapse">
                            <li><a href="./errors.php?source=video_errors"><i class="fa fa-fw fa-exclamation-triangle"></i> Videos</a></li>
                            <li><a href="./errors.php?source=team_errors"><i class="fa fa-fw fa-exclamation-triangle"></i> Teams</a></li>
                            <li><a href="./errors.php?source=profile_errors"><i class="fa fa-fw fa-exclamation-triangle"></i> Profiles</a></li>
                            <li><a href="./errors.php?source=league_errors"><i class="fa fa-fw fa-exclamation-triangle"></i> Leagues</a></li>
                            <li><a href="./errors.php?source=./errors.php"><i class="fa fa-fw fa-exclamation-triangle"></i> Sports</a></li>
                            <li><a href="./errors.php?source=./errors.php"><i class="fa fa-fw fa-exclamation-triangle"></i> Other</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>
