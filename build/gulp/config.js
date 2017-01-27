var argv = require('yargs').argv;

var path = require('path');

var rootPath = path.join(__dirname, '../../');
var sassTempDir = '~sasstemp_' + new Date().getTime();

var es2015 = require('babel-preset-es2015-nostrict');

module.exports = {
  rootPath: rootPath,

  js: {
    dest: rootPath + 'js',
    src: argv['vendor'] ? [
      rootPath + 'js/src/vendor/jquery.js',
      rootPath + 'js/src/vendor/jquery-ui.js',
      rootPath + 'js/src/vendor/hammer.js',
      rootPath + 'js/src/vendor/jquery_plugins/*.js',
      rootPath + 'js/src/vendor/jquery_ui_widgets/*.js',
      rootPath + 'js/src/vendor/**/*.js'
    ] : [
      rootPath + 'js/src/common.js'
    ],
  },

  babel: {
    presets: [es2015],
    compact: false
  },

  css: {
    src: [
      rootPath + 'i/style/**/*.styl',
      '!' + rootPath + 'i/style/**/_*.styl'
    ],
    watchSrc: rootPath + 'i/style/**/*.styl',
    dest: rootPath + 'i/style/css/'
  },

  autoprefixer: {
    browsers: ['last 5 versions'],
    cascade: false
  },

  assets: {
    basePath: rootPath
  },

  base64: {
    options: {
      baseDir: '..',
      extensions: ['png', 'jpg'],
      maxImageSize: 32 * 1024
    }
  }
};