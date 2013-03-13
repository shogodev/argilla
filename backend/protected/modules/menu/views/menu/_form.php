<?php
/**
 * @var $this BMenuController
 * @var $form BActiveForm
 * @var $model BFrontendMenu
 */
Yii::app()->breadcrumbs->show();

$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));

$this->renderPartial('//_form_buttons', array('model' => $model));
echo $form->errorSummary($model);
echo $form->renderRequire();
?>

<table id="yw0" class="detail-view table table-striped table-bordered">
<thead>
  <tr>
    <th colspan="2">Меню</th>
  </tr>
</thead>
<tbody>
  <?php echo $form->textFieldRow($model, 'name'); ?>
  <?php echo $form->textFieldRow($model, 'sysname'); ?>
  <?php echo $form->textFieldRow($model, 'url'); ?>

  <?php echo Chtml::link('', '#', array('class' => 'visible_toggle', 'rel' => 'tooltip'))?>

  <?php if( !$model->isNewRecord ):?>
  <tr>
    <th>Элементы: </th>
    <td>
      <?php $this->widget('BFrontendMenuGridView', array(
        'id' => BFrontendMenuGridView::buildGridId($model),
        'dataProvider' => $model->getGridData(),
        'buttonsTemplate' => false,
        'columns' => array(
          array('name' => 'name', 'header' => 'Название'),
          array(
            'name' => 'position',
            'header' => 'Позиция',
            'class' => 'BFrontendMenuGridPositionColumn',
            'gridId' => BFrontendMenuGridView::buildGridId($model),
            'action' => '/menu/bMenu/setPosition'
          ),
          array('name' => 'url', 'header' => 'Url'),
          array('name' => 'type', 'header' => 'Тип', 'class' => 'BFrontendMenuGridTypeColumn'),
          array(
            'name' => 'active',
            'header' => 'Вид',
            'class' => 'BFrontendMenuGridActiveColumn',
            'menu_id' => $model->getId()
          )
        ),
      ));?>
    </td>
  </tr>
  <tr>
    <th>Добавить: </th>
    <td>
      <?php echo CHtml::tag('div', array(
        'class' => 'btn',
        'rel' => 'tooltip',
        'href' => '#',
        'onClick' => 'assigner.open("'.$this->createUrl('menuCustomItem/create', array('popup' => true)).'", {"closeOperation" : function(){location.reload();}})',
      ), 'Создать');?>
    </td>
  </tr>
  <?php endif;?>

</tbody>
</table>
<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>