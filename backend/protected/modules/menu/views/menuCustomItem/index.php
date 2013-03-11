<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'filter' => $model,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn',  'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'name'),
    array('name' => 'url'),
    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => false),
    array('class' => 'BButtonColumn'),
  ),
));