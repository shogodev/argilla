<?php
return array(
  'param11' => array('id' => 11, 'parent' => 11),

  'param5' => array('id' => 5, 'visible' => 1, 'product' => 1,  'name' => 'Группа', 'parent' => 11, 'section' => 1, 'key' => 'filter'),
    'param1'  => array('id' => 1, 'visible' => 1, 'product' => 1,  'name' => 'Размер', 'parent' => 5, 'type' => 'checkbox', 'key' => 'size'),
    'param2'  => array('id' => 2, 'visible' => 1, 'product' => 1,  'name' => 'Цвет',   'parent' => 5, 'type' => 'checkbox', 'key' => 'color'),
    'param3'  => array('id' => 3, 'visible' => 1, 'product' => 1,  'name' => 'Длинна', 'parent' => 5, 'type' => 'checkbox', 'key' => 'length'),
    'param4'  => array('id' => 4, 'visible' => 1, 'product' => 1,  'name' => 'Текст',  'parent' => 5, 'key' => 'text'),
    'param10' => array('id' => 10, 'visible' => 1, 'product' => 1, 'name' => 'Диапазон', 'parent' => 5, 'key' => 'range'),

  'param6'  => array('id' => 6, 'visible' => 1, 'product' => 1, 'name' => 'Группа 2', 'parent' => 11, 'section' => 1, 'key' => 'filter'),
    'param7'  => array('id' => 7, 'visible' => 1, 'product' => 1, 'name' => 'Параметр в группе 2', 'parent' => 6, 'type' => 'text', 'key' => 'length'),
    'param8'  => array('id' => 8, 'visible' => 1, 'product' => 1, 'name' => 'Параметр в группе 2', 'parent' => 6, 'type' => 'text', 'key' => 'length'),

  'param13' => array('id' => 13, 'visible' => 1, 'product' => 1, 'name' => 'Общие параметры', 'key' => 'common', 'parent' => 11),
    'param12' => array('id' => 12, 'visible' => 1, 'name' => 'Вес', 'parent' => 13, 'type' => 'text'),
    'param14' => array('id' => 14, 'visible' => 0, 'name' => 'Отключенный параметр', 'parent' => 13),

  'param15' => array('id' => 15, 'visible' => 1, 'product' => 1, 'name' => 'Параметры корзины', 'key' => 'section', 'parent' => 11),
    'param16' => array('id' => 16, 'visible' => 1, 'name' => 'Ширина', 'parent' => 15, 'type' => 'checkbox'),
);