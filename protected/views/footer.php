<?php
/**
 * @var FController $this
 */
?>

<footer class="footer">

  <!-- bottom menu -->
  <nav class="menu">
    <?php ViewHelper::menu($this->getMenuBuilder()->getMenu('bottom'))?>
  </nav>

  <?php if( ViewHelper::contact() ) {?>

    <!-- phones -->
    <div class="footer-phones">
      <?php foreach(ViewHelper::phones() as $phone) {?>
        <a href="tel:<?php echo $phone->getClearPhone();?>">
          <?php echo $phone->value.$phone->description;?>
        </a>
      <?php } ?>
    </div>

    <!-- address -->
    <?php echo ViewHelper::contact()->address;?>
  <?php } ?>

  <!-- copyrights -->
  <?php echo $this->getCopyright('copyright');?>
  <?php echo $this->getCopyright('shogo');?>

  <!-- related links -->
  <a href="<?php echo $this->createUrl('link/index')?>">Ресурсы по теме</a>

  <!-- counters -->
  <?php foreach($this->counters as $counter) {?>
    <?php echo $counter?>
  <?php } ?>
</footer>

<!-- popups -->
<?php $this->renderPartial('/popups/popups'); ?>