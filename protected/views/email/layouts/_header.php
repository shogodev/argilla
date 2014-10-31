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
<body style="background: #fff; font-size: 18px; font-family: Arial; color: #373731; padding: 0; margin: 0">
<div style="background: url(<?php echo $host?>/i/mail/head-bg.png) repeat-x; height: 144px;">
  <table style="border-collapse: collapse; width: 1000px; margin: 0 auto">
    <tr>
      <td style="padding: 15px 0">
        <a href="<?php echo $host?>" target="_blank">
          <img src="<?php echo $host?>/i/mail/logo.png" alt="магазин комфорта RelaxMarket" />
        </a>
      </td>
      <td style="text-align: right">
        <div style="font-size: 33px">
          <?php foreach($phones as $phone) {?>
            <span style="color: #f58220"><?php echo $phone->value?></span> <?php echo $phone->description?><br />
          <?php }?>
        </div>
      </td>
    </tr>
  </table>
</div>

<table style="border-collapse: collapse; width: 1000px; margin: 0 auto; background: #fff; font-size: 17px; font-family: Arial; color: #373731">
  <tr>
    <td>
      <table style="width: 100%;">
        <tr>
          <td style="padding: 15px 0">