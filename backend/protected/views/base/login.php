<?php Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/i/reject/jquery.reject.css') ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.plugins/jquery.reject.min.js') ?>

<?php
$this->pageTitle   = Yii::app()->name.' - Авторизация';
$this->breadcrumbs = array(
  'Авторизация',
);
?>

<section class="s-form-auth row-fluid">
  <div class="span4"></div>
  <div class="span4">

    <h1>Авторизация</h1>

    <p>Для входа в систему управления нужно ввести ваш личный логин и пароль:</p>

    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id' => 'verticalForm',
      'htmlOptions' => array('class' => 'well'),
      'enableClientValidation' => true,
      'clientOptions' => array(
        'validateOnSubmit' => true,
      ),
    )); ?>

    <p class="note">Поля, отмеченные знаком <span class="required">*</span>, обязательны к заполнению.</p>
    <?php echo $form->textFieldRow($model, 'username', array('class' => 'span10')); ?>
    <?php echo $form->passwordFieldRow($model, 'password', array('class' => 'span10')); ?>
    <?php echo $form->checkboxRow($model, 'rememberMe'); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType' => 'submit', 'label' => 'Войти', 'htmlOptions' => array('style' => 'margin-top:5px'))); ?>
    <?php $this->endWidget(); ?>

  </div>
  <div class="span4"></div>
</section>

<script>
//<![CDATA

  $(function () {
    $('#LoginForm_username').focus();
  });

  // Проверяем браузер
  $(function () {
    $.reject({
      reject:{
        all:false,
        msie5:true, msie6:true, msie7:true, msie8:true,
        firefox1:true, firefox2:true, firefox3:true, firefox4:true, firefox5:true, firefox6:true, firefox7:true, firefox8:true, firefox9:true,
        firefox10:true, firefox11:true, firefox12:true, firefox13:true, firefox14:true, firefox15:true,
        konqueror:true, /* ??? */
        chrome1:true, chrome2:true, chrome3:true, chrome4:true, chrome5:true, chrome6:true, chrome7:true, chrome8:true, chrome9:true, chrome10:true,
        chrome11:true, chrome12:true, chrome13:true, chrome14:true, chrome15:true, chrome16:true, chrome17:true, chrome18:true, chrome19:true,
        chrome20:true, chrome21:true,
        safari2:true, safari3:true, safari4:true,
        opera7:true, opera8:true, opera9:true, opera10:true
      },
      display:['firefox', 'opera', 'msie', 'chrome', 'safari'],
      browserInfo:{
        firefox:{
          text:'Firefox 16+'
        },
        safari:{
          text:'Safari 5+'
        },
        opera:{
          text:'Opera 12+'
        },
        chrome:{
          text:'Chrome 22+'
        },
        msie:{
          text:'Internet Explorer 9+',
          url:'http://www.microsoft.com/rus/windows/internet-explorer/'
        }
      },
      imagePath:'<?php echo Yii::app()->request->baseUrl ?>/i/reject/',
      closeCookie:true,

      header:'Ваш браузер устарел!',
      paragraph1:'Возможно, он не полностью совместим с этой системой управления.',
      paragraph2:'Пожалуйста, установите один из браузеров, представленных ниже:',
      closeLink:'Закрыть окно (ESC)',
      closeMessage:'Корректная работа в текущем браузере не гарантируется!'
    });
  });
//]]>
</script>