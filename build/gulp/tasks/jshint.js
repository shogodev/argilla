var gulp = require('gulp');
var jshint = require('gulp-jshint');
var argv = require('yargs').argv;

var config = require('../config');

var jsSrc = argv.common ? '../js/src/common.js' : '../js/src/**/*.js';

gulp.task('jshint', function() {
  return gulp.src(jsSrc)
    .pipe(jshint())
    .pipe(jshint.reporter('gulp-jshint-file-reporter', {
      filename: config.rootPath + '/jshint-report.txt'
    }));
});
