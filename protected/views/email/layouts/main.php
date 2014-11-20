<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=windows-1251" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <title><?php echo $subject?></title>
</head>
<?php $this->renderPartial('frontend.views.email.layouts._header', $_data_)?>
  <?php echo $content; ?>
<?php $this->renderPartial('frontend.views.email.layouts._footer', $_data_)?>
</html>