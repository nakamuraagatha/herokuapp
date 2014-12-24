var myApp = angular.module('myApp', ['ngRoute', 'ngProgress']);
myApp.config(['$routeProvider', function ($routeProvider) {
        $routeProvider
                .when('/', {
                    templateUrl: 'templates/users/home.html',
                    controller: 'AppCtrl'
                });

    }]).controller('AppCtrl', ['$scope', '$http', 'ngProgress', function ($scope, $http, ngProgress) {
        ngProgress.start();
        var promise = $http.get('/userDetails/users').success(function (data, status, headers, config) {
            $http.defaults.headers.common.Authorization = data.api_key;
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
                $.each($scope.users, function (index, value) {
                    $scope.check[value.email] = [false, false];
                });
                ngProgress.complete();
            }).error(function (data, status, headers, config) {
                console.log(status);
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

        $scope.change = function (appSelcted, email) {
            var appName = null !== appSelcted ? appSelcted.name : false;
            if (appName) {
                ngProgress.start();
                $http.get('api/appPermissions/' + appName + '/' + email)
                        .success(function (data, status, headers, config) {
                            $scope.check[email] = data;
                            ngProgress.complete();
                        }).error(function (data, status, headers, config) {
                    console.log(status);
                });
            }
        };

        $scope.updateChange = function (appSelcted, email) {
            var appName = typeof appSelcted !== 'undefined' ? appSelcted.name : false;
            if (appName) {
                $http.post('api/appPermissions/' + appName + '/' + email,
                        [$scope.check[email][0], $scope.check[email][1]])
                        .success(function (data, status, headers, config) {
                            ngProgress.complete();
                            $scope.check[email] = data;
                        }).error(function (data, status, headers, config) {
                    console.log(status);
                });
            }
        };
    }]);