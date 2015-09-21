<?php
/**
 * @var FForm $form
 * @var UserController $this
 * @var array $_data_
 * @var FActiveDataProvider $orderDataProvider
 */
?>
<div class="wrapper">
  <div class="breadcrumbs-offset m25">
    <?php $this->renderPartial('/_breadcrumbs');?>
  </div>

  <h1 class="uppercase s33 m20"><?php echo Yii::app()->meta->setHeader('Мои социальные сети')?></h1>

  <div class="nofloat m50">
    <aside id="left" class="small-aside">
      <nav class="menu profile-menu">
        <ul>
          <li>
            <span class="caption">Мои заказы</span>
            <ul>
              <li><a href="">Мои заказы (45)</a></li>
            </ul>
          </li>
          <li>
            <span class="caption">Мои скидки и бонусы</span>
            <ul>
              <li><a href="">Скидки</a></li>
              <li><a href="">Накопление баллов (564 балла)</a></li>
              <li><a href="">Приведи друга</a></li>
            </ul>
          </li>
          <li>
            <span class="caption">Мой профиль</span>
            <ul>
              <li><a href="">Личные данные</a></li>
              <li><a href="">Рейтинг</a></li>
              <li><a href="">Мои социальные сети</a></li>
              <li><a href="">Адреса доставки</a></li>
              <li><a href="">Сменить пароль</a></li>
              <li><a href="">Оплата картой</a></li>
            </ul>
          </li>
        </ul>
      </nav>
    </aside>

    <section id="main" class="personal-page">
      <div class="profile-socials-block">
        <div class="s18 light center m60">
          Привяжите ваш аккаунт Avocado к социальным сетям и мы будем узнавать вас!
        </div>
        <div class="social-checks">
          <div class="check">
            <label for="social-fb" class="label fb"></label>
            <input type="checkbox" id="social-fb" />
          </div>
          <div class="check">
            <label for="social-vk" class="label vk"></label>
            <input type="checkbox" id="social-vk" />
          </div>
          <div class="check">
            <label for="social-twit" for="social-twit" class="label twit"></label>
            <input type="checkbox" id="social-twit" checked />
            <span class="name">
              Сиранодебержераков Константин
            </span>
          </div>
          <div class="check">
            <label for="social-gplus" class="label gplus"></label>
            <input type="checkbox" id="social-gplus" />
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
