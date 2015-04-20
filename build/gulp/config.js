var path = require('path');

var rootPath = path.join(__dirname, '../../');
var sassTempDir = '~sasstemp_' + new Date().getTime();

module.exports = {
  rootPath: rootPath,

  js: {
    dest: rootPath + '/js',
    src: [
      rootPath + '/js/src/vendor/jquery.js',
      rootPath + '/js/src/vendor/jquery-ui.js',
      rootPath + '/js/src/vendor/jqury_plugins/*.js',
      rootPath + '/js/src/vendor/jquery_ui_widgets/*.js',
      rootPath + '/js/src/vendor/**/*.js',
      rootPath + '/js/src/common.js'
    ]
  },

  sass: {
    src: rootPath + '/i/style/**/*.scss',
    dest: rootPath + '/i/style/css/',
    tempDir: sassTempDir,

    options: {
      style: 'compressed',
      cacheLocation: '/tmp/.sass-cache',
      container: sassTempDir,
      sourcemap: true
    }
  },

  base64: {
    options: {
      baseDir: '..',
      extensions: ['png', 'jpg'],
      maxImageSize: 32 * 1024
    }
  }
};
