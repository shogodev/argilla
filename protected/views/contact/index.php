<?php
/**
 * @var ContactController $this
 * @var Contact $model
 */
?>

<div class="wrapper nofloat">
  <?php $this->renderPartial('/_breadcrumbs');?>
</div>

<div class="wrapper">
  <h1><?php echo Yii::app()->meta->setHeader('Контакты')?></h1>
</div>

<div class="contacts-wrap">
  <div class="contacts-map">
    <a href="" class="btn aqua-btn h30-btn contacts-print-btn noprint">
      Распечатать
    </a>
    <?php echo $model->map?>
    <div class="contacts-text panel paddings">
      <div class="s20 opensans bb m5"><?php echo $model->name?></div>

      <div class="m15">
        <p class="bb m5">Адрес офиса:</p>
        <p class="s14"><?php echo $model->address?></p>
      </div>

      <div class="m15">
        <p class="bb m5">URL-адрес:</p>
        <p class="s14"><a href="<?php echo $model->url?>"><?php echo $model->url?></a></p>
      </div>

      <?php if( $phones = $model->getFields('phones') ) { ?>
      <div class="m15">
        <p class="bb m5">Телефон:</p>
        <?php foreach($phones as $phone) { ?>
        <p class="s14"><?php echo $phone->value?> <?php echo $phone->description?></p>
        <?php } ?>
      </div>
      <?php } ?>

      <?php if( $emails = $model->getFields('emails') ) { ?>
      <div>
        <p class="bb m5">E-mail:</p>
        <?php foreach($emails as $email) { ?>
        <p class="s14"><a href="mailto:<?php echo $email->value?>"><?php echo $email->value?></a></p>
        <?php } ?>
      </div>
      <?php } ?>

      <?php echo $model->notice?>
    </div>
  </div>
</div>

<div class="wrapper"></div>