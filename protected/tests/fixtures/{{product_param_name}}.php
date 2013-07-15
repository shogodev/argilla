<?php
return array(

  'param1' => array('id' => '1', 'visible' => 1, 'product' => 1,  'name' => 'Размер', 'parent' => '5', 'type' => 'checkbox', 'key' => 'size'),
  'param2' => array('id' => '2', 'visible' => 1, 'product' => 1,  'name' => 'Цвет',   'parent' => '5', 'type' => 'checkbox', 'key' => 'color'),
  'param3' => array('id' => '3', 'visible' => 1, 'product' => 1,  'name' => 'Длинна', 'parent' => '5', 'type' => 'checkbox', 'key' => 'length'),
  'param4' => array('id' => '4', 'visible' => 1, 'product' => 1,  'name' => 'Текст',  'parent' => '5', 'key' => 'text'),
  'param5' => array('id' => '5', 'visible' => 1, 'product' => 1,  'name' => 'Группа', 'key' => 'filter'),

  'param6'  => [
    'id' => '6',
    'visible' => 1,
    'product' => 1,
    'name' => 'Группа 2',
    'key' => 'filter'
  ],
  'param7'  => [
    'id' => '7',
    'visible' => 1,
    'product' => 1,
    'name' => 'Параметр в группе 2',
    'parent' => '6',
    'type' => 'text',
    'key' => 'length'
  ],
  'param8'  => [
    'id' => '8',
    'visible' => 1,
    'product' => 1,
    'name' => 'Параметр в группе 2',
    'parent' => '6',
    'type' => 'text',
    'key' => 'length'
  ],

  'param10' => array('id' => '10', 'visible' => 1, 'product' => 1,  'name' => 'Диапазон',  'parent' => '5', 'key' => 'range'),
);