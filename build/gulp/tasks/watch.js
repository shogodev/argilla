var gulp = require('gulp');
var config = require('../config');

gulp.task('watch', function() {
  gulp.start('js', 'css');

  gulp.watch(config.js.src, ['js']);
  gulp.watch(config.css.src, ['css']);
});
