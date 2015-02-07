var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var User = new Schema({
  displayName: String,
  email: String,
  created: {type: Date, default: Date.now}
});

module.exports = mongoose.model('User', User);