var gulp = require('gulp');
var gulpif = require('gulp-if');
var sass = require('gulp-ruby-sass');
var base64 = require('gulp-base64');
var argv = require('yargs').argv;
var del = require('del');

var config = require('../config');

gulp.task('sass-compile', function() {
  function errorHandler(err) {
    console.log(err);
  }

  function endHandler() {
    del('/tmp/' + config.sass.tempDir, { force: true });
    gulp.start('update-stylesheets');
  }

  return gulp.src(config.sass.src)
    .pipe(sass(config.sass.options))
      .on('error', errorHandler)
    .pipe(gulpif(!argv.nobase64, base64(config.base64.options)))
    .pipe(gulp.dest(config.sass.dest))
      .on('end', endHandler);
});
