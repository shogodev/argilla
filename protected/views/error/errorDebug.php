<?php
/**
 * @var ErrorController $this
 * @var string $code
 * @var string $type
 * @var string $errorCode
 * @var string $message
 * @var string $file
 * @var string $line
 * @var string $trace
 * @var array $traces
 */
?>
<section id="main">

  <?php $this->renderPartial('/breadcrumbs');?>

  <h2 class="m7">Ошибка <?php echo $this->errorCode?></h2>

  <div class="error">
    <p class="bb"><?php echo CHtml::encode($this->errorMessage); ?></p>
  </div>

</section>