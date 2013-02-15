<?php
/**
 * @var $this BMenuController
 * @var $form BActiveForm
 * @var $model BFrontendMenu
 */

Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' );

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
      <table id="elements" class="detail-view table table-striped table-bordered">
        <thead>
          <tr>
            <th>Название</th>
            <th>Url</th>
            <th>Тип</th>
            <th>Вид</th>
          </tr>
        </thead>

        <!--ENTRIES-->
        <tbody class="sortable" id="entries">
          <?php foreach( $model->entries as $entry ):?>
            <tr class="item">
              <td class="name"><?php echo $entry->getModel()->getName();?></td>
              <td class="url"><?php echo $entry->getModel()->getUrl();?></td>

              <td class="type">
                <?php if( !$entry->getIsCustom() ):?>
                  <?php echo $entry->getModelClass();?>
                <?php else:?>
                  <?php echo CHtml::link($entry->getModelClass(), '#', array('data-model-id' => $entry->getModel()->getId(), 'class'   => 'custom-item edit-custom-item'));?>
                  <!--CUSTOM MODEL DATA-->
                  <div class="custom-item-parameters">
                    <ul>
                      <?php foreach( $entry->getModel()->data as $parameter ):?>
                        <li><?php echo $parameter->name?> => <?php echo $parameter->value?></li>
                      <?php endforeach;?>
                    </ul>
                  </div>
                  <!--END CUSTOM MODEL DATA-->
                <?php endif;?>
              </td>

              <td><div data-type="<?php echo get_class($entry->getModel());?>" class="switch-entry turn-on" data-id="<?php echo $entry->getModel()->getId();?>"><img src="<?php echo Yii::app()->baseUrl?>/css/i/toggle-on.png" /></div></td>
            </tr>
          <?php endforeach;?>
        </tbody>
        <!--END ENTRIES-->

        <!--AVAILABLE ENTRIES-->
        <tfoot id="available-entries">
          <?php foreach( $model->availableEntries as $entry ):?>
          <tr class="item">
            <td class="name"><?php echo $entry->getName();?></td>
            <td class="url"><?php echo $entry->getUrl()?></td>
            <td class="type">
              <?php if( get_class($entry) !== 'BFrontendBFrontendCustomMenuItem' ):?>
              <?php echo get_class($entry);?>
              <?php else:?>
              <?php echo CHtml::link(get_class($entry), '#', array('data-model-id' => $entry->getId(), 'class'   => 'custom-item edit-custom-item'));?>
              <!--CUSTOM MODEL DATA-->
              <div class="custom-item-parameters">
                <ul>
                  <?php foreach( $entry->data as $parameter ):?>
                  <li><?php echo $parameter->name?> => <?php echo $parameter->value?></li>
                  <?php endforeach;?>
                </ul>
              </div>
              <!--END CUSTOM MODEL DATA-->
              <?php endif;?>
            </td>
            <td><div class="switch-entry turn-off" data-type="<?php echo get_class($entry);?>" data-id="<?php echo $entry->getId();?>" ><img src="<?php echo Yii::app()->baseUrl?>/css/i/toggle-off.png" /></div></td>
          </tr>
          <?php endforeach;?>
        </tfoot>
        <!--END AVAILABLE ENTRIES-->

      </table>
    </td>
  </tr>
  <tr>
    <th>Добавить: </th>
    <td>
      <?php echo CHtml::tag('div', array('class' => 'btn', 'id' => 'create-menu-entry'), 'Создать')?>
    </td>
  </tr>
  <?php endif;?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>

<?php if( !$model->isNewRecord ):?>
<?php $this->renderPartial('_custom_item_form', array('model' => $model));?>
<script>
  $(function(){
    //------------------------------------------------------------------------
    // Switch entry event
    $('.switch-entry').live('click', function(){
      var url  = "<?php echo Yii::app()->controller->createUrl('menu/switchEntry');?>";
      var self = $(this);

      var type    = $(this).attr('data-type');
      var id      = $(this).attr('data-id');
      var menu_id = "<?php echo $model->id;?>";

      // Вывод сообщения об ошибке, если она существует,
      // в противном случае добавлени / удаление из таблицы элементов
      var callback = function( resp ){
        if( resp.error != undefined )
          alert(resp.error);
        else
        {
          $(self).children('img').remove();

          if( $(self).hasClass('turn-on') )
          {
            $(self).removeClass('turn-on');
            $(self).addClass('turn-off');
            $(self).append(getSwitchImage(false));

            $('#available-entries').append($(self).parents('.item'));
          }
          else
          {
            $(self).removeClass('turn-off');
            $(self).addClass('turn-on');
            $(self).append(getSwitchImage(true));

            $('#entries').append($(self).parents('.item'));
          }
        }
      };

      $.post(url, {"menu_id" : menu_id, "id" : id, "type" : type}, callback, "json");
    });

    // -----------------------------------------------------------------------------------------
    var fixHelper = function(e, ui) {
      ui.children().each(function() {
        $(this).width($(this).width());
      });
      return ui;
    };

    // Сортировка полей в группе
    $('.sortable').sortable({
      helper: fixHelper,
      update: function(event, ui) {
        sort(ui.item);
      }
    });
  });

  //------------------------------------------------
  // Сохранение сортировки
  function sort(item)
  {
    var url     = "<?php echo Yii::app()->controller->createUrl('menu/sort');?>";
    var i       = 1;
    var data    = {};
    var menu_id = "<?php echo $model->id;?>";

    $('.sortable tr').each(function(){
      var element = $(this).find('.switch-entry');

      data[i] = {
        "type"     : $(element).attr('data-type'),
        "id"       : $(element).attr('data-id'),
        "position" : i
      };

      i++;
    });

    $.post(url, {"menu_id" : menu_id, "data" : data});
  }

  //-----------------------------------------------------------------------------
  // Получение необходимого изображения в зависимости от статуса
  function getSwitchImage(status) {
    var on  = $('<img src="<?php echo Yii::app()->baseUrl?>/css/i/toggle-on.png" />');
    var off = $('<img src="<?php echo Yii::app()->baseUrl?>/css/i/toggle-off.png" />');

    if( status )
      return on;
    else
      return off;
  }

  // Попап для добавления/редактирования элементов меню
  $('#create-menu-entry, .edit-custom-item').click(function(){
    popupShow('#edit-custom-item');
    $('html, body').animate({
      scrollTop: 0
    }, 200);
  })
  $('.close-popup').click(function(e){
    e.preventDefault();
    popupClose( '#' + $(this).parents('.popup').attr('id') )
  })
  var popupShow = function(popupId) {
    $('.overlay-white').show();
    $(popupId).show();
  }
  var popupClose = function(popupId) {
    $(popupId).hide();
    $('.overlay-white').hide();
  }

</script>
<?php endif;?>
<?php $this->endWidget(); ?>