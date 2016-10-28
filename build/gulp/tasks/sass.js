var gulp = require('gulp');
var plumber = require('gulp-plumber');
var del = require('del');
var sass = require('gulp-ruby-sass');
var postcss = require('gulp-postcss');
var autoprefixer = require('autoprefixer');
var base64 = require('gulp-base64');
require('shelljs/global');
// var sourcemaps = require('gulp-sourcemaps');

// es-6 promise for autoprefixer
require('es6-promise').polyfill();

var config = require('../config');

gulp.task('sass', function() {
  function endHandler() {
    del('/tmp/' + config.sass.tempDir, { force: true });
    exec('cd ../i/ && touch style');
  }

  return gulp.src(config.sass.src)
    .pipe(plumber())
    // .pipe(sourcemaps.init())
    .pipe(sass(config.sass.options))
    .pipe(postcss([ autoprefixer(config.autoprefixer) ]))
    .pipe(base64(config.base64.options))
    // .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(config.sass.dest))
      .on('end', endHandler);
});
