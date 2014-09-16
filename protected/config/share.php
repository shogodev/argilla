<?php
return array(
  'aliases' => array(
    'share' => 'frontend.share',
    'ext' => 'frontend.extensions',
    'nestedset' => 'frontend.extensions.nested-set-behavior',
  ),

  'preload' => array(
    'log',
  ),

  'import' => array(
    'share.*',
    'share.behaviors.*',
    'share.formatters.*',
    'share.helpers.*',
    'share.validators.*',
  ),

  'components' => array(
    'format' => array(
      'class' => 'share.formatters.SFormatter',
      'datetimeFormat' => 'd.m.Y H:i:s',
      'dateFormat'     => 'd.m.Y',
      'timeFormat'     => 'H:i:s',
      'numberFormat'   => array(
        'decimals'          => 0,
        'decimalSeparator'  => ',',
        'thousandSeparator' => ' ')
    ),

    'phpThumb' => array(
      'class' => 'ext.phpthumb.EPhpThumb',
      'options' => array(
        'jpegQuality' => '75',
      ),
    ),

    'email' => array(
      'class'    => 'ext.email.Email',
      'delivery' => 'php',
    ),

    'notification' => array(
      'class' => 'share.SNotification',
    ),
  ),
);