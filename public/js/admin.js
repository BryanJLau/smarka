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
        $scope.addItemsForm = {
            name: "",
            description: "",
            price: "",
            active: false
        };
        $scope.editItemsForm = {
            name: "",
            description: "",
            price: "",
            active: false
        };
        
        $scope.editItemsPicturesForm = {
            id: -1,
            dp2: false
        };
        
        $scope.editingItemId = -1;  // Not editing at the moment
        
        $scope.items = [];
        $scope.locations = [];
        $scope.notification = "";
        $scope.orders = {
            orders: [],         // Actual orders array
            requiredItems: [],  // Required items for this week's batch
            all: false          // Is requesting all, enables "pay all" button
        };
        
        // Initialize the items view
        $scope.showItems();
    }
    
    // +-------------+
    // |    Items    |
    // +-------------+
    // Display items
    $scope.showItems = function () {
        $http.get("/items?all=true")
            .success(function(response) {
                $('.content').addClass('hidden');
                $('#content-items').removeClass('hidden');
                $scope.items = response;
                for(var i = 0; i < $scope.items.length; i++) {
                    $scope.items[i].picture1Path = "/uploads/" +
                        $scope.items[i].hash + "_1.jpg";
                    if($scope.items[i].picture2 == 1)
                        $scope.items[i].picture2Path = "/uploads/" +
                            $scope.items[i].hash + "_2.jpg";
                }
            });
    };
    
    // Show the modal
    // Doesn't need to be in controller, but for the sake of extensibility
    $scope.showAddItemModal = function () {
        $('#addItemsModal').modal('show');
    }
    $scope.showEditItemModal = function (id) {
        $scope.editingItemId = id;
        $('#editItemsModal').modal('show');
    }
    
    $scope.editItemsPicturesModal = function (id) {
        $scope.editingItemId = id;
        $scope.editItemsPicturesForm.id = id;
        $('#editItemsPicturesModal').modal('show');
    }
    
    // Store the pictures
    $scope.upload1 = function(el) {
        $scope.addItemsForm.picture1 = el.files[0];
    };
    $scope.upload2 = function(el) {
        $scope.addItemsForm.picture2 = el.files[0];
    };
    $scope.eupload1 = function(el) {
        $scope.editItemsPicturesForm.picture1 = el.files[0];
    };
    $scope.eupload2 = function(el) {
        $scope.editItemsPicturesForm.picture2 = el.files[0];
    };
    
    // Add an item
    $scope.addItem = function () {
        $('#addItemLoaderGif').removeClass('hidden');
        $('#addItemsErrorDiv').addClass('hidden');
        
        // Code adapted from:
        // http://stackoverflow.com/questions/16483873/angularjs-http-post-file-and-form-data
        $http({
            method: 'POST',
            url: '/items',
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            data: $scope.addItemsForm,
            transformRequest: function (data, headersGetter) {
                var formData = new FormData();
                angular.forEach(data, function (value, key) {
                    formData.append(key, value);
                });

                var headers = headersGetter();
                delete headers['Content-Type'];

                return formData;
            }
        })
        .then(function (response) {
            // Return everything to normal
            $('#addItemLoaderGif').addClass('hidden');
            $('#addItemsModal').modal('hide');

            // Reset the form
            $scope.addItemsForm.name = "";
            $scope.addItemsForm.description = "";
            $scope.addItemsForm.price = "";
            $scope.addItemsForm.active = false;
            $scope.addItemsForm.picture1 = "";
            $scope.addItemsForm.picture1 = "";
            
            // Reload notifications data
            $scope.showItems();
        },
        function (error) {
            // Show the error message containing the error
            $('#addItemLoaderGif').addClass('hidden');
            $('#addItemsErrorMessage').text(error.data);
            $('#addItemsErrorDiv').removeClass('hidden');
        });
    };
    
    $scope.editItem = function () {
        $('#editItemLoaderGif').removeClass('hidden');
        $('#editItemsErrorDiv').addClass('hidden');
        
        // Code adapted from:
        // http://stackoverflow.com/questions/16483873/angularjs-http-post-file-and-form-data
        $http({
            method: 'PUT',
            url: '/items/' + $scope.editingItemId,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $scope.editItemsForm,
            transformRequest: function (data, headersGetter) {
                // Solution taken from:
                // http://stackoverflow.com/questions/24710503/how-do-i-post-urlencoded-form-data-with-http-in-angularjs
                var str = [];
                for(var p in data)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(data[p]));
                return str.join("&");
            }
        })
        .then(function (response) {
            // Return everything to normal
            $('#editItemLoaderGif').addClass('hidden');
            $('#editItemsModal').modal('hide');

            // Reset the form
            $scope.editItemsForm.name = "";
            $scope.editItemsForm.description = "";
            $scope.editItemsForm.price = "";
            $scope.addItemsForm.active = false;
            
            // Reload notifications data
            $scope.showItems();
        },
        function (error) {
            // Show the error message containing the error
            $('#editItemLoaderGif').addClass('hidden');
            $('#editItemsErrorMessage').text(error.data);
            $('#editItemsErrorDiv').removeClass('hidden');
        });
    };
    
    $scope.deleteItem = function (id) {
        $http.delete('/items/' + id)
            .success(function (data, status, headers) {
                // Reload notifications data
                $scope.showItems();
            })
            .error(function (data, status, header, config) {
                // Alert the user
                alert("Failed to delete item: " + data);
            });
    }
    
    $scope.editItemPictures = function () {
        $('#editItemPicturesLoaderGif').removeClass('hidden');
        $('#editItemsPicturesErrorDiv').addClass('hidden');
        
        // Code adapted from:
        // http://stackoverflow.com/questions/16483873/angularjs-http-post-file-and-form-data
        $http({
            method: 'POST',
            url: '/items/changePictures',
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            data: $scope.editItemsPicturesForm,
            transformRequest: function (data, headersGetter) {
                var formData = new FormData();
                angular.forEach(data, function (value, key) {
                    formData.append(key, value);
                });

                var headers = headersGetter();
                delete headers['Content-Type'];

                return formData;
            }
        })
        .then(function (response) {
            // Return everything to normal
            $('#editItemPicturesLoaderGif').addClass('hidden');
            $('#editItemsPicturesModal').modal('hide');

            // Reset the form
            $scope.editItemsPicturesForm.id = -1;
            $scope.editItemsPicturesForm.picture1 = "";
            $scope.editItemsPicturesForm.picture2 = "";
            $scope.editItemsPicturesForm.dp2 = false;
            
            // Reload notifications data
            $scope.showItems();
        },
        function (error) {
            // Show the error message containing the error
            $('#editItemPicturesLoaderGif').addClass('hidden');
            $('#editItemsPicturesErrorMessage').text(error.data);
            $('#editItemsPicturesErrorDiv').removeClass('hidden');
        });
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
        $http.delete('/locations/' + id)
            .success(function (data, status, headers) {
                // Reload notifications data
                $scope.showLocations();
            })
            .error(function (data, status, header, config) {
                // Alert the user
                alert("Failed to delete location: " + data);
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
