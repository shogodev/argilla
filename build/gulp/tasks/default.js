var gulp = require('gulp');
var color = require('gulp-color');

gulp.task('default', function () {
  console.log([
    '',
    color('   ДОСТУПНЫЕ КОМАНДЫ:', 'GREEN'),
    color('   ------------------', 'GREEN'),
    '',
    '|  ' + color('gulp sass', 'YELLOW') + '   компилирует scss',
    '|  ' + color('gulp js', 'YELLOW') + '     компилирует js',
    '|  ' + color('gulp build', 'YELLOW') + '  компилирует все',
    '|  ' + color('gulp watch', 'YELLOW') + '  запускает вотчер',
    '|',
    '|- для команд выше применим ключ ' + color('--debug', 'GREEN')
    + ' для сборки без минификации',
    '',
    '   ДОПОЛНИТЕЛЬНО:',
    '',
    '|  ' + color('gulp jshint', 'YELLOW') + ' запускает jshint',
    '|',
    '|- с ключом ' + color('--common', 'GREEN') + ' проверяет только common.js',
    ''
  ].join('\n'));
});