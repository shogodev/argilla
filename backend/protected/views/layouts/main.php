<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="ru"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="ru"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ru"> <!--<![endif]-->

<?php $this->renderPartial('//layouts/head')?>

<body>

<?php $this->widget('bootstrap.widgets.TbNavbar', array(
  'type' => 'null',
  'brand' => '<img title="ShogoCMS v3.0" alt="ShogoCMS v3.0" src="'.Yii::app()->homeUrl.'/i/sp.gif" />',
  'brandUrl' => Yii::app()->homeUrl,
  'collapse' => true,
  'fluid' => true,
  'htmlOptions' => array('class' => 's-header'),
  'items' => array(
    CHtml::tag('h1', array('class' => 'title'), 'CMS '.CHtml::tag('a', array('href' => Yii::app()->getFrontendUrl(), 'rel' => 'external'), Yii::app()->request->getServerName())),
    Yii::app()->user->isGuest ? array() : array(
      'class' => 'bootstrap.widgets.TbMenu',
      'htmlOptions' => array('class' => 's-topnav pull-right'),
      'items' => CMap::mergeArray(Yii::app()->menu->getGroups(),
                                  array(array('label' => 'Выход ('.Yii::app()->user->name.')',
                                    'url' => array('/base/logout'),
                                    'itemOptions' => array('class' => 'logout')
                                  )))
    ),
  ),
)); ?>

<?php if( !Yii::app()->user->isGuest ) { ?>
  <?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'type' => 'pills',
    'brand' => false,
    'collapse' => true,
    'fluid' => true,
    'htmlOptions' => array('class' => 's-subnav'),
    'items' => array(
      array(
        'class' => 'bootstrap.widgets.TbMenu',
        'type' => 'pills',
        'stacked' => false,
        'items' => Yii::app()->menu->getModules()
      ),
    ),
  )); ?>
<?php } ?>

<div class="s-shader navbar-fixed-top"></div>

<?php if( !Yii::app()->user->isGuest ) { ?>
<div class="flash">
  <?php $this->widget('bootstrap.widgets.TbAlert'); ?>
</div>
  <?php
  Yii::app()->clientScript->registerScript(
    'flashSuccess',
    '$(".flash").slideDown("slow", function(){$(".flash").animate({opacity: 1.0}, 5000).fadeOut("slow");});',
    CClientScript::POS_READY
  ); ?>
<?php } ?>

<div class="container-fluid s-container" id="top">
  <?php if( !Yii::app()->user->isGuest && isset(Yii::app()->controller->module) ): ?>
  <div class="s-title">
    <h1 class="<?php echo Yii::app()->controller->module->id;?>"><?php echo Yii::app()->controller->module->name;?></h1>
  </div>

  <?php $this->widget('bootstrap.widgets.TbMenu', array('type'    => 'tabs',
                                                        'stacked' => false,
                                                        'items'   => Yii::app()->menu->getSubmodules(),
                                                       )); ?>
  <?php endif; ?>

  <?php echo $content;?>
</div>

<?php if( !Yii::app()->user->isGuest ) { ?>
<footer class="container-fluid s-footer">
  <hr />

  &copy; <?php echo date('Y'); ?>, <a rel="external" href="http://shogo.ru">Shogo.Ru</a>. Все права защищены.<br />
  <?php echo preg_replace('#.$#', '', Yii::powered()); ?> и <a rel="external" href="http://twitter.github.com/bootstrap/">Twitter Bootstrap</a>.
</footer>
<?php } ?>

<?php if( !Yii::app()->user->isGuest ) { ?>
<script>
//<![CDATA[
$(function () {
  fixLayout();
  $(window).wresize(fixLayout)
           .on('scroll', fixLayout);

  $('input[rel="extender"]').extender();

  $('label > span[rel="tooltip"]').tooltip();
});
//]]>
</script>
<?php } ?>

</body>
</html>