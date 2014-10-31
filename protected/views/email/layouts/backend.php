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
  <body style="background: #fff; font-size: 18px; font-family: Arial; color: #373731; padding: 0; margin: 0">
    <table style="border-collapse: collapse; width: 1000px; margin: 0 auto; background: #fff; font-size: 17px; font-family: Arial; color: #373731">
      <tr>
        <td>
          <table style="width: 100%;">
            <tr>
              <td style="padding: 15px 0">

                <div style="font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 30px">
                  <?php echo $subject?>
                </div>

                <table width="98%" style="margin-bottom: 20px">
                  <tr>
                    <td nowrap="nowrap" valign="top" style="width: 150px">Дата события:</td>
                    <td width="99%"><?php echo date('Y.m.d'); ?></td>
                  </tr>
                  <tr>
                    <td nowrap="nowrap" valign="top">Время события:</td>
                    <td><?php echo date('H:i:s'); ?></td>
                  </tr>
                  <tr>
                    <td nowrap="nowrap" valign="top">IP адрес:</td>
                    <td><?php echo Yii::app()->request->userHostAddress; ?></td>
                  </tr>
                </table>

                <?php echo $content; ?>

                <?php if( isset($backendUrl) ) { ?>
                  <div style="margin-bottom: 20px">
                    <a href="<?php echo $backendUrl;?>">Урл в админе</a>
                  </div>
                <?php } ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>