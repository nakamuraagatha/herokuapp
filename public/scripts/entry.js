angular.module('myApp', ['ngRoute', 'ngProgress']);

angular.module('myApp').config(['$provide', '$routeProvider', function($provide, $routeProvider) {
  $provide.factory('$routeProvider', function() {
    return $routeProvider;
  });
}]).run(['$routeProvider', '$http', '$route', function($routeProvider, $http, $route) {
  $http.get('scripts/routes.json').success(function(routeList) {
    angular.forEach(routeList, function(item) {
      $routeProvider.when(item.url, {
        templateUrl: item.template,
        controller: item.controller
      });
    });
    $route.reload();
  });
}]);