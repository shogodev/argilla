<?php $this->beginContent('//layouts/main'); ?>
<div class="row-fluid">

  <?php if( isset($this->breadcrumbs) && !Yii::app()->user->isGuest ): ?>
    <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
      'separator'   => '::',
      'htmlOptions' => array('class' => 's-breadcrumbs'),
      'homeLink'    => false,
      'links'       => $this->breadcrumbs,
    )); ?>
  <?php endif; ?>

  <div class="clearfix">
    <?php echo $content; ?>
  </div>
  
</div>
<?php $this->endContent(); ?>