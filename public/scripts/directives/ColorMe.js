angular.module('myApp').directive('colorMe', [function() {
  function link(scope, element, attrs) {
    var colors = ["#000000", "#795548", "#4CAF50", "#009688", "#673AB7", "#FF5722", "#F44336"];
    var randomColor = colors[Math.floor(Math.random() * colors.length)];
    element.css('color', randomColor);
  }
  return {
    link: link
  };
}])
