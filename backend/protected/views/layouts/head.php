<head>
  <meta charset="utf-8" />
  <?php Yii::app()->bootstrap->register(); ?>
  <?php Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/st.css') ?>
  <?php Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/skin.css') ?>
  <?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.plugins/jquery.extender.js') ?>
  <?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/static.js') ?>
  <!--[if lt IE 9]>
  <script src="/js/html5msie/html5shiv-printshiv.js"></script>
  <script src="/js/html5msie/respond.js"></script>
  <![endif]-->

  <title><?php echo CHtml::encode($this->pageTitle); ?></title>

  <script>
  //<![CDATA[
    $('html').removeClass('no-js');
  //]]>
  </script>
</head>