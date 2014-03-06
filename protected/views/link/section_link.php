<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * @var LinkController $this
 * @var LinkSection $data
 * @var integer $index
 * @var FListView $widget
 */
?>
<div class="m10">
  <a href="<?php echo $this->createUrl('link/section', ['url' => $data->url]) ?>">
    <?php echo $data->name; ?>
  </a> (<?php echo $data->linkCount; ?>)
</div>