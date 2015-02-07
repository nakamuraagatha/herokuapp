angular.module('myApp').controller('UsersPermissionsController', ['$scope', '$http', 'ngProgress', 'ApiService', function($scope, $http, ngProgress, ApiService) {

  var failure = function(reason) {
    console.log('reason');
  };

  ApiService.get('/api/usersList').then(function(data) {
    $scope.users = data;
    $scope.checkId = [];
    $scope.check = {};
    angular.forEach($scope.users, function(value, key) {
      $scope.check[value.email] = [false, false];
    });
  }, failure);

  ApiService.get('/api/registeredApp').then(function(data) {
    $scope.apps = [];
    angular.forEach(data, function(value, key) {
      this.push(value);
    }, $scope.apps);
  }, failure);

  $scope.change = function(appSelcted, email) {
    var appName = null !== appSelcted ? appSelcted.name : false;
    if (appName) {
      ApiService.get('api/appPermissions/' + appName + '/' + email).then(function(data) {
        $scope.check[email] = data;
      }, failure);
    }
  };

  $scope.updateChange = function(appSelcted, email) {
    var appName = typeof appSelcted !== 'undefined' ? appSelcted.name : false;
    if (appName) {

      ApiService.post('api/appPermissions/' + appName + '/' + email, [$scope.check[email][0], $scope.check[email][1]]).then(function(data) {
        $scope.check[email] = data;
      }, failure);
    }
  };
}]);
