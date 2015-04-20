var gulp = require('gulp');

gulp.task('build', function() {
  gulp.start('js-compile', 'sass-compile');
});
