var gulp = require('gulp'),
  less = require('gulp-less'),
  concat = require('gulp-concat'),
  uglify = require('gulp-uglify'),
  ngmin = require('gulp-ngmin'),
  sourcemaps = require('gulp-sourcemaps'),
  htmlreplace = require('gulp-html-replace'),
  watch = require('gulp-watch'),
  prefix = require('gulp-autoprefixer'),
  plumber = require('gulp-plumber'),
  path = require('path'),
  minifyCSS = require('gulp-minify-css'),
  rev = require('gulp-rev');

var getBundleName = function() {
  var version = require('./package.json').version;
  var name = require('./package.json').name;
  return name + '.' + 'min';
};

gulp.task('scripts', function() {
  return gulp.src(['public/scripts/entry.js', 'public/scripts/controllers/**/*.js',
      'public/scripts/directives/**/*.js', 'public/scripts/factories/**/*.js', 'public/scripts/services/**/*.js', 
      'public/scripts/filters/**/*.js'
    ])
    .pipe(concat(getBundleName() + '.js'))
    // .pipe(sourcemaps.init())
    // .pipe(ngmin())
    // .pipe(uglify())
    // .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('public/scripts'));
});

gulp.task('less', function() {
  return gulp.src('./public/less/style.less')
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(less({
      paths: ['./', './overrides/'],
      compress: true
    }))
    .pipe(prefix('last 10 versions', 'ie 9'), {
      cascade: true
    })
    .pipe(minifyCSS({keepBreaks: false}))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('./public/css'));
});

gulp.task('watch', ['less', 'scripts'], function() {
  gulp.watch('./public/less/**/*.less', ['less']);
  gulp.watch('./public/scripts/**/*.js', ['scripts']);
});

gulp.task('rev', ['less', 'scripts'], function() {
  return gulp.src(['./css/*.css', 'js/*min.js'])
    .pipe(rev())
    .pipe(gulp.dest('dist'))
    .pipe(rev.manifest())
    .pipe(gulp.dest('dist'));
});

/*
db.xxx.insert({'email': 'xxx@abc.com', 'appName': 'appList', 'read': true, 'write': true})
db.xxx.insert({'email': 'xxx@abc.com', 'appName': 'usersList', 'read': true, 'write': true})
*/