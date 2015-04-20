var gulp = require('gulp');
var gulpif = require('gulp-if');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var maps = require('gulp-sourcemaps');
var argv = require('yargs').argv;
var del = require('del');

var config = require('../config');

gulp.task('js-compile', function() {
  del(config.js.dest + '/*.*', { force : true });

  function errorHandler(err) {
    console.log('Error: ', err.message, ' line number: ', err.lineNumber);
  }

  return gulp.src(config.js.src)
    .pipe(maps.init())
    .pipe(gulpif(!argv.debug, uglify()))
      .on('error', errorHandler)
    .pipe(concat('compiled.js'))
    .pipe(maps.write('.'))
    .pipe(gulp.dest(config.js.dest));
});
