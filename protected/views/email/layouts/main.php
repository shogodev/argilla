<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
/**
 * @var FController $this
 */
$host = Yii::app()->request->hostInfo
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=windows-1251" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <title>Письмо с сайта <?php echo Yii::app()->params->project?></title>
  <style type="text/css">
    * {
      font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;
      font-size: 100%;
      margin: 0;
      padding: 0; }
    .order-table th {
      font-weight: bold;
      background: #ccc;
      padding: 4px 10px;
      text-align: center; }
    .order-table td {
      padding: 3px 10px; }
    .order-table .total td {
      padding-top: 10px; }

    .invoice { padding: 3px; width: 181mm; background: #fff; }
    .invoice * { font-family: Arial; }
    .invoice .ramka { border-top: black 1px dashed; border-bottom: black 1px dashed; border-left: black 1px dashed; border-right: black 1px dashed; margin: 0 auto; height: 145mm; }
    .invoice .kassir { font-weight: bold; font-size: 10pt; font-family: "Times New Roman", serif; padding: 7mm 0 7mm 0; text-align: center; }
    .invoice .cell { font-family: Arial, sans-serif; border-left: black 1px solid; border-bottom: black 1px solid; border-top: black 1px solid; font-weight: bold; font-size: 8pt; line-height: 1.1; height: 4mm; vertical-align: bottom; text-align: center; }
    .invoice .cells { border-right: black 1px solid; width: 100%; }
    .invoice .subscript { font-size: 6pt; font-family: "Times New Roman", serif; line-height: 1; vertical-align: top; text-align: center; }
    .invoice .string, .invoice .dstring { font-weight: bold; font-size: 8pt; font-family: Arial, sans-serif; border-bottom: black 1px solid; text-align: center; vertical-align: bottom; }
    .invoice .dstring { font-size: 9pt; letter-spacing: 1pt; }
    .invoice .floor { vertical-align: bottom; padding-top: 0.5mm; }
    .invoice .stext { font-size: 8.5pt; font-family: "Times New Roman", serif; vertical-align: bottom; }
    .invoice .stext7 { font-size: 7.5pt; font-family: "Times New Roman", serif; vertical-align: bottom; }
  </style>
</head>

<body style="font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size: 14px; line-height: 120%; color: #565656; background: #fff;">

<table cellpadding="0" cellspacing="0" width="680" align="center" style="border-collapse:collapse; margin: 0 auto; background: #f9f9f9">
  <tr>
    <td colspan="2" align="left" style="height: 157px">
      <img src="<?php echo $host;?>/i/logo.png" style="padding-left: 32px" />
    </td>
  </tr>

  <tr style="border-bottom: 1px dashed #ccc">
    <td colspan="2" align="left" style="padding: 30px 32px">
      <?php echo $content; ?>
    </td>
  </tr>

  <tr>
    <td width="55%"></td>
    <td align="left" style="padding: 30px 32px 50px">
      <br /><br /><br /><br /><br />
      <span style="font-size: 14px">C наилучшими пожеланиями, <?php echo Yii::app()->params->project?></span><br />
      <br /><br />
      <?php if( $this->asa('common') ) {?>
        <?php if( $contact = $this->getHeaderContacts() ) {?>
          <?php $phones = $contact->getFields('phones');?>
          <?php foreach($phones as $phone) {?>
            <span style="font-size: 14px"> <?php echo $phone->value.' '.$phone->description; ?> </span><br />
          <?php }?>
        <?php } ?>
      <?php } ?>
      <span style="font-size: 14px"><a href="<?php echo $host;?>"><?php echo $host;?></a></span>
    </td>
  </tr>
</table>

</body>
</html>