angular.module('myApp').controller('QuotesController', ['$scope', 'ctgId', '$http', 'ngProgress', '$location', 'ApiService',
  function($scope, ctgId, $http, ngProgress, $location, ApiService) {

    // controller level instantiation
    var quotesEndPoint = '/api/quote/';
    var failure = function(reason) {
      console.log(reason);
    }
    $scope.hasWriteAccess = ctgId.hasWriteAccess;
    if (typeof ctgId.getCtgObject() === 'undefined') {
      $location.path('/categories');
      return;
    }
    if (ctgId.getCtgObject().isAuthor) {
      $scope.quoteTitleText = "Quotes by " + ctgId.getCtgObject().name;
      $scope.isAuthor = true;
      $scope.newQuoteAuthor = ctgId.getCtgObject().name;
    } else {
      $scope.quoteTitleText = "Quotes on " + ctgId.getCtgObject().name;
    }

    ApiService.get(quotesEndPoint + ctgId.getCtgId()).then(function(data) {
      $scope.quotes = [];
      angular.forEach(data, function(value, key) {
        this.push(value);
      }, $scope.quotes);
    }, failure);

    $scope.createQuote = function() {
      ApiService.post(quotesEndPoint, {
        'text': $scope.newQuoteText,
        'author': $scope.newQuoteAuthor,
        'ctg_id': ctgId.getCtgId()
      }).then(function(data) {
        $scope.quotes.push(data);
        $scope.showCreate = false;
        $scope.newQuoteText = "";
        if (ctgId.getCtgObject().isAuthor) {
          $scope.newQuoteAuthor = ctgId.getCtgObject().name;
        } else {
          $scope.newQuoteAuthor = "";
        }
      }, failure);
    };

    $scope.saveQuote = function(index) {
      ApiService.put(quotesEndPoint + $scope.quotes[index]._id, {
        'text': $scope.quotes[index].text,
        'author': $scope.quotes[index].author,
        'ctg_id': ctgId.getCtgId()
      }).then(function(data) {
        data._id = {
          $id: $scope.quotes[index]._id
        };
        $scope.quotes[index] = data;
        $scope.saving[index] = false;
      }, failure);
    };

    $scope.saving = [];
    $scope.editQuote = function(index) {
      $scope.saving[index] = true;
    };

    $scope.deleteQuote = function(index) {
      ApiService.remove(quotesEndPoint + $scope.quotes[index]._id).then(function(data) {
        if (data.message === "Deleted Successfully") {
          $scope.quotes.splice(index, 1);
        }
      }, failure);
    };
  }
]);
