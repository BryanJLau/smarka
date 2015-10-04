var cartApp = angular.module('cartApp', []);
cartApp.controller('CartCtrl', function ($scope, $http, $timeout) {
    // Initialize Angular objects to hold forms
    $scope.init = function () {
        $scope.checkoutData = {
            item_array: [],
            name: "",
            phone: "",
            email: "",
            location: "",
            notes: ""
        };
        
        $scope.cart = {};
        $scope.items = [];
        $scope.locations = [];
        $scope.checkoutText = "Checkout";
        $scope.cartTotal = 0;
        
        // Anonymous function to get item information
        (function () {
            $http.get("/items")
            .then(
                function(response) {
                    $scope.items = response.data;
                    for(var i = 0; i < $scope.items.length; i++) {
                        $scope.items[i].picture1Path = "/uploads/" +
                            $scope.items[i].hash + "_1.jpg";
                        if($scope.items[i].picture2 == 1)
                            $scope.items[i].picture2Path = "/uploads/" +
                                $scope.items[i].hash + "_2.jpg";
                    }
                },
                function(data) {
                    // Error
                    if(data.status == 503) {
                        // Menu unavailable
                        console.log("fdsa");
                    }
                    console.log(data);
                }
            );
        })();
        
        // Anonymous function to get locations
        (function () {
            $http.get("/locations")
            .then(
                function(response) {
                    console.log(response);
                    for(var i = 0; i < response.data.length; i++) {
                        $scope.locations.push(response.data[i].location);
                    }
                    console.log($scope.locations);
                },
                function(data) {
                    // Error
                    if(data.status == 503) {
                        // Menu unavailable
                        console.log("fdsa");
                    }
                    console.log(data);
                }
            );
        })();
    }
    
    $scope.showCheckoutModal = function () {
        $('#checkoutModal').modal('show');
    }
    
    $scope.addItem = function (itemName, hash) {
        var itemPrice = 0;
        for(var i = 0; i < $scope.items.length; i++) {
            if(itemName == $scope.items[i].name) {
                // Get the price for the item so we can save and multiply it
                itemPrice = $scope.items[i].price;
                break;
            }
        }
    
        $scope.cart[itemName] = {
            quantity: parseInt($("#" + hash).val()),
            total: parseInt($("#" + hash).val()) * itemPrice
        };
        
        if($scope.cart[itemName] <= 0) {
            // Want to cancel it
            delete $scope.cart[itemName];
            $().toastmessage('showNoticeToast', 'Deleted item from cart.');
        }
        else {
            $().toastmessage('showSuccessToast',
                'Successfully added item to cart');
        }
        
        var totalItems = 0;
        var itemsTotal = 0;
        Object.keys($scope.cart).forEach(function(key) {
            totalItems += $scope.cart[key].quantity;
            itemsTotal += $scope.cart[key].total;
        });
        $scope.cartTotal = itemsTotal;
        
        if(totalItems > 0) {
            $scope.checkoutText = "Checkout(" + totalItems + ")";
        }
        else {
            $scope.checkoutText = "Checkout"
        }
    }
    
    $scope.deleteItem = function(itemName) {
        delete $scope.cart[itemName];
        $().toastmessage('showNoticeToast', 'Deleted item from cart.');
        
        var itemsTotal = 0;
        Object.keys($scope.cart).forEach(function(key) {
            totalItems += $scope.cart[key].quantity;
            itemsTotal += $scope.cart[key].total;
        });
        $scope.cartTotal = itemsTotal;
    }
    
    // Checkout
    $scope.sendOrder = function () {
        $('#checkoutLoaderGif').removeClass('hidden');
        $('#checkoutErrorDiv').addClass('hidden');
        
        $scope.checkoutData.location = $("#location").val();
        
        Object.keys($scope.cart).forEach(function(key) {
            $scope.checkoutData.item_array.push(
                {
                    name: key,
                    qty: $scope.cart[key]
                }
            );
        });
        
        $scope.checkoutData.item_array = 
            JSON.stringify($scope.checkoutData.item_array);
        
        console.log($scope.checkoutData);
        
        $http.post('/orders', $scope.checkoutData)
            .then(function (response) {
            console.log(response);
                // Return everything to normal
                $('#checkoutLoaderGif').addClass('hidden');
                $('#checkoutModal').modal('hide');

                // Reset the form
                $scope.checkoutData = {
                    item_array: [],
                    name: "",
                    phone: "",
                    email: "",
                    location: "",
                    notes: ""
                };
                $scope.cart = {};
                $scope.checkoutText = "Checkout";
                $scope.cartTotal = 0;
                
                $().toastmessage('showSuccessToast',
                    'Successfully submitted order!');
            },
            function (error) {
                // Show the error message containing the error
                if(error.status == 503) {
                    // Ordering unavailable
                    error.data = "The ordering period has ended. Please try \
                        again on Sunday with our new menu!";
                }
                
                $('#checkoutLoaderGif').addClass('hidden');
                $('#checkoutErrorMessage').text(error.data);
                $('#checkoutErrorDiv').removeClass('hidden');
            }
        );
    };
});
