var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var Permission = new Schema({
  'email': {
    type: String,
    min: 10,
    max: 30
  },
  'appName': {
    type: String,
    min: 4,
    max: 30
  },
  'read': Boolean,
  'write': Boolean
});

module.exports = mongoose.model('Permission', Permission);
