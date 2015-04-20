var gulp = require('gulp');
var jshint = require('gulp-jshint');

var config = require('../config');

gulp.task('jshint', function() {
  return gulp.src('../js/src/**/*.js')
    .pipe(jshint())
    .pipe(jshint.reporter('gulp-jshint-file-reporter', {
      filename: config.rootPath + '/jshint-report.txt'
    }));
});
