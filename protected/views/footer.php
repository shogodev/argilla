<?php
/**
 * @var FController $this
 */
?>

<footer id="footer" class="footer" class="no-print">
  <div class="footer--top-row">
    <div class="container white nofloat">
      <nav id="footer-menu" class="menu footer-menu justify-menu s18 upcase">
        <?php ViewHelper::menu($this->getMenuBuilder()->getMenu('bottom'))?>
      </nav>
      <?php if( ViewHelper::contact() ) {?>
        <div class="center">
          <div class="footer--phone">
            <?php foreach(ViewHelper::phones() as $phone) {?>
              <a class="nova s24 white" href="tel:<?php echo $phone->getClearPhone();?>"><?php echo $phone->value.$phone->description;?></a>
            <?php }?>
          </div>
          <div class="footer--address text-left">
            <?php echo ViewHelper::contact()->address;?>
          </div>
        </div>
      <?php }?>
    </div>
  </div>
  <div class="footer--bottom-row">
    <div class="container white nofloat">
      <div class="s12 white fl">
        <?php echo $this->getCopyright('copyright');?>
        <?php echo $this->getCopyright('shogo');?>
      </div>
      <div class="footer--related-resources fr">
        <a class="blue" href="<?php echo $this->createUrl('link/index')?>">Ресурсы по теме</a>
      </div>
      <div>
        <?php foreach($this->counters as $counter) {?>
          <?php echo $counter?>
        <?php }?>
      </div>
    </div>
  </div>
</footer>

<?php $this->renderPartial('/popups/popups'); ?>
