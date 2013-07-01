<?php
/**
 * @var LinkController $this
 * @var LinkSection[] $sections
 * @var FForm $form
 * @var array $_data_
 */
?>

<div>

  <?php $this->renderPartial('/breadcrumbs');?>

  <h1><?php echo $this->clip('h1', 'Ресурсы по теме')?></h1>

  <?php if( $this->textBlock('resources_page_text') ) { ?>
  <div class="text-container">
    <?php echo $this->textBlock('resources_page_text')?>
  </div>
  <?php } ?>

  <div class="nofloat m30">
    <?php foreach(Arr::divide($sections) as $i => $part) { ?>
    <div class="<?php echo $i % 2 == 0 ? 'l' : 'r'?>-main">
      <?php foreach($part as $section) { ?>
        <div class="m10">
          <a href="<?php echo $section->url ?>">
            <?php echo $section->name; ?>
          </a> (<?php echo $section->linkCount; ?>)
        </div>
      <?php } ?>
    </div>
    <?php } ?>
  </div>

  <div class="h3">Форма для добавления сайта в каталог</div>

  <?php if( $this->textBlock('resources_form_text') ) { ?>
    <div class="text-container">
      <?php echo $this->textBlock('resources_form_text')?>
    </div>
  <?php } ?>

  <div>
    <?php echo $form->render()?>
  </div>

</div>