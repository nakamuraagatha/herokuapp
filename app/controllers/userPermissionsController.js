var path = require('path'),
  Permission = require('../models/permissions'),
  RegisteredApp = require('../models/registeredApp'),
  User = require('../models/user');

module.exports = {
  getUsers: getUsersAction,
  getPermission: getPermissionAction,
  setPermission: setPermissionAction,
}

function getUsersAction(req, res) {

  User.find({}, function(err, userList) {
    if (err) return handleError(err);
    res.status(200).send(userList);
  });
}

function getPermissionAction(req, res) {
  RegisteredApp.findOne({
    'name': req.params.appName
  }, function(err, appDefault) {
    if (err) return handleError(err);

    Permission.findOne({
      'appName': req.params.appName,
      'email': req.params.email
    }, function(err, appPermission) {
      if (err) return handleError(err);

      if (appPermission === null) {
        res.status(200).send([appDefault.defaultAppAccess, appDefault.defaultWriteAccess]);
      } else {
        res.status(200).send([appPermission.read, appPermission.write]);
      }

    });

  });

}

function setPermissionAction(req, res, next) {

  var requestData = req.body[0] === false ? [false, false] : req.body,
    updateDoc = {
      'read': requestData[0],
      'write': requestData[1]
    },
    findCombo = {
      'appName': req.params.appName,
      'email': req.params.email
    };

  Permission.findOne(findCombo, function(err, appPermission) {
    if (err) return handleError(err);

    if (appPermission === null) {
      Permission.create({
        'appName': req.params.appName,
        'email': req.params.email,
        'read': requestData[0],
        'write': requestData[1]
      }, function(err, doc) {
        if (err) return handleError(err);

        res.status(201).send(requestData);
      });
    } else {
      Permission.update(findCombo, updateDoc, {
        upsert: true
      }, function(err, doc) {
        if (err) return handleError(err);

        res.status(200).send(requestData);
      });
    }

  });
}
