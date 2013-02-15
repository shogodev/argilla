<?php
/**
 * @var FController $this
 */
?>

<?php $this->widget('bootstrap.widgets.TbNavbar', array(
  'type' => 'null',
  'brand' => 'Argilla',
  'brandUrl' => Yii::app()->getHomeUrl(),
  'collapse' => true,
  'fixed' => 'top',
  'items' => array(
    array(
      'class' => 'bootstrap.widgets.TbMenu',
      'items' => array(
        array('label' => 'Новости', 'url' => $this->createUrl('news/section', array('url' => 'news'))),
        array('label' => 'Информация', 'url' => $this->createUrl('info/index', array('url' => 'shop'))),
        array('label' => 'Продукты', 'url' => $this->createUrl('product/sections')),
      ),
    ),
    '<form class="navbar-search pull-left" action=""><input type="text" class="search-query span2" placeholder="Search"></form>',
    array(
      'class' => 'bootstrap.widgets.TbMenu',
      'htmlOptions' => array('class' => 'pull-right'),
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
          )
        ),
    ),
  ),
)); ?>