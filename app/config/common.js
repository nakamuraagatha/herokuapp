module.exports = {
  googleScope: {
    scope: ['https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email']
  },
  facebookScope: {
    scope: ['email']
  },
  redirects: {
    successRedirect: '/',
    failureRedirect: '/login'
  }
}
