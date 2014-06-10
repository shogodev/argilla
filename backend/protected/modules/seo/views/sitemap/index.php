<?php
/**
 * @var BSitemapController   $this
 * @var BSitemapRoute        $model
 * @var BActiveDataProvider  $dataProvider
 */ ?>
<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'route'),
    array('name' => 'changefreq', 'class' => 'OnFlyEditField', 'dropDown' => $model->getChangeFreqs()),
    array('name' => 'priority', 'class' => 'OnFlyEditField', 'htmlOptions' => array('class' => 'span1')),
    array('class' => 'JToggleColumn', 'name' => 'lastmod'),
    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));