<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */

/**
 * Class FilterState
 */
class FilterState extends CComponent
{
  /**
   * @var array
   */
  private $state = array();

  /**
   * @var string
   */
  private $filterKey;

  /**
   * @var bool
   */
  private $useSession;

  /**
   * @var bool
   */
  private $amountOnly = false;

  /**
   * @param string $filterKey
   * @param bool $useSession
   */
  public function __construct($filterKey, $useSession = true)
  {
    $this->filterKey = $filterKey;
    $this->useSession = $useSession;

    if( $this->useSession )
    {
      $this->load();
    }
  }

  /**
   * @param $offset
   *
   * @return bool
   */
  public function offsetExists($offset)
  {
    return isset($this->state[$offset]);
  }

  /**
   * @param $offset
   *
   * @return null
   */
  public function offsetGet($offset)
  {
    return $this->offsetExists($offset) ? $this->state[$offset] : null;
  }

  /**
   * @param array $state
   */
  public function setState(array $state)
  {
    $this->state = $state;
    $this->formatState($state);

    if( $this->useSession )
    {
      $this->save();
    }
  }

  /**
   * @param array $state
   */
  public function append(array $state)
  {
    $this->setState(Arr::mergeAssoc($this->state, $state));
  }

  /**
   * @return array
   */
  public function load()
  {
    $this->setState(Arr::get(Yii::app()->session, $this->filterKey, array()));
    return $this->state;
  }

  public function save()
  {
    Yii::app()->session[$this->filterKey] = $this->state;
  }

  /**
   * @param array $state
   */
  public function remove(array $state)
  {
    $this->setState(array_diff_assoc($state, $this->state));
  }

  /**
   * @return bool
   */
  public function isAmountOnly()
  {
    return $this->amountOnly;
  }

  public function isEmpty()
  {
    return empty($this->state);
  }

  public function isSelected($elementId = null, $itemId = null)
  {
    if( !isset($elementId) && !isset($itemId) )
    {
      return !empty($this->state);
    }
    elseif( !isset($itemId) )
    {
      return isset($this->state[$elementId]);
    }
    else
    {
      return isset($this->state[$elementId][$itemId]);
    }
  }

  /**
   * @param array $request
   */
  public function processState(array $request)
  {
    if( $submit = Arr::cut($request, 'submit') )
    {
      if( $submit === 'amount' )
      {
        $this->useSession = false;
        $this->amountOnly = true;
      }

      $this->setState($request);
    }
  }

  private function formatState()
  {
    foreach($this->state as $parameterId => $values)
    {
      if( empty($values) && $values !== '0' )
      {
        unset($this->state[$parameterId]);
      }
      elseif( !is_array($values) )
      {
        $this->state[$parameterId] = array($values => $values);
      }
      elseif( is_array($values) )
      {
        $this->state[$parameterId] = array();

        foreach($values as $value)
        {
          $this->state[$parameterId][$value] = $value;
        }
      }
    }
  }
}