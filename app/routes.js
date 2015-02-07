var auth = require('./controllers/authController'),
  MW = require('./middleWare'),
  registerApp = require('./controllers/registerAppController'),
  usersPermissions = require('./controllers/userPermissionsController'),
  quotesApp = require('./controllers/quotesAppController'),
  config = require('./config/common');

module.exports = function(app, passport) {

  // authentication
  app.get('/', MW.isLoggedIn, auth.index);
  app.get('/login', auth.login);
  app.get('/logout', auth.logout);

  // homepage
  app.get('/api/myApps', MW.isLoggedIn, auth.home);

  // Oauth login
  app.get('/auth/facebook', passport.authenticate('facebook', config.facebookScope));
  app.get('/auth/facebook/callback', passport.authenticate('facebook', config.redirects));
  app.get('/auth/google', passport.authenticate('google', config.googleScope));
  app.get('/auth/google/callback', passport.authenticate('google', config.redirects));

  // verify write permission
  app.get('/api/writeAccess/:appName', MW.isLoggedIn, auth.hasWriteAccess);

  // applist
  app.get('/api/registeredApp', MW.hasAccess('appList+R'), registerApp.get);
  app.post('/api/registeredApp', MW.hasAccess('appList+W'), registerApp.create);
  app.put('/api/registeredApp/:id', MW.hasAccess('appList+W'), registerApp.update);
  app.delete('/api/registeredApp/:id', MW.hasAccess('appList+W'), registerApp.remove);

  // Users
  app.get('/api/usersList', MW.hasAccess('usersList+R'), usersPermissions.getUsers);
  app.get('/api/appPermissions/:appName/:email', MW.hasAccess('usersList+R'), usersPermissions.getPermission);
  app.post('/api/appPermissions/:appName/:email', MW.hasAccess('usersList+W'), usersPermissions.setPermission);

  // quotes app
  app.get('/api/category', MW.hasAccess('quotesList+R'), quotesApp.getCtg);
  app.post('/api/category', MW.hasAccess('quotesList+W'), quotesApp.createCtg);
  app.put('/api/category/:id', MW.hasAccess('quotesList+W'), quotesApp.updateCtg);
  app.delete('/api/category/:id', MW.hasAccess('quotesList+W'), quotesApp.removeCtg);
  app.get('/api/quote/:ctg', MW.hasAccess('quotesList+R'), quotesApp.getQuotes);
  app.post('/api/quote', MW.hasAccess('quotesList+W'), quotesApp.createQuote);
  app.put('/api/quote/:id', MW.hasAccess('quotesList+W'), quotesApp.updateQuote);
  app.delete('/api/quote/:id', MW.hasAccess('quotesList+W'), quotesApp.removeQuote);
};
