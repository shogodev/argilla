<?php
/**
 * @var ContactController $this
 * @var Contact[] $model
 */
?>
<?php $this->renderPartial('/breadcrumbs');?>

<h1><?php echo $this->clip('h1', 'Контакты')?></h1>

<?php foreach($model as $contact) {?>
  <div class="text-container">
  <div class="left" style="width:30%" id="contact_4">
    <div class="m5">
      <div class="m3">
        <span class="bb">Наименование организации:</span>
      </div>
      <p style="padding-left:10px"><?php echo $contact->name?></p>
    </div>
    <?php  if( !empty($contact->address) ) {?>
      <div class="m5">
        <div class="m3">
          <span class="bb">Адрес магазина:</span>
        </div>
        <p style="padding-left:10px"><?php echo $contact->address?></p>
      </div>
    <?php }?>
    <?php  if( !empty($contact->url) ) {?>
      <div class="m5">
      <div class="m3">
        <span class="bb">URL-адрес:</span>
      </div>
      <p style="padding-left:10px">
        <a href="<?php echo $contact->url?>" target="_blank"><?php echo $contact->url?></a>
      </p>
    </div>
    <?php }?>
    <?php if( $contact->getFields('phones') ) {?>
    <div class="m5">
      <div class="m3">
        <span class="bb">Телефоны, факсы:</span>
      </div>
      <?php foreach($contact->getFields('phones') as $phone) {?>
        <p style="padding-left:10px;"><?php echo $phone?></p>
      <?php }?>
    </div>
    <?php }?>
    <?php if( $contact->getFields('email') ) {?>
      <div class="m3">
        <span class="bb">E-mail:</span>
      </div>
      <p style="padding-left:10px;">
        <?php foreach($contact->getFields('email') as $email) {?>
          <a href="mailto:<?php echo $email?>"><?php echo $email?></a>
        <?php }?>
      </p>
    <?php }?>
  </div>
  <div class="right">
    <?php if( !empty($contact->map) ) {?>
      <div class="fl">
        <?php echo $contact->map?>
      </div>
    <?php }?>
  </div>
</div>
<?php }?>