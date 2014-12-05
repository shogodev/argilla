<?php
/**
 * @var $this BController
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="ru"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang="ru"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ru"> <!--<![endif]-->

<?php $this->renderPartial('//layouts/head')?>

<body>
  <?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'type' => 'null',
    'brand' => '<img title="Argilla" alt="Argilla" src="'.Yii::app()->homeUrl.'/i/sp.gif" />',
    'brandUrl' => Yii::app()->homeUrl,
    'collapse' => true,
    'fluid' => true,
    'htmlOptions' => array('class' => 's-header'),
    'items' => array(
      CHtml::tag('h1', array('class' => 'title'), 'CMS '.CHtml::tag('a', array('href' => Yii::app()->getFrontendUrl(), 'rel' => 'external'), Yii::app()->request->getServerName())),
      Yii::app()->user->isGuest ? array() : array(
        'class' => 'bootstrap.widgets.TbMenu',
        'htmlOptions' => array('class' => 's-topnav pull-right'),
        'items' => CMap::mergeArray(
          Yii::app()->menu->getGroups(),
          array(array('label' => 'Выход ('.Yii::app()->user->name.')',
            'url' => array('/base/logout'),
            'itemOptions' => array('class' => 'logout')
          ))
        )
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
    <div class="flash" id="flash-message">
      <?php $this->widget('bootstrap.widgets.TbAlert'); ?>
    </div>
  <?php } ?>

  <div class="container-fluid s-container" id="top">
    <?php if( !Yii::app()->user->isGuest && isset($this->module) ) { ?>
      <div class="s-title">
        <h1 class="<?php echo $this->module->getHeaderCssClass();?>"><?php echo $this->module->name;?></h1>
      </div>
      <?php $this->widget('bootstrap.widgets.TbMenu', array(
          'type'=> 'tabs',
          'stacked' => false,
          'items'   => Yii::app()->menu->getSubmodules(),
       )); ?>
    <?php } ?>

    <?php echo $content;?>
  </div>

  <?php $this->renderPartial('//layouts/footer')?>
</body>
</html>