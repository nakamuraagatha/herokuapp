angular.module('myApp').controller('HomeController', ['$scope', 'ApiService', function($scope, ApiService) {
  ApiService.get('/api/myApps').then(function(data) {
    $scope.myApps = data;
  });
}]);
