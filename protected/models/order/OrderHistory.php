<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 *
 * @method static OrderHistory model(string $className = __CLASS__)
 */
class OrderHistory extends Order
{
  /**
   * @var string
   */
  public $historyUrl;

  public function tableName()
  {
    return '{{order}}';
  }

  public function defaultScope()
  {
    return array(
      'order' => 'id DESC',
      'condition' => 'user_id = :userId',
      'params' => array('userId' => Yii::app()->user->id)
    );
  }

  public function afterFind()
  {
    parent::afterFind();

    $this->historyUrl = Yii::app()->controller->createUrl('user/historyOne', array('id' => $this->id));
  }

  public function getFilterKeys($userId)
  {
    $data = array();

    $mounts = Yii::app()->db->createCommand()
      ->selectDistinct("DATE_FORMAT(date_create, '%m') AS mouth, DATE_FORMAT(date_create, '%Y') AS year")
      ->from($this->tableName())
      ->where("user_id = :user_id AND (deleted IS NULL OR deleted = 0)", array(':user_id' => $userId))
      ->queryAll();

    foreach($mounts as $value)
    {
      $data[] = array(
        'id'   => $value['year'].$value['mouth'],
        'name' => Yii::app()->locale->getMonthName(intval($value['mouth']), 'wide', true).' '.$value['year'],
      );
    }

    return $data;
  }

  public function getFilteredOrders($userId, $filter)
  {
    $criteria = new CDbCriteria;
    $criteria->condition = "DATE_FORMAT(date_create, '%Y%m') = :filter AND user_id = :user_id AND (deleted IS NULL OR deleted = 0)";
    $criteria->params    = array(':filter' => $filter, ':user_id' => $userId);

    return $this->findAll($criteria);
  }

  public function renderFilter($filterKeys, $htmlOptions = array())
  {
    $event   = 'change';
    $id      = 'filerDate';
    $handler = "location.href='".Yii::app()->controller->createUrl('user/history', array('filter' => ''))."' + $(this).val()";

    $cs = Yii::app()->getClientScript();
    $cs->registerCoreScript('jquery');
    $cs->registerCoreScript('yii');
    $cs->registerScript('Yii.CHtml.#filerDate' . $id, "$('#$id').on('$event', function(){{$handler}});");

    return CHtml::dropDownList('filerDate',
      isset($_GET['filter']) ? $_GET['filter'] : '',
      CHtml::listData($filterKeys, 'id', 'name'),
      $htmlOptions);
  }
}