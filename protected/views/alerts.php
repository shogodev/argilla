<?php
/**
 * User: Nikita Melnikov <melnikov@shogo.ru>
 * Date: 1/14/13
 */
Yii::app()->clientScript->registerScript(
  'flashSuccess',
  '$(".flash").slideDown("slow", function(){$(".flash").animate({opacity: 1.0}, 5000).fadeOut("slow");});',
  CClientScript::POS_READY
);
Yii::app()->clientScript->registerScript(
  'flashClose',
  '$(".flash .close").on("click", function(e){
    e.preventDefault();
    $(this).parents(".system-message").fadeOut("slow");
  });',
  CClientScript::POS_READY
);
?>
<div class="flash">
  <?php foreach( Yii::app()->user->getFlashes() as $key => $message ):?>
  <div class="system-message">
    <div class="alert alert-block alert-<?php echo $key;?>">
      <a data-dismiss="alert" class="close">×</a>
      <?php echo $message;?>
    </div>
  </div>
  <?php endforeach;?>
</div>