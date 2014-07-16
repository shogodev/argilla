<?php
/**
 * @var UserProfileController $this
 */
?>
<div>
  <?php $this->widget('FMenu', array(
    'items' => $this->getMenu(),
    'encodeLabel' => false,
    'hideEmptyItems' => false,
    'activateParents' => true
  ))?>
</div>