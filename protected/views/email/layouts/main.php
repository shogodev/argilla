<!doctype html>
<?php
/**
 * @var CController $this
 *
 * @var Email $sender
 * @var string $subject
 * @var string $host
 * @var string $project
 * @var string $content
 * @var ContactField[] $emails
 * @var ContactField $email
 * @var ContactField[] $phones
 * @var ContactField $phone
 */
?>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title><?php echo $subject?></title>
  <style>
    * {
      margin: 0;
      padding: 0;
    }
  </style>
</head>
<body>

  <div style="background:#fffff;color:#373731;font-family:sans-serif;font-size:16px;line-height:120%;">
    <?php $this->renderPartial('frontend.views.email.layouts._header', $_data_)?>
      <div style="width:1000px;margin:auto;padding:40px 0 30px;">
        <?php echo $content; ?>
      </div>
    <?php $this->renderPartial('frontend.views.email.layouts._footer', $_data_)?>
  </div>

</body>
</html>