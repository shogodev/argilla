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
</td>
</tr>
</table>
</td>
</tr>
</table>

<div style="background: #f2f2f2; padding: 10px 0 20px">
  <table style="border-collapse: collapse; width: 1000px; margin: 0 auto">
    <tr>
      <td style="font-size: 14px; line-height: 150%">
        Если вам необходима помощь или у вас возникли вопросы по вашему заказу, напишите нам на <?php if($email) echo '<a href="'.$email.'" style="color: #949494">'.$email.'</a>'?><br />
        или позвоните по телефону:
        <div style="font-size: 18px">
          <?php foreach($phones as $phone) {?>
            <span style="color: #f58220"><?php echo $phone->value?></span> <?php echo $phone->description?><br />
          <?php }?>
        </div>
        Пожалуйста, не забудьте указать в письме номер вашего заказа.
      </td>
    </tr>
  </table>
</div>

</body>