<?php
/**
 * @var BProductController $this
 * @var array $_data_
 * @var BProduct $model
 * @var BProductAssignment $assignmentModel
 */
?>
<?php if( $this->isUpdate() ) {?>
  <tr>
    <th><label>Модификации</label></th>
    <td>
      <?php
      $modificationColumns = array(
        array('type' => 'raw', 'header' => 'Заголовок',  'value' => 'CHtml::link($data->name, array("product/update", "id" => $data->primaryKey))'),
        array(
          'name' => 'price',
          'class' => 'OnFlyEditField',
          'ajaxUrl' => Yii::app()->controller->createUrl('product/onflyedit'),
          'htmlOptions' => array('class' => 'span1','style' => 'width: 100px;'),
          'header' => 'Цена',
        ),
        array(
          'name' => 'price_old',
          'class' => 'OnFlyEditField',
          'ajaxUrl' => Yii::app()->controller->createUrl('product/onflyedit'),
          'htmlOptions' => array('class' => 'span1','style' => 'width: 100px;'),
          'header' => 'Старая цена',
        ),
        array('name' => 'visible', 'class' => 'JToggleColumn', 'action' => 'product/toggle', 'header' => 'Вид'),
        array('class' => 'BButtonColumn'),
      );
      $widget = $this->widget('BGridView', array(
        'dataProvider' => new CArrayDataProvider($model->modifications, array('pagination' => false)),
        'template' => "{items}\n{pager}\n{buttons}\n{scripts}",
        'columns' => $modificationColumns,
        'buttonsTemplate' => 'product.views.product.modification._button_add_modification'
      )); ?>
    </td>
  </tr>
<?php }?>