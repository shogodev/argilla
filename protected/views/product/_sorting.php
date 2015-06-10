<?php
/**
 * @var ProductController $this
 * @var FActiveRecord $model
 */
?>


<?php $this->widget('FListViewSorting', array(
  'containerClass' => null,
  'dropDownContainerClass' => 'select-container sorting-select',
  'labelClass' => 'filter-label',
)); ?>

<?php $this->widget('FListViewPageSize', array(
  'containerClass' => null,
  'labelClass' => 'filter-label',
  'dropDownContainerClass' => 'select-container amount-select',
)); ?>