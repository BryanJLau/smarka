$(document).ready(function () {
    $('#addLocationsModal').modal('show');
    $("textarea#locationsLocation").val("");
});

var adminApp = angular.module('adminApp', []);
adminApp.controller('AdminCtrl', function ($scope, $http, $timeout) {
    // Initialize Angular objects to hold forms
    $scope.init = function () {
        $scope.addLocationsForm = {
            location: ""
        };
    }
    
    // +-----------------+
    // |    Locations    |
    // +-----------------+
    // Display locations
    $scope.showLocations = function () {
        
    };
    
    // Add a location
    $scope.addLocation = function () {
        $('#addLocationLoaderGif').removeClass('hidden');
        $('#addLocationsErrorDiv').addClass('hidden');
        
        // Set the POST data
        var addLocationData = {
            location: $scope.addLocationsForm.location
        };
        
        if(typeof $scope.addLocationsForm.location === 'string') {
            $http.post('/locations', addLocationData)
                .then(function (response) {
                    // Return everything to normal
                    $('#addLocationLoaderGif').addClass('hidden');
                    $('#addLocationsModal').modal('hide');

                    // Reset the form
                    $scope.addLocationsForm.location = "";
                    $scope.addLocationsForm.$setPristine();
                },
                function (error) {
                    // Show the error message containing the error
                    $('#addLocationLoaderGif').addClass('hidden');
                    $('#addLocationsErrorMessage').text(error.data);
                    $('#addLocationsErrorDiv').removeClass('hidden');
                });
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
    };
});
