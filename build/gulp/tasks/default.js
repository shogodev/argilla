var gulp = require('gulp');
var color = require('gulp-color');

gulp.task('default', function () {
  console.log([
    '',
    color('   ДОСТУПНЫЕ КОМАНДЫ:', 'GREEN'),
    color('   ------------------', 'GREEN'),
    '',
    color('|  ', 'WHITE') + color('gulp css', 'YELLOW') + color('    компилирует css', 'WHITE'),
    color('|  ', 'WHITE') + color('gulp js', 'YELLOW') + color('     компилирует js', 'WHITE'),
    color('|  ', 'WHITE') + color('gulp build', 'YELLOW') + color('  компилирует все', 'WHITE'),
    color('|  ', 'WHITE') + color('gulp watch', 'YELLOW') + color('  запускает вотчер', 'WHITE'),
    color('|', 'WHITE'),
    color('|- для команд выше применим ключ ', 'WHITE') + color('--debug', 'GREEN')
    + color(' для сборки без минификации', 'WHITE'),
    '',
    color('   ДОПОЛНИТЕЛЬНО:', 'WHITE'),
    '',
    color('|  ', 'WHITE') + color('gulp jshint', 'YELLOW') + color(' запускает jshint', 'WHITE'),
    color('|', 'WHITE'),
    color('|- с ключом ', 'WHITE') + color('--common', 'GREEN')
    + color(' проверяет только common.js', 'WHITE'),
    ''
  ].join('\n'));
});

gulp.on('task_not_found', function() {
  gulp.start('default');
});