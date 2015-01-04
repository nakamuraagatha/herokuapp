var myApp = angular.module('myApp', []);
myApp.config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});

myApp.controller('AppCtrl', ['$scope', '$window', '$http', function ($scope, $window, $http) {
    var tokenEle = document.getElementById('token');
    var token = tokenEle.innerHTML;
    tokenEle.innerHTML = "";
    if (token === 'empty' && $window.sessionStorage.token === 'undefined') {
        window.location = '/login';
    } else if (token === 'empty' && $window.sessionStorage.token) {
        getMyApps();
    } else if (token !== 'empty') {
        $window.sessionStorage.token = token;
        getMyApps();
    }

    function getMyApps() {
        $http.defaults.headers.common.Authorization = $window.sessionStorage.token;
        $http.get('/getMyApps').success(function (data, status, headers, config) {
            document.getElementsByTagName('title')[0].innerText = 'Welcome ! ' + data.name;
            $scope.appList = data.appList;
        }).error(function (data, status, headers, config) {
            //window.location = "/login"
        });
    }
}]);