<?php
return array(
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
  ),
);