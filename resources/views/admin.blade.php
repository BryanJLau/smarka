<!DOCTYPE html>
<html>
<title>Hom's Kitchen - Administrator Panel</title>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.0/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.0/js/bootstrap-toggle.min.js"></script>
<script src="http://malsup.github.com/jquery.form.js"></script>
<script src="/js/admin.js"></script>
<body ng-app="adminApp" ng-controller="AdminCtrl" ng-init="init()">
<!--
<title>Admin Panel</title>
<a href="/items/list">Items</a><br>
<a href="/notifications/create">Create a notification</a><br>
<a href="/locations/list">Locations</a><br>
<a href="/orders">Unpaid Orders</a><br>
<a href="/orders?all=true">All Orders (BE CAREFUL THIS MAY TAKE A WHILE)</a><br>
-->
<?php
    session_start();
    
    if(isset($_POST['password']) && isset($_POST['email'])) {
        if($_POST['password'] == getenv('MAIL_PASSWORD') &&
                $_POST['email'] == getenv('MAIL_USERNAME')) {
            $_SESSION['password'] = $_POST['password'];
            $_SESSION['email'] = $_POST['email'];
        }
    }
    
    if(!isset($_SESSION['password'])) {
    // Display login form
?>
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
				    <form role="form" method="POST" action="/admin">
				        <!-- Token -->
                        <input type="hidden" name="_token"
                            value="{{{ csrf_token() }}}">
                        
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
<?php
} else {
// Logged in as administrator
?>

<!-- Modals -->
<!-- Add location modal -->


<!-- Begin actual body -->
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<nav class="navbar navbar-default" role="navigation">
				<div class="navbar-header">
					 <a class="navbar-brand">Hom's Kitchen</a>
				</div>
			</nav>
		</div>
	</div>
	<div class="row">
		<div class="col-md-2">
		</div>
		<div class="col-md-2">
			<ul class="nav nav-pills nav-stacked">
			    <li class="disabled">
					<a href="#">Navigation</a>
				</li>
				<li class="enabled active">
					<a href="#" role="button" ng-click="showItems()">
					    Item List
				    </a>
				</li>
				<li class="enabled">
					<a href="#" role="button" ng-click="showPendingOrders()">
					    Orders List
				    </a>
				</li>
				<li class="enabled">
					<a href="#" role="button" ng-click="showLocations()">
					    Locations List
				    </a>
				</li>
				<li class="enabled">
					<a href="#" role="button" ng-click="showNotifications()">
					    Notifications
				    </a>
				</li>
			</ul>
		</div>
		<div class="col-md-6">
		    <ng-include id="content-items" class="content"
			    src="'/templates/itemsView.html'">
			</ng-include>
		    <ng-include id="content-locations" class="content hidden"
			    src="'/templates/locationsView.html'">
			</ng-include>
			<ng-include id="content-notifications" class="content hidden"
			    src="'/templates/notificationsView.html'">
			</ng-include>
			<ng-include id="content-orders" class="content hidden"
			    src="'/templates/ordersView.html'">
			</ng-include>
		</div>
		<div class="col-md-2">
		</div>
	</div>
</div>

<?php
}
?>
</body>
</html>
