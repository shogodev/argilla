<?php
/**
 * @var ErrorController $this
 */
?>
<section id="main">

  <?php $this->renderPartial('/_breadcrumbs');?>

  <h2 class="m7">Требуется авторизация</h2>

  <div class="error">
    <p class="bb"><?php echo CHtml::encode($this->errorMessage); ?></p>
  </div>

</section>