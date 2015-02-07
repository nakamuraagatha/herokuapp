angular.module('myApp').controller('CategoryController', ['$scope', '$http', '$location', 'ctgId', '$rootScope', 'ApiService',
  function($scope, $http, $location, ctgId, $rootScope, ApiService) {

    // controller settings, intializations
    var writeAccessInfoUrl = '/api/writeAccess/quotesList/';
    var ctgEndPoint = '/api/category/';

    function failure(reason) {
      console.log(reason);
    }

    // check if category or author for navigation
    if (typeof ctgId.getCtgObject() !== 'undefined') {
      if (ctgId.getCtgObject().isAuthor) {
        $scope.showAuthors = true;
      }
    }

    // Get all categories
    var promise = ApiService.get(writeAccessInfoUrl).then(function(data) {
      ctgId.hasWriteAccess = data.permission;
      $scope.hasWriteAccess = ctgId.hasWriteAccess;
    }, failure);

    promise.then(function() {

      ApiService.get(ctgEndPoint).then(function(data) {
        $scope.ctgs = $.map(data, function(val) {
          if (val.type === "ctg")
            return val;
        });
        $scope.authors = $.map(data, function(val) {
          if (val.type === "author")
            return val;
        });
      }, failure);

    }, failure);


    $scope.createCategory = function() {
      var ctgType = $scope.showAuthors ? "author" : "ctg";

      ApiService.post(ctgEndPoint, {
        'name': $scope.newCtgText,
        'type': ctgType
      }).then(function(data) {
        if (ctgType === "ctg") {
          $scope.ctgs.push(data);
        } else {
          $scope.authors.push(data);
        }
        $scope.showCreate = false;
        $scope.newCtgText = "";
      }, failure);
    };

    $scope.saveCategory = function(index) {
      var id, request;
      if ($scope.showAuthors) {
        id = $scope.authors[index]._id;
        request = {
          'name': $scope.authors[index].name,
          'type': $scope.authors[index].type
        };
      } else {
        id = $scope.ctgs[index]._id;
        request = {
          'name': $scope.ctgs[index].name,
          'type': $scope.ctgs[index].type
        };
      }

      ApiService.put(ctgEndPoint + id, request).then(function(data) {
        if ($scope.showAuthors) {
          data._id = $scope.authors[index]._id;
          $scope.authors[index] = data;
          $scope.saving_author[index] = false;
        } else {
          data._id = $scope.ctgs[index]._id;
          $scope.ctgs[index] = data;
          $scope.saving[index] = false;
        }
      }, failure);
    }


    $scope.saving = [];
    $scope.saving_author = [];
    $scope.editCategory = function(index) {
      if ($scope.showAuthors) {
        $scope.saving_author[index] = true;
      } else {
        $scope.saving[index] = true;
      }
    };

    $scope.deleteCategory = function(index) {
      var id;
      if ($scope.showAuthors) {
        id = $scope.authors[index]._id;
      } else {
        id = $scope.ctgs[index]._id;
      }

      ApiService.remove(ctgEndPoint + id).then(function(data) {
        if (data.message === "Deleted Successfully") {
          if ($scope.showAuthors) {
            $scope.authors.splice(index, 1);
          } else {
            $scope.ctgs.splice(index, 1);
          }
        }
      }, failure);

    };

    $scope.showQuotes = function(index) {
      if ($scope.showAuthors) {
        ctgId.setCtgId($scope.authors[index]._id);
        ctgId.setCtgObject({
          name: $scope.authors[index].name,
          isAuthor: true
        });
      } else {
        ctgId.setCtgId($scope.ctgs[index]._id);
        ctgId.setCtgObject({
          name: $scope.ctgs[index].name,
          isAuthor: false
        });
      }
      $location.path('/quotesList');
    };

  }
]);
