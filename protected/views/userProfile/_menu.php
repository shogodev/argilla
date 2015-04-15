<?php
/**
 * @var UserProfileController $this
 */
?>
<aside id="left">
  <div class="menu left-sections-menu personal-menu m15">
    <?php $this->widget('FMenu', array(
      'items' => $this->getMenu(),
      'encodeLabel' => false,
      'hideEmptyItems' => false,
      'activateParents' => true
    ))?>
  </div>

  <?php if( PriceHelper::isNotEmpty(Yii::app()->user->profile->discount) ) {?>
    <div class="personal-discount center">
      <div class="product-discount">
        <span>-<?php echo PriceHelper::number(Yii::app()->user->profile->discount)?></span>%
      </div>
      <div class="s15 opensans bb uppercase">Ваша скидка</div>
    </div>
  <?php }?>
</aside>