<?php
/**
 * @var ErrorController $this
 */
?>
<div class="error-text">
  <h1 class="error-code">Ошибка 404</h1>

  <div class="error-note">
    <?php echo CHtml::encode($this->errorMessage); ?>
  </div>
</div>