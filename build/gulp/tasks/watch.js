var gulp = require('gulp');

var config = require('../config');

gulp.task('watch', function() {
  gulp.start('js-compile', 'sass-compile');

  gulp.watch(config.js.src, ['js-compile']);
  gulp.watch(config.sass.src, ['sass-compile']);
});
