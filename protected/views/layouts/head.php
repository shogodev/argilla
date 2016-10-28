<head>
  <meta charset="utf-8" />
  <title><?php echo Yii::app()->meta->title;?></title>
  <?php echo Yii::app()->meta->custom;?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <base href="<?php echo Yii::app()->homeUrl?>" />

  <?php echo CHtml::linkTag("canonical", null, Yii::app()->controller->getCanonicalUrl());?>

  <!--[if lt IE 9]>
    <script src="js/html5msie/html5shiv-printshiv.js"></script>
    <script src="js/html5msie/respond.js"></script>
  <![endif]-->

  <?php echo CHtml::linkTag("stylesheet", null, Yii::app()->assetManager->publish(GlobalConfig::instance()->rootPath.'/i/style').'/css/st.css');?>

  <script>
    (function() { "use strict";
      window.IS_MOBILE = navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i) != null;
      window.IS_DESKTOP = !IS_MOBILE;
      window.IS_IOS = (navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false);
      window.IS_IE = navigator.appVersion.indexOf("MSIE") !== -1 || navigator.userAgent.match(/Trident.*rv[ :]*11\./);
      window.IS_TOUCH_DEVICE = false;
      if ('ontouchend' in document || !!navigator.msMaxTouchPoints || !!navigator.maxTouchPoints) {
        window.IS_TOUCH_DEVICE = true;
      }

      var HTML = document.documentElement;

      HTML.classList.remove("no-js");

      if (IS_MOBILE) HTML.classList.add("is-mobile");
      if (IS_DESKTOP) HTML.classList.add("is-desktop");
      if (IS_IOS) HTML.classList.add("is-ios");
      if (IS_IE) HTML.classList.add("is-ie");
      if (IS_TOUCH_DEVICE) HTML.classList.add("is-touch-device");
    })();
  </script>
</head>