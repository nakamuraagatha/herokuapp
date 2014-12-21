var myApp = angular.module('myApp', ['ngRoute', 'ngProgress']);
myApp.config(['$routeProvider', function ($routeProvider) {
        $routeProvider
                .when('/', {
                    templateUrl: 'templates/quotes/home.html',
                    controller: 'HomeCtrl'
                })
                .when('/quotes', {
                    templateUrl: 'templates/quotes/quotes.html',
                    controller: 'QuoteCtrl'
                });

    }]).factory('ctgId', [function () {
        var ctgId = "";
        function setCtgId($ctgId) {
            ctgId = $ctgId;
        }
        function getCtgId() {
            return ctgId;
        }
        return {
            getCtgId: getCtgId,
            setCtgId: setCtgId
        };
    }]).controller('HomeCtrl', ['$scope', '$http', 'ngProgress', '$location', 'ctgId',
    function ($scope, $http, ngProgress, $location, ctgId) {
        ngProgress.start();
        var promise = $http.get('/userDetails').success(function (data, status, headers, config) {
            $http.defaults.headers.common.Authorization = data.api_key;
            angular.element(document.querySelector('title')).text('Welcome! ' + data.displayName);
        }).error(function (data, status, headers, config) {
            window.location = "/login"
        });

        promise.then(function () {
            $http.get('api/category').success(function (data, status, headers, config) {
                $scope.ctgs = [];
                angular.forEach(data, function (value, key) {
                    this.push(value);
                }, $scope.ctgs);
                ngProgress.complete();
            }).error(function (data, status, headers, config) {
                console.log(status);
            });
        }, function (reason) {
            console.log('Failed: ' + reason);
        }, function (update) {
            console.log('Got notification: ' + update);
        });

        $scope.createCategory = function () {
            ngProgress.start();
            $http.post('api/category',
                    {
                        'name': $scope.newCtgText
                    }
            ).success(function (data, status, headers, config) {
                ngProgress.complete();
                $scope.ctgs.push(data);
                $scope.showCreate = false;
                $scope.newCtgText = "";
            }).error(function (data, status, headers, config) {
                console.log(status);
            });
        };

        $scope.saveCategory = function (index) {
            ngProgress.start();
            $http.put('api/category/' + $scope.ctgs[index]._id.$id,
                    {
                        'name': $scope.ctgs[index].name
                    }
            ).success(function (data, status, headers, config) {
                ngProgress.complete();
                data._id = {$id: $scope.ctgs[index]._id.$id};
                $scope.ctgs[index] = data;
                $scope.saving[index] = false;
            }).error(function (data, status, headers, config) {
                console.log(status);
            });
        };
        $scope.saving = [];
        $scope.editCategory = function (index) {
            $scope.saving[index] = true;
        };

        $scope.deleteCategory = function (index) {
            ngProgress.start();
            $http.delete('api/category/' + $scope.ctgs[index]._id.$id, {}
            ).success(function (data, status, headers, config) {
                ngProgress.complete();
                if (data === "Successsfully Deleted!") {
                    $scope.ctgs.splice(index, 1);
                }
            }).error(function (data, status, headers, config) {
                console.log(status);
            });
        };

        $scope.showQuotes = function (index) {
            ctgId.setCtgId($scope.ctgs[index]._id.$id);
            $location.path('/quotes');
        };
    }]).controller('QuoteCtrl', ['$scope', 'ctgId', '$http', 'ngProgress', '$location',
    function ($scope, ctgId, $http, ngProgress, $location) {
        $http.get('api/quote/' + ctgId.getCtgId()).success(function (data, status, headers, config) {
            $scope.quotes = [];
            angular.forEach(data, function (value, key) {
                this.push(value);
            }, $scope.quotes);
            ngProgress.complete();
        }).error(function (data, status, headers, config) {
            $location.path('/');
        });

        $scope.createQuote = function () {
            ngProgress.start();
            $http.post('api/quote',
                    {
                        'text': $scope.newQuoteText,
                        'author': $scope.newQuoteAuthor,
                        'ctg_id': ctgId.getCtgId()
                    }
            ).success(function (data, status, headers, config) {
                console.log(data);
                ngProgress.complete();
                $scope.quotes.push(data);
                $scope.showCreate = false;
                $scope.newQuoteText = "";
                $scope.newQuoteAuthor = "";
            }).error(function (data, status, headers, config) {
                console.log(status);
            });
        };

        $scope.saveQuote = function (index) {
            ngProgress.start();
            $http.put('api/quote/' + $scope.quotes[index]._id.$id,
                    {
                        'text': $scope.quotes[index].text,
                        'author': $scope.quotes[index].author,
                        'ctg_id': ctgId.getCtgId()
                    }
            ).success(function (data, status, headers, config) {
                ngProgress.complete();
                data._id = {$id: $scope.quotes[index]._id.$id};
                $scope.quotes[index] = data;
                $scope.saving[index] = false;
            }).error(function (data, status, headers, config) {
                console.log(status);
            });
        };
        $scope.saving = [];
        $scope.editQuote = function (index) {
            $scope.saving[index] = true;
        };

        $scope.deleteQuote = function (index) {
            ngProgress.start();
            $http.delete('api/quote/' + $scope.quotes[index]._id.$id, {}
            ).success(function (data, status, headers, config) {
                ngProgress.complete();
                if (data === "Successsfully Deleted!") {
                    $scope.quotes.splice(index, 1);
                }
            }).error(function (data, status, headers, config) {
                console.log(status);
            });
        };
    }]);