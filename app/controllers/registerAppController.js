var path = require('path'),
  RegisteredApp = require('../models/registeredApp');
  
module.exports = {
  get: indexAction,
  create: createAction,
  update: updateAction,
  remove: removeAction
}

function indexAction(req, res) {
  RegisteredApp.find({}, function(err, applist) {
    if (err) return handleError(err);
    res.status(200).send(applist);
  });
}

function createAction(req, res) {
  RegisteredApp.create({
    'name': req.body.name,
    'app_url': req.body.app_url,
    'defaultAppAccess': req.body.defaultAppAccess,
    'defaultWriteAccess': req.body.defaultWriteAccess
  }, function(err, doc) {
    if (err) return handleError(err);
    res.status(201).send(doc);
  });
}

function updateAction(req, res, next) {
  var updateDoc = req.body;
  RegisteredApp.update({
    _id: req.params.id
  }, updateDoc, {
    upsert: true
  }, function(err, doc) {
    if (err) return handleError(err);
    res.status(200).send(updateDoc);
  });
}

function removeAction(req, res, next) {
  RegisteredApp.remove({
    _id: req.params.id
  }, function(err, doc) {
    if (err) return handleError(err);
    res.status(200).send({
      'message': 'Deleted Successfully'
    });
  });
}
