var dbuser = 'ajay4067',
  dbpassword = 'Infosys@123';

module.exports = {
  mongoDb: process.env.MONGODBURL ? process.env.MONGODBURL : 'mongodb://localhost:27017/beginner'
}
