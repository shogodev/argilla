<?php
/**
 * @var FForm $form
 * @var UserProfileController $this
 * @var array $_data_
 * @var array $socials
 */
?>
<div class="wrapper">
  <div class="breadcrumbs-offset m25">
    <?php $this->renderPartial('/_breadcrumbs');?>
  </div>

  <h1 class="uppercase s33 m20"><?php echo Yii::app()->meta->setHeader('Мои социальные сети')?></h1>

  <div class="nofloat m50">
    <?php $this->renderPartial('_menu', $_data_)?>

    <section id="main" class="personal-page">
      <div class="profile-socials-block">
        <div class="s18 light center m60">
          <?php echo $this->textBlockRegister('Привязка соц. сетей в ЛК', 'Привяжите ваш аккаунт '.Yii::app()->params->project.' к социальным сетям и мы будем узнавать вас!', null);?>
        </div>
        <div class="social-checks">
          <?php foreach($socials as $key => $social) {?>
            <div class="check">
              <label for="<?php echo 'social-'.$key;?>" class="label <?php echo $social['cssClass'];?>"></label>
              <?php echo CHtml::checkBox('social', !empty($social['related']), array(
                'id' => 'social-'.$key,
                'autocomplete' => 'off',
                'data-bind-url' => $social['bindUrl'],
                'disabled' => $social['disabled']
              ));?>
              <?php if( !empty($social['name']) ) {?>
                <span class="name">
                  <?php echo $social['name'];?>
                </span>
              <?php }?>
            </div>
          <?php }?>
        </div>
      </div>
    </section>

    <script language="JavaScript">
      $(function() {
        $('.check input').on('change', function(e) {
            location.href = $(this).data('bind-url');
        });
      });
    </script>
  </div>
</div>