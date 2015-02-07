var FacebookStrategy = require('passport-facebook').Strategy,
  GoogleStrategy = require('passport-google-oauth').OAuth2Strategy,
  User = require('../models/user');

var oauthConfigs = {
  fbClientId: process.env.FBCLIENTID ? process.env.FBCLIENTID : '751436901630601',
  fbClientSecret: process.env.FBCLIENTSECRET ? process.env.FBCLIENTSECRET : '3953b5a94d39a9c142383d559dac36b6',
  fbClientCallback: process.env.FBCLIENTCALLBACK ? process.env.FBCLIENTCALLBACK : 'http://localhost:1337/auth/facebook/callback',
  gplusClientId: process.env.GPLUSCLIENTID ? process.env.GPLUSCLIENTID : '895592031689-66cq90di9ui9ki6sbeegnsoc6f6nupvs.apps.googleusercontent.com',
  gplusClientSecret: process.env.GPLUSCLIENTSECRET ? process.env.GPLUSCLIENTSECRET : 'XpJq_ugvxxoroFkoHbxMxEVW',
  gplusClientCallback: process.env.GPLUSCLIENTCALLBACK ? process.env.GPLUSCLIENTCALLBACK : 'http://localhost:1337/auth/google/callback'
};

module.exports = function(passport) {

  passport.use(new FacebookStrategy({
      clientID: oauthConfigs.fbClientId,
      clientSecret: oauthConfigs.fbClientSecret,
      callbackURL: oauthConfigs.fbClientCallback
    },
    function(accessToken, refreshToken, profile, done) {
      User.findOne({
        email: profile.emails[0].value
      }, function(err, user) {
        if (err) {
          console.log(err);
        }
        if (!err && user != null) {
          done(null, user);
        } else {
          var user = new User({
            email: profile.emails[0].value,
            displayName: profile.displayName,
            created: Date.now()
          });
          user.save(function(err) {
            if (err) {
              console.log(err);
            } else {
              done(null, user);
            };
          });
        };
      });
    }
  ));

  passport.use(new GoogleStrategy({
      clientID: oauthConfigs.gplusClientId,
      clientSecret: oauthConfigs.gplusClientSecret,
      callbackURL: oauthConfigs.gplusClientCallback
    },
    function(accessToken, refreshToken, profile, done) {
      User.findOne({
        email: profile.emails[0].value
      }, function(err, user) {
        if (err) {
          console.log(err);
        }
        if (!err && user != null) {
          done(null, user);
        } else {
          var user = new User({
            email: profile.emails[0].value,
            displayName: profile.displayName,
            created: Date.now()
          });
          user.save(function(err) {
            if (err) {
              console.log(err);
            } else {
              done(null, user);
            };
          });
        };
      });
    }
  ));

  passport.serializeUser(function(user, done) {
    done(null, user._id);
  });

  passport.deserializeUser(function(id, done) {
    User.findById(id, function(err, user) {
      if (!err)
        done(null, user);
      else
        done(err, null);
    });
  });

};
