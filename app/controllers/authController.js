var path = require('path');

console.log(path);
var RegisteredApp = require('./../models/registeredApp'),
  Permission = require('./../models/permissions');

module.exports = {
  index: indexAction,
  login: loginAction,
  logout: logoutAction,
  home: homeAction,
  hasWriteAccess: hasWriteAccessAction
}

function indexAction(req, res) {
  res.render('home.ejs');
}

function loginAction(req, res) {
  res.render('login.ejs');
}

function logoutAction(req, res, next) {
  req.logout();
  res.redirect('/login');
}

function hasWriteAccessAction(req, res, next) {

  RegisteredApp.findOne({
    'name': req.params.appName
  }, function(err, appDefault) {
    if (err) return handleError(err);

    Permission.findOne({
      'email': req.user.email,
      'appName': req.params.appName
    }, function(err, appPermission) {
      if (err) return handleError(err);

      if (appPermission !== null) {
        res.status(200).send({
          'permission': appPermission.write
        });
      } else if (appDefault !== null) {
        res.status(200).send({
          'permission': appDefault.defaultWriteAccess
        });
      } else {
        res.status(400).send({
          'message': 'Error!'
        });
      }

    });
  });

}

function homeAction(req, res, next) {
  RegisteredApp.find({}, function(err, applist) {
    if (err) return handleError(err);

    Permission.find({
      'email': req.user.email
    }, function(err, appPermission) {
      if (err) return handleError(err);

      var allowed = [];
      for (var i = 0; i < applist.length; i++) {
        var found = false,
          appName = '',
          read = false;
        for (var j = 0; j < appPermission.length; j++) {
          if (applist[i].name === appPermission[j].appName) {
            found = true;
            appName = appPermission[j].appName;
            read = appPermission[j].read;
            break;
          }
        }
        if (found && read) {
          allowed.push(applist[i]);
        } else if (!found && applist[i].defaultAppAccess) {
          allowed.push(applist[i]);
        }
      }
      res.status(200).send(allowed);
    });

  });
}
