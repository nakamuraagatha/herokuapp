var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var Quotes = new Schema({
  'text': {
    type: String,
    min: 10,
    max: 1000
  },
  'author': {
    type: String,
    min: 4,
    max: 30
  },
  'ctg_id': {
    type: String
  }
});

module.exports = mongoose.model('quotesModel', Quotes);
