<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="ru"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="ru"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ru"> <!--<![endif]-->

<?php
/**
 * @var FController $this
 * @var string $content
 */
?>

<head>
  <meta charset="utf-8" />
  <title><?php echo Yii::app()->meta->title;?></title>
  <?php echo Yii::app()->meta->custom;?>
  <meta name="viewport" content="target-densitydpi=device-dpi" />
  <base href="<?php echo Yii::app()->homeUrl?>" />

  <?php echo CHtml::linkTag("canonical", null, Yii::app()->controller->getCanonicalUrl());?>

  <!--[if lt IE 9]>
  <script src="js/html5msie/html5shiv-printshiv.js"></script>
  <script src="js/html5msie/respond.js"></script>
  <![endif]-->

  <?php echo CHtml::linkTag("stylesheet", null, Yii::app()->assetManager->publish(getcwd().'/i/style').'/css/st.css');?>

  <script>
  //<![CDATA[
    $('html').removeClass('no-js');
    var isMobile = navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i) != null;
    if (isMobile) {
      $('html').addClass('mobile');
    }
  //]]>
  </script>
</head>

<body>

<?php $this->renderOverride('header');?>

<div class="container" style="padding-top: 60px;">

  <?php $this->renderPartial('/alerts');?>

  <?php echo $content; ?>

</div>

<?php $this->renderOverride('footer');?>

</body>
</html>