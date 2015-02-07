var path = require('path'),
  QuotesModel = require('../models/quotes'),
  CtgModel = require('../models/categories');

module.exports = {
  getCtg: getCtgAction,
  createCtg: createCtgAction,
  updateCtg: updateCtgAction,
  removeCtg: removeCtgAction,
  getQuotes: getQuotesAction,
  createQuote: createQuotesAction,
  updateQuote: updateQuotesAction,
  removeQuote: removeQuotesAction
}

function getCtgAction(req, res) {
  CtgModel.find({}, function(err, categoryList) {
    if (err) return handleError(err);
    
    res.status(200).send(categoryList);
  });
}

function createCtgAction(req, res) {
  CtgModel.create({
    'name': req.body.name,
    'type': req.body.type
  }, function(err, doc) {
    if (err) return handleError(err);

    res.status(201).send(doc);
  });
}

function updateCtgAction(req, res, next) {
  var updateDoc = req.body;
  CtgModel.update({
    _id: req.params.id
  }, updateDoc, {
    upsert: true
  }, function(err, doc) {
    if (err) return handleError(err);

    res.status(200).send(updateDoc);
  });
}

function removeCtgAction(req, res, next) {
  CtgModel.remove({
    _id: req.params.id
  }, function(err, doc) {
    if (err) return handleError(err);

    res.status(200).send({
      'message': 'Deleted Successfully'
    });
  });
}

function getQuotesAction(req, res) {
  QuotesModel.find({
    'ctg_id': req.params.ctg
  }, function(err, quoteList) {
    if (err) return handleError(err);

    res.status(200).send(quoteList);
  });
}

function createQuotesAction(req, res) {
  QuotesModel.create({
    'text': req.body.text,
    'author': req.body.author,
    'ctg_id': req.body.ctg_id
  }, function(err, doc) {
    if (err) return handleError(err);

    res.status(201).send(doc);
  });
}

function updateQuotesAction(req, res, next) {
  var updateDoc = req.body;
  QuotesModel.update({
    _id: req.params.id
  }, updateDoc, {
    upsert: true
  }, function(err, doc) {
    if (err) return handleError(err);

    res.status(200).send(updateDoc);
  });
}

function removeQuotesAction(req, res, next) {
  QuotesModel.remove({
    _id: req.params.id
  }, function(err, doc) {
    if (err) return handleError(err);
    res.status(200).send({
      'message': 'Deleted Successfully'
    });
  });
}
