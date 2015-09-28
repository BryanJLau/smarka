<!DOCTYPE html>
<html>
<title>Hom's Kitchen - Administrator Login</title>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<body>
<div class="container-fluid">
    <div class="row">
	    <div class="col-md-4">
	    </div>
	    <div class="col-md-4 text-center">
		    <div class="panel panel-default">
			    <div class="panel-heading">
				    <h3 class="panel-title">
					    Administrator Login
				    </h3>
			    </div>
			    <div class="panel-body">
				    <form role="form" method="POST" action="/admin/login">
			            <div class="form-group">
				            <label for="email">
					            Email address
				            </label>
				            <input type="email" name="email"
				                class="form-control" id="email" />
			            </div>
			            <div class="form-group">
				            <label for="password">
					            Password
				            </label>
				            <input type="password" name="password"
				                class="form-control" id="password" />
			            </div>
			            
			            <button type="submit" class="btn btn-default">
				            Submit
			            </button>
		            </form>
			    </div>
		    </div>
	    </div>
	    <div class="col-md-4">
	    </div>
    </div>
</div>
</body>
</html>
