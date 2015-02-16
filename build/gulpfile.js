var gulp   = require('gulp');
var gulpif = require('gulp-if');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var jshint = require('gulp-jshint');
var maps   = require('gulp-sourcemaps');
var sass   = require('gulp-ruby-sass');
var shell  = require('gulp-shell');
var argv   = require('yargs').argv;
var del    = require('del');


gulp.task('jshint', function() {
  return gulp.src('../js/src/**/*.js')
    .pipe(jshint())
    .pipe(jshint.reporter('gulp-jshint-file-reporter', {
      filename: __dirname + '/jshint-report.txt'
    }));
});


gulp.task('sass-compile', function() {
  var sassCompileTempDir = '~sasstemp_' + new Date().getTime();
  var opts = {
    style: 'compressed',
    cacheLocation: '/tmp/.sass-cache',
    container: sassCompileTempDir,
    sourcemap: true
  };

  function errorHandler(err) {
    console.log(err);
  }

  function endHandler() {
    del('/tmp/' + sassCompileTempDir, { force : true });
    gulp.start('update-dir');
  }

  return gulp.src('../i/style/**/*.scss')
    .pipe(sass(opts))
      .on('error', errorHandler)
    .pipe(gulp.dest('../i/style/css/'))
      .on('end', endHandler);
});


gulp.task('js-compile', function() {
  del('../js/*.*', { force : true });

  var sources = [
    '../js/src/vendor/jquery.js',
    '../js/src/vendor/jquery-ui.js',
    '../js/src/vendor/jqury_plugins/*.js',
    '../js/src/vendor/jquery_ui_widgets/*.js',
    '../js/src/vendor/photoswipe.js',
    '../js/src/vendor/photoswipe-ui-default.js',
    '../js/src/vendor/**/*.js',
    '../js/src/**/*.js'
  ];

  function errorHandler(err) {
    console.log('Error: ', err.message, ' line number: ', err.lineNumber);
  }

  return gulp.src(sources)
    .pipe(maps.init())
    .pipe(gulpif(!argv.debug, uglify()))
      .on('error', errorHandler)
    .pipe(concat('compiled.js'))
    .pipe(maps.write('.'))
    .pipe(gulp.dest('../js'));
});


gulp.task('update-dir', shell.task([ 'cd ../i && touch style' ]));


gulp.task('build', function() {
  gulp.start('js-compile', 'sass-compile');
});


gulp.task('watch', function() {
  gulp.start('js-compile', 'sass-compile');

  gulp.watch('../js/src/**/*.js', ['js-compile']);
  gulp.watch('../i/style/**/*.scss', ['sass-compile']);
});
