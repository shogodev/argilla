<?php
/**
 * @var FController $this
 */
?>
  <header class="nofloat">
    <a href="<?php echo $this->createUrl('index/index')?>">
      <img src="i/logo.png" alt="" />
    </a>
    <div class="no-overflow" style="padding-bottom: 38px">
      <div class="menu hd-menu main-hd-menu fr">
        <?php $this->widget('FMenu', array('items' => $this->getTopMenu()))?>
      </div>
    </div>
    <div class="nofloat">
      <div class="fl">
        <div class="nofloat phones">
          <a href="" class="callback-link">
            <img src="i/btn-callback.png" alt="" />
          </a>
          <div class="popup" id="callback-popup">
            <a href="" class="close"></a>
            <div class="h2">Заказ обратного звонка</div>
            <?php echo $this->callbackForm?>
          </div>
          <script>
            //<[CDATA[
            $('.callback-link').click(function(e){
              e.preventDefault();
              var target = '#callback-popup',
                self = $(this);
              if ( $(target).is(':visible') ) {
                $(target).hide();
              } else {
                $.showpos(self, target, {value:'under+10',auto:false}, {value:'left-5',auto:false}, true, false);
              }
            })
            $('#callback-popup .close').click(function(e){
              e.preventDefault();
              var target = $(this).closest('.popup');
              target.hide();
            })
            //]]>
          </script>
        </div>
      </div>
    </div>
  </header>

<?php $this->widget('FMenu', array(
      'items' => array(
        array('label' => 'Новости', 'url' => $this->createUrl('news/section', array('url' => 'news'))),
        array('label' => 'Информация', 'url' => $this->createUrl('info/index', array('url' => 'shop'))),
        array('label' => 'Продукты', 'url' => $this->createUrl('product/sections')),
      ),
));?>
<form class="navbar-search pull-left" action=""><input type="text" class="search-query span2" placeholder="Search"></form>
<?php
$this->widget('FMenu', array(
  'items' => CMap::mergeArray(
    array(
      array('label' => 'Заказать обратный звонок', 'url' => '#', 'linkOptions' => array('class' => 'callback-btn'))
    ),
    Yii::app()->user->isGuest ?
      array(
        array('label' => 'Вход', 'url' => $this->createUrl('user/login')),
        array('label' => 'Регистрация', 'url' => $this->createUrl('user/registration')),
      )
      :
      array(
        array('label' => 'Выход', 'url' => $this->createUrl('user/logout')),
        array('label' => 'Профиль', 'url' => $this->createUrl('user/data')),
  ))));
?>