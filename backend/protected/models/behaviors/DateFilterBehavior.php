<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.models.behaviors
 *
 * Поведение реализует фильтрацию по периоду дат
 *
 * Пример подключения поведения:
 * <pre>
 *  'fateFilterBehavior' => array(
 *    'class' => 'DateFilterBehavior',
 *    'attribute' => 'date',
 *    'defaultNow' => true
 *  )
 * </pre>
 *
 * Пример подключения фильтра в BGridView
 * <pre>
 *  $this->widget('BGridView', array(
 *    'filter' => $model,
 *    'dataProvider' => $dataProvider,
 *       'columns' => array(
 *         array('name' => 'id', 'class' => 'BPkColumn'),
 *         array('name' => 'date', 'class' => 'BDatePickerColumn'),
 *  ...
 * </pre>
 */
class DateFilterBehavior extends DateFormatBehavior
{
  /**
   * @var string
   */
  public $date_from;

  /**
   * @var string
   */
  public $date_to;

  public function init()
  {
    parent::init();

    $validator = new CSafeValidator();
    $validator->attributes = array('date_from', 'date_to');
    $validator->on = array('search' => 'search');
    $this->owner->validatorList->add($validator);

    $this->owner->attachEventHandler('onBeforeSearch', array($this, 'beforeSearch'));
  }

  public function beforeSearch(CEvent $event)
  {
    /**
     * @var CDbCriteria $criteria
     */
    $criteria = $event->params['criteria'];

    if( !empty($this->date_from) || !empty($this->date_to) )
      $criteria->addBetweenCondition('date', Utils::dayBegin($this->date_from), Utils::dayEnd($this->date_to));

    return $criteria;
  }
}