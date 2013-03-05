<div class="wrap-info">
  <?php $this->renderPartial('//breadcrumbs');?>
</div>
<div class="wrap">
  <div class="container container_16 nofloat">
    <div class="center">
      <div class="h1 s30">Ошибка <?php echo $code?></div>
      <div class="bb m20">
        <p class="red"><b><?php echo CHtml::encode($message); ?></b></p>
      </div>
    </div>
  </div>
</div>