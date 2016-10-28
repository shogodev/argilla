var argv = require('yargs').argv;

var path = require('path');

var rootPath = path.join(__dirname, '../../');
var sassTempDir = '~sasstemp_' + new Date().getTime();

var es2015 = require('babel-preset-es2015-nostrict');

module.exports = {
  rootPath: rootPath,

  js: {
    dest: rootPath + 'js',
    src: [
      rootPath + 'js/src/vendor/jquery.js',
      rootPath + 'js/src/vendor/jquery-ui.js',
      rootPath + 'js/src/vendor/jqury_plugins/*.js',
      rootPath + 'js/src/vendor/jquery_ui_widgets/*.js',
      rootPath + 'js/src/vendor/**/*.js',
      rootPath + 'js/src/common.js'
    ]
  },

  babel: {
    presets: [es2015],
    compact: false
  },

  sass: {
    src: rootPath + 'i/style/**/*.scss',
    dest: rootPath + 'i/style/css/',
    tempDir: sassTempDir,

    options: {
      style: argv.debug ? 'expanded' : 'compressed',
      lineNumbers: argv.debug,
      cacheLocation: rootPath + 'build/tmp/.sass-cache',
      container: sassTempDir
      // sourcemap: true
    }
  },

  imagemin: {
    src: rootPath + 'i/',
    dest: rootPath + 'i/'
  },

  autoprefixer: {
    browsers: ['last 5 versions'],
    cascade: false
  },

  base64: {
    options: {
      baseDir: '..',
      extensions: ['png', 'jpg'],
      maxImageSize: 32 * 1024
    }
  }
};