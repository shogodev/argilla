<?php
/* @var BActiveForm $form */
/* @var BProductParamName[] $parameters */
?>

<div class="group-view">

  <table class="items table table-striped table-bordered param-table">
    <thead>
    <tr>
      <th>Параметры</th>
      <th>Значения</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach($parameters as $param) { ?>
      <?php if( $param->isGroup() ) { ?>
        <tr class="group"><td colspan="2"><?php echo $param->name?></td></tr>
      <?php } else {?>
        <tr>
          <th><label><?php echo $param->name?></label></th>
          <td>
            <?php switch($param->type) {
            case 'text':
            case 'slider':
              echo CHtml::activeTextField($param, "[$param->id]value");
              break;

            case 'checkbox':
              echo CHtml::tag('div', array('style' => 'float: left'), false, false);
              echo $form->checkBoxList($param, "[$param->id]value", CHtml::listData($param->variants, 'id', 'name'));
              echo CHtml::closeTag('div');
              break;

            case 'select':
              echo $form->dropDownList($param, "[$param->id]value", array('' => 'Не задано') + CHtml::listData($param->variants, 'id', 'name'));
              break;

            case 'radio':
              echo CHtml::tag('div', array('style' => 'float: left'), false, false);
              echo $form->radioButtonList($param, "[$param->id]value", CHtml::listData($param->variants, 'id', 'name'));
              echo CHtml::closeTag('div');
              break;
            } ?>
          </td>
        </tr>
      <?php } ?>
    <?php } ?>
    </tbody>
  </table>

</div>