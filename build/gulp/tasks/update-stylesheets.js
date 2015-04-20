var gulp   = require('gulp');
var shell  = require('gulp-shell');

var config = require('../config');

gulp.task('update-stylesheets', shell.task([
  'cd ' + config.rootPath + 'i/&& touch style'
]));
