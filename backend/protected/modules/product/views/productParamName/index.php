<?php
/**
 * @var BProductParamNameController $this
 * @var BProductParamName $model
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'buttonsTemplate' => '_form_button_create',
  'rowCssClassExpression' => '$data->isGroup() ? "group" : ($row % 2 ? "odd" : "even" )',
  'columns' => array(
    array('name' => 'position', 'htmlOptions' => array('class' => 'span1'), 'class' => 'OnFlyEditField', 'header' => 'Позиция'),
    array('name' => 'name', 'header' => 'Название', 'filter' => false),

    array(
      'name' => 'section_id',
      'filter' => CMap::mergeArray(array(
        'common' => '[Общие]',
      ),
        BProductSection::listData()
      ),
      'htmlOptions' => array('prompt' => '[Все]'),
      'header' => 'Раздел',
      'value' => '$data->sectionName'
    ),
    array('name' => 'type', 'header' => 'Тип', 'filter' => false, 'value' => '$data->isGroup() ? "" : $data->types[$data->type]'),
    array('name' => 'key', 'htmlOptions' => array('class' => 'span3'), 'class' => 'OnFlyEditField', 'header' => 'Ключ'),
    array('class' => 'ParamToggleColumn', 'name' => 'visible', 'header' => 'Вид'),
    array('class' => 'ParamToggleColumn', 'name' => 'product', 'header' => '<span data-original-title="Параметр отображается в карточке товара">Карточка</span>', 'filter' => false, 'headerHtmlOptions' => array('rel' => 'tooltip')),
    array('class' => 'ParamToggleColumn', 'name' => 'section', 'header' => '<span data-original-title="Параметр отображается на разводной в режиме \'Плитка\'">Плитка</span>', 'filter' => false, 'headerHtmlOptions' => array('rel' => 'tooltip')),
    array('class' => 'ParamToggleColumn', 'name' => 'section_list', 'header' => '<span data-original-title="Параметр отображается на разводной в режиме \'Список\'">Список</span>', 'filter' => false, 'headerHtmlOptions' => array('rel' => 'tooltip')),
    array('class' => 'ParamToggleColumn', 'name' => 'selection', 'header' => '<span data-original-title="Параметр участвует в подборе">Подбор</span>', 'filter' => false, 'headerHtmlOptions' => array('rel' => 'tooltip')),

    array('class' => 'ParamButtons'),
  ),
));
?>

<script>
  //<![CDATA[
  $(function () {
    var headerTooltips = function(){$('th[rel="tooltip"] > span').tooltip().css('border-bottom', '1px dashed');};
    $.fn.yiiGridView.addObserver('yw0', headerTooltips);
    headerTooltips();
  });
  //]]>
</script>