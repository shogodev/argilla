<?php if( !Yii::app()->user->isGuest ) { ?>
  <footer class="container-fluid s-footer">
    <hr />
    &copy; <?php echo date('Y'); ?>, <a rel="external" href="http://shogo.ru">Shogo.Ru</a>. Все права защищены.<br />
    <?php echo preg_replace('#.$#', '', Yii::powered()); ?> и <a rel="external" href="http://twitter.github.com/bootstrap/">Twitter Bootstrap</a>.
  </footer>
<?php } ?>