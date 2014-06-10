<?php
/**
 * @var FForm $registrationForm
 * @var UserController $this
 */
?>
<?php $menu = array(
  array(
    'label' => '<span class="accordion-menu-heading icon-my-profile">Мой профиль</span>',
    'url' => '',
    'items' => array(
      array(
        'label' => 'Личные данные',
        'url' => array('user/data')
      ),
      array(
        'label' => 'Сменить пароль',
        'url' => array('user/changePassword')
      )
    )
  ),
  array(
    'label' => '<span class="accordion-menu-heading icon-my-orders">Мои заказы</span>',
    'url' => '',
    'items' => array(
      array(
        'label' => 'Текущие заказы',
        'url' => array('user/currentOrders')
      ),
      array(
        'label' => 'История заказов',
        'url' => array('user/history')
      )
    )
  ),
  array(
    'label' => '<span class="accordion-menu-heading icon-my-baskets">Сохраненные корзины</span>',
    'url' => '',
    'items' => array(
      array(
        'label' => 'Мои корзины',
        'url' => array('user/sharedBasket')
      )
    )
  )
);?>

<div class="fl menu accordion-menu profile-menu vertical-menu">
  <?php $this->widget('FMenu', array(
    'items' => $menu,
    'encodeLabel' => false,
    'hideEmptyItems' => false,
    'activateParents' => true
  ))?>
</div>