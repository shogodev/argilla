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

<?php $this->renderPartial('/layouts/head');?>

<body class="error-page error-404">

<div id="structure">

  <?php echo $content; ?>

</div>

<?php if( YII_DEBUG ) $this->renderPartial('//template/_templates_navigation'); ?>

</body>
</html>