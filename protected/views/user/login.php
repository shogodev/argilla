<?php
/**
 * @var UserController $this
 * @var FForm $loginForm
 */
?>
  <div class="wrapper" style="background-color: #F4F4F4;">

    <?php $this->renderPartial('/_breadcrumbs');?>

    <h1><?php echo Yii::app()->meta->setHeader('Вход')?></h1>

    <?php echo $loginForm->render()?>
  </div>