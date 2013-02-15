<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="ru"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="ru"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ru"> <!--<![endif]-->

<?php
/**
 * @var $this FController
 */
?>

<head>
  <meta charset="utf-8" />
  <title><?php echo CHtml::encode($this->meta->title); ?></title>
  <meta name="viewport" content="target-densitydpi=device-dpi" />
  <?php if( !empty($this->meta->description) ) {?><meta name="description" content="<?php echo CHtml::encode($this->meta->description); ?>" /><?php }?>
  <?php if( !empty($this->meta->keywords) ) {?><meta name="keywords" content="<?php echo CHtml::encode($this->meta->keywords); ?>" /><?php }?>
  <base href="<?php echo Yii::app()->homeUrl?>" />

  <link rel="canonical" href="<?php echo $this->getCanonicalUrl();?>" />

  <?php Yii::app()->clientScript->registerScriptFile('/js/config/config.js') ?>

  <!--[if lt IE 9]>
  <script src="js/html5msie/html5shiv-printshiv.js"></script>
  <script src="js/html5msie/respond.js"></script>
  <![endif]-->

  <?php Yii::app()->clientScript->registerCssFile('/i/st.css') ?>
</head>

<body>

<?php $this->renderOverride('header');?>

<div class="container" style="padding-top: 60px">

  <?php $this->renderPartial('/alerts');?>

  <?php echo $content; ?>

</div>

<?php $this->renderOverride('footer');?>

</body>
</html>