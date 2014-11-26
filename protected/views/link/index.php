<?php
/**
 * @var LinkController $this
 * @var LinkSection[] $sections
 * @var FForm $form
 * @var array $_data_
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <div class="caption m20">
    <h1><?php echo Yii::app()->meta->setHeader('Ресурсы по теме')?></h1>
  </div>

  <?php if( $topText = $this->textBlockRegister('Текст в ресурсах по теме(в вурху)', 'В данном разделе сайта размещены интернет-ресурсы, схожей тематики.', null) ) { ?>
    <div class="text-container">
      <?php echo $topText?>
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
  <?php if( $textBeforeForm = $this->textBlockRegister('Текст в ресурсах по теме(над формой)', 'Мы обмениваемся ссылками с сайтами схожей тематики. Если ваш проект может быть интересен посетителям нашего сайта, вы можете заполнить приведенную ниже форму и мы свяжемся с вами в ближайшее время.', null) ) {?>
    <div class="text-container m20">
      <?php echo $textBeforeForm?>
    </div>
  <?php }?>

  <?php echo $form->render()?>

</div>