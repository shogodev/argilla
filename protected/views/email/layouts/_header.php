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
<!-- header -->
<div style="width:100%;background:#031d38;padding:30px 0 28px;">

  <table style="margin:auto;" cellpadding="0" cellspacing="0" width="1000" align="center">
    <tr>
      <td style="vertical-align:middle;text-align:left;">
        <a style="color:#fff;text-decoration:none;" href="">
          <img src="<?php echo $host?>/i/email/logo.png" alt="<?php echo Utils::ucfirst($project);?>" />
        </a>
      </td>
      <td style="vertical-align:middle;text-align:right;">
        <img src="/i/email/icon-phone.png" alt="" style="margin-right: 10px" />
        <?php foreach($phones as $phone) {?>
          <a href="tel:<?php echo $phone->getClearPhone();?>" style="text-decoration:none;color:#fff;font-size: 36px;"><span style="color:#c92128; line-height: 120%"><?php echo $phone->value?></span> <?php echo $phone->description?></a><br />
        <?php }?>
      </td>
    </tr>
  </table>

</div>
<!-- /header -->