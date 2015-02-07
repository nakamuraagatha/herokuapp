var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var CategoryModel = new Schema({
  'name': {
    type: String,
    min: 4,
    max: 30
  },
  'type': {
    type: String,
    min: 4,
    max: 10
  }
});

module.exports = mongoose.model('ctgModel', CategoryModel);
