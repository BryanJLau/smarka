<!DOCTYPE html>
<html>
<meta charset="UTF-8">
<title>Hom's Kitchen</title>

<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="/js/cart.js"></script>
<script src="/js/jquery.toastmessage.js"></script>
<link rel="stylesheet" href="/css/jquery.toastmessage.css">

<body ng-app="cartApp" ng-controller="CartCtrl" ng-init="init()">

<div id="checkoutModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Checkout</h4>
            </div>
            <div class="modal-body">
                <div id="checkoutErrorDiv" 
                    class='alert alert-danger hidden'>
                    <span class="glyphicon glyphicon-warning-sign"></span>
                    &nbsp;
                    <span id="checkoutErrorMessage"></span>
                </div>

                <div>
                    <table class="table table-striped table-responsive">
                        <tr>
                            <td></td>
                            <td>Quantity</td>
                            <td>Name</td>
                            <td>Total</td>
                        </tr>
                        <tr ng-repeat="(name, item) in cart">
                            <td>
                                <button class="btn btn-danger btn-xs"
                                    ng-click="deleteItem(name)">
                                <span class="glyphicon glyphicon-remove"></span>
                                </button>
                            </td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ name }}</td>
                            <td>${{ item.total }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Total</td>
                            <td>${{ cartTotal }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Begin form -->
                <form id="checkoutForm" name="checkoutForm" role="form">
                    <div class="form-group has-feedback">
                        <label>Name:</label>
                        <input type="text" class="form-control"
                            ng-model="checkoutData.name">
                    </div>
                    <div class="form-group has-feedback">
                        <label>Phone:</label>
                        <input type="text" class="form-control"
                            ng-model="checkoutData.phone"><br>
                        <div class='alert alert-info'>
                            Please provide a phone number with text message 
                            capability. Standard text messaging rates and fees 
                            may apply.
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label>Email (Optional):</label>
                        <input type="text" class="form-control"
                            ng-model="checkoutData.email"><br>
                        <div class='alert alert-info'>
                            A confirmation email will be sent to this email 
                            if provided.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sel1">Select list:</label>
                        <select class="form-control" id="location">
                            <option ng-repeat="location in locations"
                                value="{{ location }}">{{ location }}</option>
                        </select>
                    </div>
                    <div class="form-group has-feedback">
                        <label>Notes to seller (Optional):</label>
                        <input type="text" class="form-control"
                            ng-model="checkoutData.notes"><br>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <img id="checkoutLoaderGif" src="/images/ajax-loader.gif"
                    class="hidden"/>
                <button type="button" class="btn btn-default"
                    data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary"
                    ng-click="sendOrder()">Add item</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<nav class="navbar navbar-default navbar-fixed-top"
			        role="navigation">
				<div class="navbar-header">
					<a class="navbar-brand" href="#">Hom's Kitchen</a>
				</div>
				
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li ng-click="showCheckoutModal()">
							<a href="#">{{ checkoutText }}</a>
						</li>
						<!-- Placeholder so that the above button isn't cut -->
						<li>
							<a href="#"></a>
						</li>
					</ul>
				</div>
				
			</nav>
			<div class="jumbotron">
				<h2>
					Hom's Kitchen <small>(626)807-9898, prettyching821@hotmail.com</small>
				</h2>
				<p>
					We are a family owned business in the service of providing 
					high quality authentic Chinese food at affordable prices.
				</p>
				<p>
				    The menu is available from Sunday to Friday 9 PM, after 
				    which ordering is finished for the week. Some items are 
				    seasonal or ave limited availability. Items may be 
				    retroactively cancelled, but you will not be charged for 
				    the cancelled items. We accept cash payments only. Please 
				    try to bring exact change at the time of pickup.
				</p>
				<p>
					<a class="btn btn-primary btn-large" href="#" ng-click="showCheckoutModal()">Checkout Cart</a>
				</p>
			</div>
			<div class="page-header">
				<h1>
					This week's menu: <small>(menu rotates every week)</small>
				</h1>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4" ng-repeat="item in items">
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					<h3 class="panel-title">
						{{ item.name }}
					</h3>
				</div>
				<div class="panel-body">
				    <img class="img-responsive center-block" 
				        ng-src="{{ item.picture1Path }}"><br>
			        <div class='alert alert-info'>
					    <p>Description:</p>
					    <p class="lead">{{ item.description }}</p>
				    </div>
				    <div class='alert alert-warning'>
					    <p>Price:</p>
					    <p class="lead">${{ item.price }}</p>
				    </div>
				</div>
				<div class="panel-footer">
					<div class="input-group">
                        <input class="form-control" type="number"
                            placeholder="Quantity" id="{{ item.hash }}">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"
                                ng-click="addItem(item.name, item.hash)">
                                Add to Cart
                            </button>
                        </span>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>
