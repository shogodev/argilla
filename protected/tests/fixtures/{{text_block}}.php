<?php
return array(
  '1' => array('id' => '1', 'location' => 'main', 'name' => 'Блок на главной 1', 'content' => 'Текст 1',  'visible' => 1, 'position' => 30),
  '2' => array('id' => '2', 'location' => 'main', 'name' => 'Блок на главной 2', 'content' => 'Текст 2',  'visible' => 1, 'position' => 20),
  '3' => array('id' => '3', 'location' => 'main', 'name' => 'Блок на главной 3', 'content' => 'Текст 3',  'visible' => 0, 'position' => 10),
  '4' => array('id' => '4', 'location' => 'mainNotVisible', 'name' => 'Тест2', 'content' => 'testNotVisible', 'visible' => 0),
  '5' => array('id' => '5', 'location' => 'testReplace', 'name' => 'Test replace', 'content' => 'text {before}', 'visible' => 1),
  '6' => array('id' => '6', 'location' => 'testReplaceByTextBlock', 'name' => 'Test replace text block', 'content' => 'text2 {replaced_text_block}', 'visible' => 1),
  '7' => array('id' => '7', 'location' => 'replaced_text_block', 'name' => 'Test replace text block', 'content' => 'new replaced text', 'visible' => 1),
  '8' => array('id' => '8', 'location' => 'test_replace_registration', 'name' => 'Test replace', 'content' => 'text3 {before}', 'visible' => 1),
);