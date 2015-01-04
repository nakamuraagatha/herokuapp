var myApp = angular.module('myApp', ['ngRoute', 'ngProgress']);
myApp.config(['$routeProvider', function ($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: 'templates/users/home.html',
            controller: 'AppCtrl'
        });

}]).controller('AppCtrl', ['$scope', '$http', 'ngProgress', '$window', function ($scope, $http, ngProgress, $window) {
    if ($window.sessionStorage.token == "undefined") {
        window.location = "/";
    }
    $http.defaults.headers.common.Authorization = $window.sessionStorage.token;
    ngProgress.start();
    var promise = $http.get('/userDetails/users').success(function (data, status, headers, config) {
        angular.element(document.querySelector('title')).text('Welcome! ' + data.displayName);
    }).error(function (data, status, headers, config) {
        window.location = "/login"
    });

    promise.then(function () {
        $http.get('api/usersList').success(function (data, status, headers, config) {
            $scope.users = [];
            $scope.checkId = [];
            angular.forEach(data, function (value, key) {
                this.push(value);
            }, $scope.users);
            $scope.check = {};
            $scope.selects = {};
            $.each($scope.users, function (index, value) {
                $scope.check[value._id.$id] = [false, false];
                $scope.selects[value._id.$id] = "Select";
            });
            ngProgress.complete();
        }).error(function (data, status, headers, config) {
            window.location = "/";
            return;
        });
    }, function (reason) {
        console.log('Failed: ' + reason);
    }, function (update) {
        console.log('Got notification: ' + update);
    });
    promise.then(function () {
        $http.get('api/appList').success(function (data, status, headers, config) {
            $scope.apps = [];
            angular.forEach(data, function (value, key) {
                this.push(value);
            }, $scope.apps);
            ngProgress.complete();
        }).error(function (data, status, headers, config) {
            console.log(status);
        });
    }, function (reason) {
        console.log('Failed: ' + reason);
    }, function (update) {
        console.log('Got notification: ' + update);
    });

    $scope.change = function (appName, id) {
        $scope.selects[id] = appName;
        if (appName) {
            ngProgress.start();
            $http.get('api/appPermissions/' + appName + '/' + id)
                .success(function (data, status, headers, config) {
                    $scope.check[id] = data;
                    ngProgress.complete();
                }).error(function (data, status, headers, config) {
                    console.log(status);
                });
        }
    };

    $scope.updateChange = function (appName, id) {
        if (appName) {
            $http.post('api/appPermissions/' + appName + '/' + id,
                [$scope.check[id][0], $scope.check[id][1]])
                .success(function (data, status, headers, config) {
                    ngProgress.complete();
                    $scope.check[id] = data;
                }).error(function (data, status, headers, config) {
                    console.log(status);
                });
        }
    };

}]);