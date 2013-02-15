<?php
/**
 * User: Nikita Melnikov <melnikov@shogo.ru>
 * Date: 1/5/13
 *
 * @var User $user
 * @var UserMessage[] $messages
 * @var UserController $this
 * @var FForm $form
 */
?>
<div class="wrap-info">
  <?php $this->renderPartial('//breadcrumbs');?>
</div>
<div class="wrap">
  <div class="container container_16 nofloat">
    <h1 class="h3">Профиль пользователя</h1>

    <?php $this->renderPartial('_private_messages', $_data_)?>


    <div class="float-none">

      <?php if( $user->avatarExists() ):?>
        <div class="avatar ib left">
          <img alt="" src="<?php echo $user->getAvatar();?>" />
        </div>
      <?php endif;?>

      <h4 class="m3"><?php echo $user->user->name;?> <?php echo $user->getDecoratedCityString();?></h4>
      <div class="m15">

        <?php if( !empty($user->user->address) ):?>
        <div class="m5">
          <b>Адрес:</b>
          <?php echo $user->user->address;?>
        </div>
        <?php endif;?>
      </div>
    </div>
    <div class="float-none">
      <div class="m15">
        <?php if( !empty($user->user->bicycle) ):?>
        <div class="m5">
          <b>Марка велосипеда:</b>
          <?php echo $user->user->bicycle;?>
        </div>
        <?php endif;?>
        <?php if( !empty($user->user->interests) ):?>
        <div class="m5">
          <b>Интересы:</b>
          <?php echo $user->user->interests;?>
        </div>
        <?php endif;?>
      </div>
      <a class="btn btn-red m20" href="<?php echo $this->createUrl('user/data');?>">Изменить данные</a>

      <?php if( !$user->isActivated() ):?>
      <form class="form mobile-form m20">
        <div class="form-hint form-hint-top">
          <h4>Привязка аккаунта к номеру мобильного телефона</h4>
          Система привязки очень просто активируется и, кроме того, дает возможность принимать участие в голосованиях и конкурсах.
        </div>
        <div class="text-container">
          <label class="required" for="user_attached_phone">
            Номер телефона
          </label>
          <div class="pdb">
            <?php echo UserPhoneAssignment::model()->getRequestInput();?>
          </div>
          <span class="s12 italic" style="margin-left:10px">Пример, +79209202020</span>
        </div>
        <div class="form-submit m20">
          <?php echo UserPhoneAssignment::model()->getRequestButton();?>
        </div>
        <div class="text-container">
          <label class="required" for="user_attached_phone_code">Введите код из СМС</label>
          <div class="pdb">
            <?php echo UserPhoneAssignment::model()->getActivateInput();?>
          </div>
        </div>
        <div class="form-submit">
          <?php echo UserPhoneAssignment::model()->getActivateButton();?>
        </div>
      </form>
      <?php else:?>
      <form class="form mobile-form m20">
        <div class="form-hint form-hint-top form-hint-bottom">
          <h4>Привязка аккаунта к номеру мобильного телефона</h4>
          Ваш аккаунт привязан к телефону <span class="bb s16" style="padding-left:20px"><?php echo $user->phoneAssignment->maskPhone();?></span>
        </div>
      </form>
      <?php endif;?>

      <h4>Фотогалерея</h4>
      <?php if( !empty($user->images)  ):?>
      <div id="user-gallery" class="nofloat user-gallery">
        <?php foreach( $user->images as $image ):?>
        <?php if( !$image->image ) continue;?>
        <div class="left">
          <div>
            <a href="<?php echo $image?>" class="jqfancybox" rel="fancybox">
              <img src="<?php echo $image?>" alt="" height="150" />
            </a>
          </div>
          <div class="center">
            <a href="<?php echo $image->getDeleteFileUrl();?>" class="grey s12 delete-image">удалить</a>
          </div>
        </div>
        <?php endforeach;?>
      </div>
      <?php endif;?>
      <br>

      <?php echo $form->render();?>

    </div>
  </div>
</div>
<script>
  $(function(){
    $('form.userUploadImage input[type=submit]').on('click', function(e)
    {
      if( !$('#UserImage_image_file_wrap_list a').length )
      {
        e.preventDefault();
        alert('Выберите файл для загрузки.');
      }
    });

    $('.delete-image').on('click', function(e){
      e.preventDefault();

      var url  = $(this).attr('href');
      var self = $(this);

      var callback = function( resp ) {
        if( resp.status == 'error' ) {
          alert(resp.message);
        }
        else {
          $(self).parents('.left').fadeOut();
        }
      };

      $.post(url, {}, callback);
    });
  });
</script>