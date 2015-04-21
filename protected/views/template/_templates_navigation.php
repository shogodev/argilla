<?php
/**
 * @var FController $this
 */
?>
<!-- Templates navigation panel styles -->
<style>
  #templates-navigation * {
    font-family: Arial;
    font-size: 12px; }
  #templates-navigation {
    width: 180px;
    padding: 10px;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    background: #000;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999999; }
  #templates-navigation.collapsed {
    left: -190px; }
  .templates-navigation-toggle {
    display: none;
    cursor: pointer;
    width: 10px;
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5); }
  .templates-navigation-toggle:before {
    border-color: transparent #888;
    content: '';
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 5px 5px 5px 0;
    margin-left: 2px;
    margin-top: -5px;
    position: fixed;
    top: 50%; }
  #templates-navigation.collapsed .templates-navigation-toggle:before {
    border-width: 5px 0 5px 5px; }
  #templates-navigation:hover .templates-navigation-toggle,
  #templates-navigation.collapsed .templates-navigation-toggle {
    display: block; }
  .templates-map-container {
    max-height: 60%;
    margin-bottom: 10px; }
  .templates-map ul {
    margin: 2px 0; }
  .templates-map li {
    list-style: none;
    background: none;
    margin: 0;
    padding: 0;  }
  .templates-map ul li ul  {
    margin-left: 15px; }
  .templates-map ul li {
    list-style-position: inside;
    list-style-type: disc;
    color: #fff; }
  .templates-map li a {
    text-decoration: none;
    color: #fff; }
  .templates-map li a:hover {
    text-decoration: underline; }
  .templates-map li a.active {
    font-weight: bold; }
  .templates-navigation-header {
    font-weight: bold;
    color: #fff; }
  .template-description {
    color: #fff; }
  #templates-navigation .scroll-pane {
    color: white;
    width: 100%;
    height: 100%;
    overflow: auto; }
  #templates-navigation .jspContainer {
    overflow: hidden;
    position: relative; }
  #templates-navigation .jspPane {
    position: absolute; }
  #templates-navigation .jspVerticalBar {
    background: none repeat scroll 0 0 transparent;
    height: 100%;
    position: absolute;
    right: 0;
    top: 0;
    width: 3px; }
  #templates-navigation .jspCap {
    display: none; }
  #templates-navigation .jspVerticalBar *, #templates-navigation .jspHorizontalBar * {
    margin: 0;
    padding: 0; }
  #templates-navigation .jspTrack {
    background: none repeat scroll 0 0 transparent;
    position: relative; }
  #templates-navigation .jspDrag {
    background: none repeat scroll 0 0 #999;
    cursor: pointer;
    left: 0;
    position: relative;
    top: 0; }
  .templates-navigation-close {
    position: absolute;
    top: 5px;
    right: 10px;
    padding: 5px 10px;
    color: #fff !important;
    font-weight: bold;
    text-decoration: none; }
  .templates-navigation-close:hover {
    text-decoration: none; }
  @media print {
    #templates-navigation {
      display: none; }
  }
</style>
<!-- End of styles for templates navigation -->

<!-- Left panel with templates list and descriptions -->
<div id="templates-navigation">
  <a href="" class="templates-navigation-close" title="Закрыть">х</a>
  <div class="templates-navigation-toggle"></div>
  <div class="templates-navigation-header">Список шаблонов:</div>
  <div class="templates-map-container">
      <ul class="templates-map scroll-pane">
        <li>
          <a href="<?php echo $this->createUrl('index/index');?>" data-description="">Главная</a>
          <ul>
            <li>
              <a href="<?php echo $this->createUrl('product/section', array('section' => 'yablonya'));?>" data-description="">Каталог</a>
              <ul>
                <li><a href="<?php echo $this->createUrl('product/one', array('url' => 'bogatyr'));?>" data-description="">Карточка</a></li>
              </ul>
            </li>
            <li><a href="<?php echo $this->createUrl('order/firstStep');?>" data-description="">Корзина (пустая)</a></li>
            <li><a href="/templates/basket_step_1/" data-description="">Корзина (шаг 1)</a></li>
            <li><a href="/templates/basket_step_2/" data-description="">Корзина (шаг 2)</a></li>
            <li><a href="/templates/basket_step_3/" data-description="">Корзина (шаг 3)</a></li>
              <li><a href="<?php echo $this->createUrl('contact/index');?>" data-description="">Контакты</a></li>
              <li>
                <a href="<?php echo $this->createUrl('news/section', array('section' => 'news'));?>" data-description="">Новости</a>
                <ul>
                  <li><a href="<?php echo $this->createUrl('news/one', array('section' => 'news','url' => 'v_manezhe_otkrylis_vosem_fotovystavok'));?>" data-description="">Одна новость</a></li>
                </ul>
              </li>
              <li><a href="<?php echo $this->createUrl('info/index', array('url' => 'garantia'));?>" data-description="">Инфостраница</a></li>
              <li>
                <a href="/templates/personal/" data-description="">Личный кабинет</a>
                <ul>
                  <li><a href="/templates/personal_history/" data-description="">История заказов</a></li>
                  <li><a href="/templates/personal_history/?empty=true" data-description="">История заказов (пустая)</a></li>
                  <li><a href="/templates/personal_password/" data-description="">Смена пароля</a></li>
                </ul>
              </li>
              <li><a href="<?php echo $this->createUrl('user/login');?>" data-description="Всегда нужно, здесь выводятся ошибки">Вход</a></li>
              <li><a href="<?php echo $this->createUrl('user/registration');?>" data-description="">Регистрация</a></li>
              <li><a href="<?php echo $this->createUrl('user/restore');?>" data-description="">Восстановление пароля</a></li>
              <li><a href="<?php echo $this->createUrl('search/index', array('searchid' => 2193125, 'text' => 1))?>" data-description="">Результаты поиска</a></li>
              <li>
                <a href="<?php echo $this->createUrl('link/index');?>" data-description="">Ресурсы по теме</a>
                <ul>
                  <li><a href="<?php echo $this->createUrl('link/section', array('section' => 'doors'));?>" data-description="">Ресурсы по теме (категория)</a></li>
                </ul>
              </li>
              <li><a href="<?php echo $this->createUrl('sitemap/index');?>" data-description="">Карта сайта</a></li>
            </ul>
        </li>
      <li><a href="/templates/email/" data-description="">Почтовое уведомление</a></li>
    </ul>
  </div>
  <div class="templates-navigation-header">Примечание:</div>
  <div class="scroll-pane template-description-block">
    <div class="template-description">
    </div>
  </div>
</div>
<!-- End of navigation-panel -->

<script>
//<![CDATA[
  // На маленьких мониторах сразу прячем панель
  if ( $(window).width() < 1100 ) {
    $('#templates-navigation').toggleClass('collapsed');
  }
  // Скрытие панели
  $('.templates-navigation-toggle').on('click', function() {
    $('#templates-navigation').toggleClass('collapsed');
  })
  // Закрытие панели
  $('.templates-navigation-close').on('click', function(e){
    e.preventDefault();
    $('#templates-navigation').hide();
  })

  var changeDescription = function() {
    // Выводит описание открытой страницы из дата-атрибутов
    var currentPage = window.location.pathname;
    $('.templates-map li a').each(function() {
      if ( $(this).attr('href') == currentPage ) {
        $('.template-description').html( $(this).data('description') );
        $(this).addClass('active');
      }
    });
  }

  $(function(){
    changeDescription();

    var scrollHeight = $(window).height() - $('.template-description-block').position().top - 10;
    $('.template-description-block').css( 'height', scrollHeight );
    $('.templates-map').css( 'height', $('.templates-map-container').height() );
    $('.scroll-pane').jScrollPane();
  })

  $(window).resize(function(){
    var scrollHeight = $(window).height() - $('.template-description-block').position().top - 10;
    $('.template-description-block').css( 'height', scrollHeight );
    $('.templates-map').css( 'height', $('.templates-map-container').height() );
    $('.scroll-pane').jScrollPane();
  });
//]]>
</script>