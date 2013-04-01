<?php
/**
 * @var FController $this
 * @var Info               $model
 * @var FForm|null         $form
 * @var CommentForm|null   $commentForm
 */
?>

<div class="wrap-info">
  <?php $this->renderPartial('/breadcrumbs');?>
</div>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="span2">
      Левое меню

      <!--Sidebar content-->
    </div>
    <div class="span10">
      <h1><?php echo $this->clip('h1', (empty($model->alternative_header) ? $model->name : $model->alternative_header))?></h1>

      <div class="text-container">
        <?php echo $model->content?>
      </div>

      <?php if( $model->children ) { ?>
      <?php foreach($model->getDescendants() as $child) { ?>

        <div class="grid_4">
          <?php if( $child->img ) { ?>
          <div class="m15">
            <a href="<?php echo $this->createUrl('info/index', array('url' => $child->url))?>">
              <img src="<?php echo $child->img?>" alt="" width="205" />
            </a>
          </div>
          <?php } ?>
          <h3><a href="<?php echo $this->createUrl('info/index', array('url' => $child->url))?>"><?php echo (empty($child->alternative_header) ? $child->name : $child->alternative_header)?></a></h3>
          <div class="text-container"><?php echo $child->notice?></div>
        </div>

        <?php } ?>
      <?php } ?>
    </div>
  </div>
</div>
