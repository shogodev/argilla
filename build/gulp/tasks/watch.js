var gulp = require('gulp');
var config = require('../config');

gulp.task('watch', function() {
  gulp.start('js', 'sass');

  gulp.watch(config.js.src, ['js']);
  gulp.watch(config.sass.src, ['sass']);
});
