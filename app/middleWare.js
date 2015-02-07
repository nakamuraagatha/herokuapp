var path = require("path"),
  RegisteredApp = require('./models/registeredApp'),
  Permission = require('./models/permissions');

module.exports = {
  isLoggedIn: isLoggedIn,
  hasAccess: hasAccess
};

function isLoggedIn(req, res, next) {
  if (req.isAuthenticated())
    return next();

  res.redirect('/login');
}

function hasAccess(appName) {
  return hasAccess[appName] || (hasAccess[appName] = function(req, res, next) {
    var permissionRequest = appName.split('+');

    RegisteredApp.findOne({
      'name': permissionRequest[0]
    }, function(err, appDefault) {
      if (err) return handleError(err);

      Permission.findOne({
        'appName': permissionRequest[0],
        'email': req.user.email
      }, function(err, appPermission) {
        if (err) return handleError(err);

        if (appPermission === null && appDefault !== null) {
          if (permissionRequest[1] === 'R' && appDefault.defaultAppAccess) {
            return next();
          } else if (permissionRequest[1] === 'W' && appDefault.defaultWriteAccess) {
            return next();
          }
          res.status(400).send({
            'message': 'Unauthorized Access!'
          });
        } else if (appPermission !== null) {
          if (permissionRequest[1] === 'R' && appPermission.read) {
            return next();
          } else if (permissionRequest[1] === 'W' && appPermission.write) {
            return next();
          }
          res.status(400).send({
            'message': 'Unauthorized Access!'
          });
        } else {
          res.status(400).send({
            'message': 'Unauthorized Access!'
          });
        }

      });

    });

  })
}
