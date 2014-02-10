<?php
/**
 * @var FController $this
 */
?>
<div class="popup confirm-popup auto-width-popup" id="added-to-compare-popup">
  <?php $this->compare->addAfterAjaxScript(new CJavaScriptExpression("
      if( action == 'add' ) {
        var target = $('#added-to-compare-popup');
        $.overlayLoader(true, target);
      };
  "));?>
  <a href="" class="close"></a>
  <div class="center jurabold s20 m30">
    Товар добавлен в сравнение!
  </div>
  <div class="nofloat">
    <a href="<?php echo $this->createUrl("compare/index")?>" class="fl btn red-btn smallfont-btn lowercase-btn">Перейти к сравнению</a>
    <a href="" class="fr btn red-btn smallfont-btn lowercase-btn close-btn">Закрыть</a>
  </div>
</div>