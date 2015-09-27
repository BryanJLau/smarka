$(document).ready(function () {    
    $(".nav-pills > .enabled").click(function () {
        $(".active").removeClass("active");
        $(this).addClass("active");
    });
});

var adminApp = angular.module('adminApp', []);
adminApp.controller('AdminCtrl', function ($scope, $http, $timeout) {
    // Initialize Angular objects to hold forms
    $scope.init = function () {
        $scope.addLocationsForm = {
            location: ""
        };
        $scope.addNotificationsForm = {
            text: ""
        };
        
        $scope.items = [];
        $scope.locations = [];
        $scope.notification = "";
        $scope.orders = {
            orders: [],         // Actual orders array
            requiredItems: [],  // Required items for this week's batch
            all: false          // Is requesting all, enables "pay all" button
        };
    }
    
    // +-----------------+
    // |    Locations    |
    // +-----------------+
    // Display locations
    $scope.showLocations = function () {
        $http.get("/locations")
            .success(function(response) {
                $('.content').addClass('hidden');
                $('#content-locations').removeClass('hidden');
                $scope.locations = response;
            });
    };
    
    // Show the modal
    // Doesn't need to be in controller, but for the sake of extensibility
    $scope.showAddLocationModal = function () {
        $('#addLocationsModal').modal('show');
    }
    
    // Add a location
    $scope.addLocation = function () {
        $('#addLocationLoaderGif').removeClass('hidden');
        $('#addLocationsErrorDiv').addClass('hidden');
        
        // Set the POST data
        var addLocationData = {
            location: $scope.addLocationsForm.location
        };
        
        //if(typeof $scope.addLocationsForm.location === 'string') {
        $http.post('/locations', addLocationData)
            .then(function (response) {
                // Return everything to normal
                $('#addLocationLoaderGif').addClass('hidden');
                $('#addLocationsModal').modal('hide');

                // Reset the form
                $scope.addLocationsForm.location = "";
                
                // Reload notifications data
                $scope.showLocations();
            },
            function (error) {
                // Show the error message containing the error
                $('#addLocationLoaderGif').addClass('hidden');
                $('#addLocationsErrorMessage').text(error.data);
                $('#addLocationsErrorDiv').removeClass('hidden');
            });
        /*
        } else {
            // This happens because the model addLocationForm.location
            // is bound to "undefined". I can't figure out a way to default
            // it to "", so this is a small workaround to prevent sending
            // what Javascript says is a "circular structure".
            $('#addLocationLoaderGif').addClass('hidden');
            $('#addLocationsErrorMessage')
                .text("Please provide a time and location.");
            $('#addLocationsErrorDiv').removeClass('hidden');
        }
        */
    };
    
    $scope.deleteLocation = function (id) {
        $('#deleteLocationsSuccessDiv').addClass('hidden');
        $http.delete('/locations/' + id)
            .success(function (data, status, headers) {
                // Reload notifications data
                $scope.showLocations();
            })
            .error(function (data, status, header, config) {
                // Alert the user
                alert("Failed to delete goal: " + data);
            });
    }
    
    // +---------------------+
    // |    Notifications    |
    // +---------------------+
    // Display notifications
    $scope.showNotifications = function () {
        $http.get("/notifications")
            .success(function(response) {
                $('.content').addClass('hidden');
                $('#content-notifications').removeClass('hidden');
                // \n doesn't translate automatically to <br>, do that here
                $scope.notification = response.text || "Notification not set.";
            });
    };
    
    // Show the modal
    // Doesn't need to be in controller, but for the sake of extensibility
    $scope.showAddNotificationModal = function () {
        $('#addNotificationsModal').modal('show');
    }
    
    // Add a location
    $scope.addNotification = function () {
        $('#addNotificationLoaderGif').removeClass('hidden');
        $('#addNotificationsErrorDiv').addClass('hidden');
        
        // Set the POST data
        var addNotificationData = {
            text: $scope.addNotificationsForm.text
        };
        
        $http.post('/notifications', addNotificationData)
            .then(function (response) {
                // Return everything to normal
                $('#addNotificationLoaderGif').addClass('hidden');
                $('#addNotificationsModal').modal('hide');

                // Reset the form
                $scope.addNotificationsForm.text = "";
                
                // Reload notifications data
                $scope.showNotifications();
            },
            function (error) {
                // Show the error message containing the error
                $('#addNotificationLoaderGif').addClass('hidden');
                $('#addNotificationsErrorMessage').text(error.data);
                $('#addNotificationsErrorDiv').removeClass('hidden');
            });
    };
    
    // +--------------+
    // |    Orders    |
    // +--------------+
    // Display pending orders
    $scope.showPendingOrders = function () {
        $('.orders-nav').removeClass('active');
        $('#orders-pending-tab').addClass('active');
        
        $scope.orders.all = false;
        $('.orderToggle').bootstrapToggle();
        
        $http.get("/orders")
            .success(function(response) {
                $('.content').addClass('hidden');
                $('#content-orders').removeClass('hidden');
                $scope.orders.orders = response.orders;
                $scope.orders.requiredItems = response.requiredItems;
            });
    };
    
    // Show all orders (dangerous)
    $scope.showAllOrders = function () {
        $('.orders-nav').removeClass('active');
        $('#orders-completed-tab').addClass('active');
        
        $scope.orders.all = true;
        $('.orderToggle').bootstrapToggle();
        
        $http.get("/orders?all=true")
            .success(function(response) {
                $('.content').addClass('hidden');
                $('#content-orders').removeClass('hidden');
                $scope.orders.orders = response.orders;
                $scope.orders.requiredItems = response.requiredItems;
            });
    };
    
    // Toggle an order from paid to unpaid and vice versa
    $scope.toggleOrder = function (id) {
        $http.put('/orders/' + id, {})
            .then(function (response) {
                // Just reload data
                if($scope.orders.all) {
                    $scope.showAllOrders();
                }
                else {
                    $scope.showPendingOrders();
                }
            },
            function (error) {
                alert(error.data);
            });
    };
    
    // Pay off all the orders at once
    $scope.payAllOrders = function () {
        $http.post('/orders/all', {})
            .then(function (response) {
                // Just reload to tell user it's done
                $scope.showPendingOrders();
            },
            function (error) {
                alert(error.data);
            });
    };
});
