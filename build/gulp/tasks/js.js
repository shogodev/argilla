// es6 polyfill
require('babel-polyfill');

var gulp = require('gulp');
var plumber = require('gulp-plumber');
var gulpif = require('gulp-if');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var maps = require('gulp-sourcemaps');
var argv = require('yargs').argv;
var del = require('del');
var browserify = require('gulp-browserify');

var config = require('../config');

gulp.task('js', function() {
  return gulp.src(config.js.src)
    .pipe(plumber())
    .pipe(maps.init())
    .pipe(gulpif(!argv['vendor'], browserify({ transform: ['babelify'] })))
    .pipe(gulpif(!argv.debug, uglify()))
    .pipe(gulpif(argv['vendor'], concat('vendor.js')))
    .pipe(maps.write('.'))
    .pipe(gulp.dest(config.js.dest));
});
