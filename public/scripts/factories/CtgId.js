angular.module('myApp').factory('ctgId', [function() {
  var ctgId, ctgObject, hasWriteAccess;

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
    setCtgObject: setCtgObject,
    hasWriteAccess: hasWriteAccess
  };
}])
