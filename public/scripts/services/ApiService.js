angular.module('myApp').factory('ApiService', ['$http', '$q', 'ngProgress', function($http, $q, ngProgress) {

  function service(apiMethod, apiUrl, requestData) {
    ngProgress.start();
    var deferred = $q.defer();
    deferred.notify('Calling Service...');
    var req = {
      method: apiMethod,
      url: apiUrl,
      headers: {
        'Content-Type': 'application/json'
      },
      data: requestData,
    }
    $http(req).
    success(function(data, status, headers, config) {
      ngProgress.complete();
      deferred.resolve(data);
    }).
    error(function(data, status, headers, config) {
      ngProgress.complete();
      deferred.reject(data);
    });

    return deferred.promise;
  }

  function get(apiUrl) {
    return service('GET', apiUrl, null)
  }

  function post(apiUrl, requestData) {
    return service('POST', apiUrl, requestData)
  }

  function put(apiUrl, requestData) {
    return service('PUT', apiUrl, requestData)
  }

  function remove(apiUrl) {
    return service('DELETE', apiUrl, null);
  }

  return {
    get: get,
    post: post,
    put: put,
    remove: remove
  };

}]);
