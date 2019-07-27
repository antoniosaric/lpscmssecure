<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>LPS CMS SPLASH </title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style type="text/css">
	    body {
	    	padding-top: 70px; /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
	    	background-image: url("images/stadium1.jpg");
			background-repeat: no-repeat;
  background-attachment: fixed;
  background-position: center; 

		}	
		.content {
	        position:absolute; /*it can be fixed too*/
	        left:0; right:0;
	        top:0; bottom:0;
	        margin:auto;

	        /*this to solve "the content will not be cut when the window is smaller than the content": */
	        max-width:100%;
	        max-height:100%;
	        overflow:auto;
	        height: 25%
	    }

    </style>



</head>
	<body>
		<div class="container">
			<div class="col-md-4 content">
	        	<div class="row text-center">
					<!-- Login Well -->
		    		<div class="well">
		        		<h4>Login</h4>
		        		<form action="includes/login.php" method="post">
		            		<div class="form-group">
		                		<input autocomplete="off" name="email" type="email" class="form-control" placeholder="enter email">
		            		</div>
		            		<div class="input-group">
		                		<input autocomplete="off" name="password" type="password" class="form-control" placeholder="enter password">
		                		<span class="input-group-btn">
		                    		<button class="btn btn-primary" type="submit" name="login" >login</button>
		                		</span>
		            		</div>
		        		</form><!-- search form    --> 
		            	<!-- /.input-group -->
		    		</div>
				</div>
			</div>
		</div>
	</body>
</html>