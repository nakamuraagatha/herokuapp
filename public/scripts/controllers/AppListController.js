angular.module('myApp').controller('AppListController', ['$scope', 'ApiService', function($scope, ApiService) {
  var serviceEndPoint = '/api/registeredApp/';

  function failure(reason) {
    console.log(reason);
    $scope.newApp.app_url = '';
    $scope.newApp.name = '';
  }

  function getIndex(id) {
    var len = $scope.registeredApps.length;
    for (var i = 0; i < len; i++) {
      if ($scope.registeredApps[i]._id === id) {
        return i;
      }
    }
  }

  // controller logic

  ApiService.get(serviceEndPoint).then(function(data) {
    $scope.registeredApps = data;
  }, failure);

  $scope.createApp = function(newApp) {
    ApiService.post(serviceEndPoint, newApp).then(function(data) {
      $scope.registeredApps.push(data);
      $scope.newApp.app_url = '';
      $scope.newApp.name = '';
    }, failure);
  };

  $scope.prepareForEdit = function(id) {
    $scope.edit = true;
    $scope.newApp = $scope.registeredApps[getIndex(id)];
  };

  $scope.updateApp = function(app) {
    ApiService.put(serviceEndPoint + app._id, app).then(function(data) {
      $scope.registeredApps[getIndex(app._id)] = data;
      $scope.newApp.app_url = '';
      $scope.newApp.name = '';
      $scope.edit = false;
    }, failure);
  };

  $scope.removeApp = function(id) {
    ApiService.remove(serviceEndPoint + id).then(function(data) {
      $scope.registeredApps.splice(getIndex(id), 1);
    }, failure);
  };



}]);
