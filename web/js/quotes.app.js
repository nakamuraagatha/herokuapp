var x;
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
        var ctgId, ctgObject;
        function setCtgId($ctgId) {
            ctgId = $ctgId;
        }
        function getCtgId() {
            return ctgId;
        }
        function getCtgObject() {
            return ctgObject;
        }
        function setCtgObject($ctgObject) {
            ctgObject = $ctgObject;
        }
        return {
            getCtgId: getCtgId,
            setCtgId: setCtgId,
            getCtgObject: getCtgObject,
            setCtgObject: setCtgObject
        };
    }]).filter()
        .directive('colorMe', [function () {
                function link(scope, element, attrs) {
                    var colors = ["#000000", "#795548", "#4CAF50", "#009688", "#673AB7", "#FF5722", "#F44336"];
                    var randomColor = colors[Math.floor(Math.random() * colors.length)];
                    element.css('color', randomColor);
                }
                return {
                    link: link
                };
            }]).controller('HomeCtrl', ['$scope', '$http', 'ngProgress', '$location', 'ctgId',
    function ($scope, $http, ngProgress, $location, ctgId) {
        if (typeof ctgId.getCtgObject() !== 'undefined') {
            if (ctgId.getCtgObject().isAuthor) {
                $scope.showAuthors = true;
            }
        }
        ngProgress.start();
        var promise = $http.get('/userDetails').success(function (data, status, headers, config) {
            $http.defaults.headers.common.Authorization = data.api_key;
            angular.element(document.querySelector('title')).text('Welcome! ' + data.displayName);
        }).error(function (data, status, headers, config) {
            window.location = "/login"
        });

        promise.then(function () {
            $http.get('api/category').success(function (data, status, headers, config) {
                var dataArray = [];
                angular.forEach(data, function (value, key) {
                    this.push(value);
                }, dataArray);
                x = $scope.ctgs;
                $scope.ctgs = $.map(dataArray, function (val) {
                    if (val.type === "ctg")
                        return val;
                });
                $scope.authors = $.map(dataArray, function (val) {
                    if (val.type === "author")
                        return val;
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
        $scope.createCategory = function () {
            var ctgType = $scope.showAuthors ? "author" : "ctg";
            ngProgress.start();
            $http.post('api/category',
                    {
                        'name': $scope.newCtgText,
                        'type': ctgType
                    }
            ).success(function (data, status, headers, config) {
                ngProgress.complete();
                if (ctgType === "ctg") {
                    $scope.ctgs.push(data);
                } else {
                    $scope.authors.push(data);
                }
                $scope.showCreate = false;
                $scope.newCtgText = "";
            }).error(function (data, status, headers, config) {
                console.log(status);
            });
        };

        $scope.saveCategory = function (index) {
            ngProgress.start();
            var id, request;
            if ($scope.showAuthors) {
                id = $scope.authors[index]._id.$id;
                request = {
                    'name': $scope.authors[index].name,
                    'type': $scope.authors[index].type
                };
            } else {
                id = $scope.ctgs[index]._id.$id;
                request = {
                    'name': $scope.ctgs[index].name,
                    'type': $scope.ctgs[index].type
                };
            }
            $http.put('api/category/' + id, request
                    ).success(function (data, status, headers, config) {
                ngProgress.complete();
                if ($scope.showAuthors) {
                    data._id = {$id: $scope.authors[index]._id.$id};
                    $scope.authors[index] = data;
                    $scope.saving_author[index] = false;
                } else {
                    data._id = {$id: $scope.ctgs[index]._id.$id};
                    $scope.ctgs[index] = data;
                    $scope.saving[index] = false;
                }

            }).error(function (data, status, headers, config) {
                console.log(status);
            });
        };
        $scope.saving = [];
        $scope.saving_author = [];
        $scope.editCategory = function (index) {
            if ($scope.showAuthors) {
                $scope.saving_author[index] = true;
            } else {
                $scope.saving[index] = true;
            }
        };

        $scope.deleteCategory = function (index) {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this category and it's quotes!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    ngProgress.start();
                    var id;
                    if ($scope.showAuthors) {
                        id = $scope.authors[index]._id.$id;
                    } else {
                        id = $scope.ctgs[index]._id.$id;
                    }
                    $http.delete('api/category/' + id, {}
                    ).success(function (data, status, headers, config) {
                        ngProgress.complete();
                        if (data === "Successsfully Deleted!") {
                            if ($scope.showAuthors) {
                                $scope.authors.splice(index, 1);
                            } else {
                                $scope.ctgs.splice(index, 1);
                            }
                            swal("Deleted!", "Your category has been deleted.", "success");
                        }
                    }).error(function (data, status, headers, config) {
                        console.log(status);
                    });
                } else {
//                    swal("Cancelled", "Your imaginary file is safe :)", "error");
                    console.log('action cancelled');
                }
            });
        };

        $scope.showQuotes = function (index) {
            if ($scope.showAuthors) {
                ctgId.setCtgId($scope.authors[index]._id.$id);
                ctgId.setCtgObject({name: $scope.authors[index].name, isAuthor: true});
            } else {
                ctgId.setCtgId($scope.ctgs[index]._id.$id);
                ctgId.setCtgObject({name: $scope.authors[index].name, isAuthor: false});
            }
            $location.path('/quotes');
        };

        $scope.getHome = function () {
            $location.path('/');
        };
    }]).controller('QuoteCtrl', ['$scope', 'ctgId', '$http', 'ngProgress', '$location',
    function ($scope, ctgId, $http, ngProgress, $location) {
        if (typeof ctgId.getCtgObject() === 'undefined') {
            $location.path('/');
            return;
        }
        if (ctgId.getCtgObject().isAuthor) {
            $scope.quoteTitleText = "Quotes by " + ctgId.getCtgObject().name;
        } else {
            $scope.quoteTitleText = "Quotes for " + ctgId.getCtgObject().name;
        }
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
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this quote!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    ngProgress.start();
                    $http.delete('api/quote/' + $scope.quotes[index]._id.$id, {}
                    ).success(function (data, status, headers, config) {
                        ngProgress.complete();
                        if (data === "Successsfully Deleted!") {
                            $scope.quotes.splice(index, 1);
                            swal("Deleted!", "Your quote has been deleted.", "success");
                        }
                    }).error(function (data, status, headers, config) {
                        console.log(status);
                    });
                } else {
//                    swal("Cancelled", "Your imaginary file is safe :)", "error");
                    console.log('action cancelled');
                }
            });

        };

        $scope.getHome = function () {
            $location.path('/');
        };
    }]);