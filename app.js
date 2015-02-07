var express = require('express');

var app = express(),
  passport = require('passport'),
  session = require('express-session'),
  cookieParser = require('cookie-parser'),
  morgan = require('morgan'),
  cookieParser = require('cookie-parser'),
  bodyParser = require('body-parser'),
  session = require('express-session'),
  mongoose = require('mongoose'),
  uriUtil = require('mongodb-uri');

var options = {
  server: {
    socketOptions: {
      keepAlive: 1,
      connectTimeoutMS: 30000
    }
  },
  replset: {
    socketOptions: {
      keepAlive: 1,
      connectTimeoutMS: 30000
    }
  }
};
var mongodbUrl = require('./app/config/database.js').mongoDb;
var mongooseUri = uriUtil.formatMongoose(mongodbUrl);
mongoose.connect(mongooseUri, options);
var conn = mongoose.connection;

conn.on('error', console.error.bind(console, 'connection error:'));

conn.once('open', function() {
  console.log('database is open for access.');
});

app.set('port', process.env.PORT || 1337);
app.set('views', __dirname + '/app/views');
app.set('view engine', 'ejs');

app.use(express.static(__dirname + '/public'));
app.use(morgan('dev'));
app.use(cookieParser());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({
  extended: true
}));
app.use(session({
  secret: 'keyboard cat',
  resave: false,
  saveUninitialized: true
}));
app.use(passport.initialize());
app.use(passport.session());

require('./app/config/passport')(passport);

require('./app/routes')(app, passport);

app.listen(app.get('port'), function() {
  console.log('\nExpress server listening on port ' + app.get('port'));
});
