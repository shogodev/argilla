<?php
/**
 * @author Kolobkov Alexander <kolobkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @var $order_id int
 *
 */
?>
<tr>
  <th>
    Система оплаты платрон:
  </th>
  <td>
    <?php $platronModel = new PlatronSystem($order_id) ?>
    <?php if( ($platronModel->getPaymentStatus()) && $platronModel->getPaymentStatus()['pg_status'] != 'error' ) { ?>
      <?php echo BOrderStatus::model()->getPlatronStatus($platronModel->getPaymentStatus()['pg_transaction_status'])?> <br />
    <?php if( $platronModel->getPaymentStatus()['pg_status'] == 'ok' && (!$model->capture) ) { ?>
      <form action="" name="Capture" method="post">
        <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id?>">
        <input type="submit" id="capture_submit" name="submit" value="Провести клиринг">
      </form>
      <script type="text/javascript">
        var finish = function() {
          location.reload();
        };

        $('#capture_submit').click( function(e)
        {
          e.preventDefault();
          var order_id = $('#order_id').val();
          $.post('<?php echo Yii::app()->request->baseUrl?>/order/bOrder/capture/', 'action=get_capture&id='+order_id, finish, 'json');
        });
      </script>
    <?php } ?>
    <?php } else { ?>
      Не оплачен
    <?php } ?>
  </td>
</tr>