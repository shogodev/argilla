require('babel-polyfill');
require('shelljs/global');

var gulp = require('gulp');
var plumber = require('gulp-plumber');
var gulpif = require('gulp-if');
var argv = require('yargs').argv;
var stylus = require('gulp-stylus');
var postcss = require('gulp-postcss');
var autoprefixer = require('autoprefixer');
var assets = require('postcss-assets');
var base64 = require('gulp-base64');
var sourcemaps = require('gulp-sourcemaps');

var config = require('../config');

gulp.task('css', function() {
  function endHandler() {
    exec('cd ../i/ && touch style');
  }

  return gulp.src(config.css.src)
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(stylus({
      compress: !argv.debug
    }))
    .pipe(postcss(
      [
        autoprefixer(config.autoprefixer),
        assets(config.assets)
      ]
    ))
    .pipe(gulpif(!argv.debug, base64(config.base64.options)))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(config.css.dest))
      .on('end', endHandler);
});