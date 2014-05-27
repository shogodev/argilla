<?php
/**
 * @var BSitemapController   $this
 * @var BSitemapRoute        $model
 * @var BActiveDataProvider $dataProvider
 */
$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'route', 'header' => 'Маршрут'),
    array(
      'name' => 'changefreq',
      'class' => 'OnFlyEditField',
      'header' => 'Частота изменения',
      'dropDown' => $model->changeFreqs,
    ),
    array(
      'name' => 'priority',
      'class' => 'OnFlyEditField',
      'htmlOptions' => array('class' => 'span1'),
      'header' => 'Приоритет',
    ),
    array('class' => 'JToggleColumn', 'name' => 'lastmod'),
    array('class' => 'JToggleColumn', 'name' => 'visible', 'header' => 'Вид'),
    array('class' => 'BButtonColumn'),
  ),
));