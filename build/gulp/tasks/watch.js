var gulp = require('gulp');
var config = require('../config');

gulp.task('watch', function() {
  gulp.start('js', 'css');

  gulp.watch(config.js.watchSrc, ['js']);
  gulp.watch(config.css.watchSrc, ['css']);
});
