<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.orders
 */
class BOrderPaymentColumn extends BButtonColumn
{
  public $template = '{updatePaymentStatus} {capturePayment}';

  public function init()
  {
    $this->buttons['updatePaymentStatus'] = array(
      'label' => 'Обновить статус',
      'icon' => 'pencil',
      'options' => array(
        'class' => 'add',
      ),
    );

    $this->buttons['capturePayment'] = array(
      'label' => 'Провести клиринг',
      'icon' => 'pencil',
      'options' => array(
        'class' => 'copy',
      ),
    );
  }

  protected function renderButton($id, $button, $row, $data)
  {
    $button['options']['ajax'] = array(
      'url' => Yii::app()->controller->createUrl('/order/bOrder/'.$id, array('orderId' => $data->order_id)),
      'success' => 'function(){$("#'.$this->grid->id.'").yiiGridView("update");}',
    );

    return parent::renderButton($id, $button, $row, $data);
  }
}