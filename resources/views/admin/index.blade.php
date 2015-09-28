<!DOCTYPE html>
<html>
<title>Hom's Kitchen - Administrator Panel</title>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="http://malsup.github.com/jquery.form.js"></script>
<script src="/js/admin.js"></script>
<body ng-app="adminApp" ng-controller="AdminCtrl" ng-init="init()">
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
</body>
</html>
