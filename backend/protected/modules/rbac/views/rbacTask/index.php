<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac
 *
 * @var BRbacTaskController $this
 * @var BActiveDataProvider $dataProvider
 * @var BRbacTask $model
 */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'filter' => $model,
  'columns' => array(
    array('name' => 'title', 'htmlOptions' => ['class' => 'span4']),
    array('name' => 'name', 'htmlOptions' => ['class' => 'span4']),
    array('name' => 'description'),
    array('class' => 'BButtonColumn'),
  ),
));