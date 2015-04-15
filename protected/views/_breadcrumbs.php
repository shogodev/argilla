<?php
/**
 * @var FController $this
 */
$this->widget('FBreadcrumbs', array(
  'links' => $this->breadcrumbs,
  'separator' => '<div class="breadcrumbs-separator"></div>',
  'htmlOptions' => array('class' => 'breadcrumbs'),
));