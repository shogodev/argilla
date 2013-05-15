<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 *
 * @property ProductFilterRender $render
 * @method public __construct($filterKey = 'productFilter', $saveState = true, $setStateAuto = true)
 */
class AbstractProductFilter extends CComponent
{
  /**
   * @var ProductFilterRender
   */
  protected $render;

  protected $filterKey;

  /**
   * @var array Текущее состояние фильтра
   */
  protected $state = array();

  protected $saveState;

  /**
   * @param string $filterKey - уникальный ключ фильтра
   * @param bool $saveState - сохраняет состояние в сессию, также считывает оттуда
   * @param bool $setStateAuto - ищет состояние фильтра в POST и GET запросе по $filterKey
   */
  public function __construct($filterKey = 'productFilter', $saveState = true, $setStateAuto = true)
  {
    $this->filterKey = $filterKey;
    $this->saveState = $saveState;

    if( $this->saveState )
      $this->setState($this->loadStateFromSession());

    $state = Yii::app()->request->getParam($this->filterKey, array());

    if( Arr::cut($state, 'remove', false) )
    {
      foreach($state as $elementId => $value)
      {
        if( !is_array($value) )
          $this->removeElementState($elementId);
        else
        {
          foreach($value as $key => $item)
            $this->removeElementState($elementId, $key);
        }
      }
    }

    if( $setStateAuto && Arr::cut($state, 'submit', false) )
      $this->setState($state);

    $this->render = new ProductFilterRender($this);
  }

  public function getFilterKey()
  {
    return $this->filterKey;
  }

  /**
   * Устанавливает полностью новое состояние фильтра
   * @param array $state
   */
  public function setState(array $state)
  {
    $this->state = $this->clearEmptyState($state);

    if( $this->saveState )
      $this->saveStateInSession($this->state);
  }

  /**
   * @return array
   */
  public function getState()
  {
    return $this->state;
  }

  /**
   * Устанавливает состояния фильтра только для переданных элементов
   */
  public function setStatePartial(array $state)
  {
    $newState = Arr::mergeAssoc($this->getState(), $state);
    $this->setState($newState);
  }

  public function getRender()
  {
    return $this->render;
  }

  protected function loadStateFromSession()
  {
    return Arr::get(Yii::app()->session, $this->filterKey, array());
  }

  protected function saveStateInSession($state)
  {
    Yii::app()->session[$this->filterKey] = $state;
  }

  protected function clearEmptyState($state)
  {
    foreach($state as $key => $item)
      if( empty($item) && $item !== '0' )
        unset($state[$key]);

    return $state;
  }

  protected function removeElementState($elementId, $value = null)
  {
    $state = $this->getState();

    if( !isset($state[$elementId]) )
      return;

    if( $value !== null && !isset($state[$elementId][$value]) )
      return;

    if( $value !== null )
      unset($state[$elementId][$value]);
    else
      unset($state[$elementId]);

    $this->setState($state);
  }
}