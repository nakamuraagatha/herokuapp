var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var RegisteredApp = new Schema({
  'name': { type: String, min: 4, max: 20 },
  'app_url': String,
  'defaultAppAccess': Boolean,
  'defaultWriteAccess': Boolean
});

module.exports = mongoose.model('RegisteredApp', RegisteredApp);