<head>
  <meta charset="utf-8" />
  <title><?php echo Yii::app()->meta->title;?></title>
  <?php echo Yii::app()->meta->custom;?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <base href="<?php echo Yii::app()->homeUrl?>" />

  <?php echo CHtml::linkTag("canonical", null, Yii::app()->controller->getCanonicalUrl());?>

  <?php echo CHtml::linkTag("stylesheet", null, Yii::app()->assetManager->publish(GlobalConfig::instance()->rootPath.'/i/style').'/css/st.css');?>
</head>
