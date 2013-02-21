<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.models
 *
 * @method static BOrder model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $user_id
 * @property string  $name
 * @property string  $email
 * @property string  $address
 * @property string  $comment
 * @property string  $type
 * @property string  $sum
 * @property integer $ip
 * @property string $date_create
 * @property string $status
 * @property string $order_comment
 * @property integer $deleted
 */
class BOrder extends BActiveRecord
{
  const STATUS_NEW       = 'new';
  const STATUS_CONFIRMED = 'confirmed';
  const STATUS_CANCELED  = 'canceled';

  const TYPE_FAST   = 'fast';
  const TYPE_NORMAL = 'normal';

  public $statusLabel = array(
    self::STATUS_NEW       => 'Новый',
    self::STATUS_CONFIRMED => 'Подтвержден',
    self::STATUS_CANCELED  => 'Отменен',
  );

  public $typeLabel = array(
    self::TYPE_FAST   => 'Быстрый',
    self::TYPE_NORMAL => 'Обычный',
  );

  public $date_create_from;

  public $date_create_to;

  public function rules()
  {
    return array(
      array('name', 'required'),
      array('email', 'email'),
      array('address, comment', 'safe'),
      array('status', 'in', 'range' => array(self::STATUS_NEW, self::STATUS_CONFIRMED, self::STATUS_CANCELED)),
      array('order_comment', 'safe'),

      array('id, type, sum, date_create_from, date_create_to', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'name' => 'Имя',
      'date_create_from' => 'Дата с...',
      'date_create_to' => 'по ...'
    ));
  }

  public function defaultScope()
  {
    return array(
      'condition' => 'deleted = 0',
      'order' => 'date_create DESC'
    );
  }

  public function relations()
  {
    return array('products' => array(self::HAS_MANY, 'BOrderProduct', 'order_id'));
  }

  public function changeStatus($status, $orderComment)
  {
    $this->status        = $status;
    $this->order_comment = $orderComment;
    if( $this->save() )
      return true;

    return $this->getErrors();
  }

  public function renderButtonConfirm($controller)
  {
    $ajaxOptions = array(
      'type'       => 'POST',
      'beforeSend' => "function() {return confirm('Вы уверены что хотите принять заказ?')}",
      'success'    => "function(resp) {if(resp == 'ok') location.reload(); else alert(resp)}",
      'data'       => new CJavaScriptExpression("{id : '{$this->id}', status : '".self::STATUS_CONFIRMED."', order_comment : $('#order_comment').val()}")
    );

    $controller->widget('bootstrap.widgets.TbButton', array(
      'ajaxOptions' => $ajaxOptions,
      'buttonType'  => TbButton::BUTTON_AJAXBUTTON,
      'label'       => 'Принять',
      'url'         => array('changeStatus'),
      'type'        => 'primary',
    ));
  }

  public function  renderButtonCancel($controller)
  {
    $ajaxOptions = array(
      'type'       => 'POST',
      'beforeSend' => "function() {if($('#order_comment').val() == '') {alert('Укажите причину отмены!'); return false;}}",
      'success'    => "function(resp) {if(resp == 'ok') location.reload(); else alert(resp)}",
      'data'       => new CJavaScriptExpression("{id : '{$this->id}', status : '".self::STATUS_CANCELED."', order_comment : $('#order_comment').val()}")
    );

    $controller->widget('bootstrap.widgets.TbButton', array(
      'ajaxOptions' => $ajaxOptions,
      'buttonType'  => TbButton::BUTTON_AJAXBUTTON,
      'label'       => 'Отменить',
      'url'         => array('changeStatus'),
      'type'        => 'warning',
    ));
  }

  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('id', '='.$this->id);
    $criteria->compare('status', '='.$this->status);
    $criteria->compare('type', '='.$this->type);

    $criteria->addSearchCondition('name', $this->name);
    $criteria->addSearchCondition('email', $this->email);
    $criteria->addSearchCondition('sum', $this->sum);

    if( !empty($this->date_create_from) || !empty($this->date_create_to) )
      $criteria->addBetweenCondition('date_create', Utils::dayBegin($this->date_create_from), Utils::dayEnd($this->date_create_to));

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}