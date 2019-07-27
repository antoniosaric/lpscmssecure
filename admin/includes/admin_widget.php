      
                <!-- /.row -->
                
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-file-text fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class='huge'><?php echo widgetCount('posts') ?></div>
                        <div>Posts</div>
                    </div>
                </div>
            </div>
            <a href="./posts.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-pencil-square-o fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                    	<div class='huge'><?php echo widgetCount('changes') ?></div>
                      <div>Changes</div>
                    </div>
                </div>
            </div>
            <a href="./changes.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                    	<div class='huge'><?php echo widgetCount('accounts') ?></div>
                        <div> Accounts</div>
                    </div>
                </div>
            </div>
            <a href="./accounts.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
		                <div class='huge'><?php echo widgetCount('categories') ?></div>
                        <div>Categories</div>
                    </div>
                </div>
            </div>
            <a href="./categories.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
<hr>                
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-file-text fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class='huge'><?php echo tableCount('video') ?></div>
                        <div>video</div>
                    </div>
                </div>
            </div>
            <a href="categories.php?source=videos">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-pencil-square-o fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class='huge'><?php echo tableCount('profile') ?></div>
                      <div>Profiles</div>
                    </div>
                </div>
            </div>
            <a href="categories.php?source=profiles">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class='huge'><?php echo tableCount('team') ?></div>
                        <div> Teams</div>
                    </div>
                </div>
            </div>
            <a href="categories.php?source=teams">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class='huge'><?php echo tableCount('entity') ?></div>
                        <div>Leagues</div>
                    </div>
                </div>
            </div>
            <a href="categories.php?source=leagues">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
<hr>
<script type="text/javascript">
    google.charts.load('current', {'packages':['table']});
    google.charts.setOnLoadCallback(drawTable);

    function drawTable() {
        var data = new google.visualization.DataTable();
        console.log('one');
        data.addColumn('string', 'status');
        data.addColumn('string', 'count');
        data.addRows([

            <?php
                $query = "SELECT * FROM ( SELECT count(*) AS online FROM accounts WHERE session='online' ) t, ( SELECT count(*) AS offline FROM accounts WHERE session='offline' ) t2";

                $online_query = mysqli_query($connection, $query);
                $row = mysqli_fetch_assoc($online_query);
                $online_count = $row['online'];
                $offline_count = $row['offline'];

                echo "['online', '".$online_count."'],";
                echo "['offline', '".$offline_count."']";
            ?>
        ]);

        var table = new google.visualization.Table(document.getElementById('table_div'));

        table.draw(data, {showRowNumber: true, width: '150px', height: '150px'});
    }

    setInterval(function(){
        drawTable();
    }, 60000);
</script>

<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
    console.log('two');

        var data = google.visualization.arrayToDataTable([
        ['Account', 'Changes'],
        <?php
            $query_graph = "SELECT count(changes.id) AS totalCount, accounts.first_name AS first_name, accounts.last_name AS last_name FROM changes LEFT JOIN accounts on accounts.id=changes.account_id GROUP BY accounts.id";
            $select_all_changes_graph = mysqli_query($connection, $query_graph);

            while($row = mysqli_fetch_assoc($select_all_changes_graph)){
                $full_name = $row['first_name']." ".$row['last_name'];
                echo "['".$full_name."',".$row['totalCount']."],";
            }
        ?>
        ]);

        var options = {
        title: 'Database Edits Overview'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }
    setInterval(function(){
        drawChart();
    }, 60000);
</script>
<div id="table_div"></div>
<div id="piechart" style="width: auto; height: 500px;"></div>



