<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 *
 * @property string $label
 * @property string $name
 * @property string $cssId
 * @property string $url
 * @property string $image
 * @property ProductFilter $filter
 */
class ProductFilterElementItem extends CComponent
{
  const UNDEFINED_NAME = 'Не определено';

  public $id;

  public $selected = false;

  public $amount = 0;

  protected $label;

  protected $url;

  /**
   * @var $parent ProductFilterElement
   */
  protected $parent;

  public function getFilter()
  {
    return $this->parent->parent;
  }

  public function getLabel()
  {
    if( !empty($this->label) )
      return $this->label;
    else
    {
      return isset($this->parent->itemLabels[$this->id]) ? $this->parent->itemLabels[$this->id] : self::UNDEFINED_NAME;
    }
  }

  public function setLabel($label)
  {
    $this->label = $label;
  }

  public function setParent($parent)
  {
    $this->parent = $parent;
  }

  public function getParent()
  {
    return $this->parent;
  }

  public function getName()
  {
    if( !$this->parent->isMultiple() )
      return $this->parent->name;

    return !empty($this->parent->name) ? $this->parent->name.'['.$this->id.']' : '';
  }

  public function getCssId()
  {
    $cssId = !empty($this->parent->name) ? $this->parent->name.'['.$this->id.']' : '';

    return CHtml::getIdByName(str_replace('.', '_', $cssId));
  }

  public function isSelected()
  {
    if( $this->parent instanceof ProductFilterElement )
      return $this->parent->isSelectedItems($this->id);

    return false;
  }

  public function isDisabled()
  {
    return isset($this->parent->disabled[$this->id]);
  }

  public function getUrl()
  {
    if( $this->url === null )
    {
      $this->url = $this->buildUrl();
    }

    return $this->url;
  }

  public function setUrl($url)
  {
    $this->url = $url;
  }

  public function getImage()
  {
    return new FSingleImage($this->parent->id.'_'.$this->id.'.png', 'upload/images/color');
  }

  /**
   * @return string
   */
  protected function buildUrl()
  {
    $curUrl = parse_url(Yii::app()->controller->getCurrentUrl());
    $path   = explode("/", $curUrl['path']);

    if( !is_array($this->filter->urlPattern) )
      $this->filter->urlPattern = explode("/", $this->filter->urlPattern);

    $state = Arr::mergeAssoc($this->filter->state, $this->getItemState($this));

    if( $this->isSelected() && $this->parent->type ==! 'select' )
    {
      $this->parent->isMultiple() ? Arr::cut($state[$this->parent->id], $this->id) : Arr::cut($state, $this->parent->id);
      unset($path[array_search('{'.$this->parent->id.'}', $this->filter->urlPattern)]);
    }

    foreach($this->filter->elements as $element)
    {
      if( $element->isUrlDependence() )
      {
        list($state, $path) = $this->pathToFilterMode($state, $path, $element);
        list($state, $path) = $this->pathToUrlMode($state, $path, $element);
      }
    }

    if( !empty($state) )
    {
      $state['submit'] = 1;
    }

    $path = preg_replace("/\/+/", "/", implode('/', $path));
    $url  = Utils::buildUrl(array(
      'path' => $path,
      'query' => http_build_query(array($this->filter->filterKey => $state))
    ));

    return $url;
  }

  /**
   * @param array $state
   * @param array $path
   * @param ProductFilterElement $element
   *
   * @return array
   */
  protected function pathToFilterMode($state, $path, $element)
  {
    $state = $this->sortUrlState($state);
    $key   = Arr::search('{'.$element->id.'}', $this->filter->urlPattern);
    $value = Arr::get($path, $key);
    $id    = array_search($value, $element->itemUrls);

    if( $key && !isset($path[$key]) )
    {
      $path[$key] = '';
    }

    if( $element->isMultiple() )
    {
      if( isset($this->filter->state[$element->id][$id]) )
        $state = Arr::mergeAssoc($this->getItemState($element->items[$id]), $state);
    }
    else
    {
      if( isset($this->filter->state[$element->id]) && $id )
        $state = Arr::mergeAssoc($this->getItemState($element->items[$id]), $state);
    }

    return array($state, $path);
  }

  /**
   * Удаляем из ссылки значение фильтра из get параметра и помещяем значение
   * в саму ссылку в соответствие с urlPattern
   *
   * @param array $state
   * @param array $path
   * @param ProductFilterElement $element
   *
   * @return array
   */
  protected function pathToUrlMode($state, $path, $element)
  {
    $state = $this->sortUrlState($state);
    $key   = $element->id;
    $value = Arr::get($state, $key);

    if( is_array($value) )
    {
      $id = key($value);
      unset($state[$key][$id]);

      if( empty($state[$key]) )
        unset($state[$key]);
    }
    else
    {
      $id = $value;
      unset($state[$key]);
    }

    $pos = Arr::search('{'.$key.'}', $this->filter->urlPattern);
    $url = Arr::get($element->itemUrls, $id, '');

    if( $url && $pos && empty($path[$pos]) )
    {
      $path[$pos] = $url.'/';
    }

    return array($state, $path);
  }

  /**
   * Сортируем состояние в соответствие с сортировкой элементов фильтра
   * @param $state
   *
   * @return array
   */
  protected function sortUrlState($state)
  {
    $sorted = array();

    foreach($this->filter->elements as $elementId => $element)
    {
      if( isset($state[$elementId]) )
      {
        $sorted[$elementId] = $state[$elementId];
      }

      foreach($element->items as $itemId => $item)
      {
        if( isset($state[$elementId][$itemId]) )
        {
          $sorted[$elementId][$itemId] = $state[$elementId][$itemId];
        }
      }
    }

    return $sorted;
  }

  /**
   * @param ProductFilterElementItem $item
   *
   * @return array
   */
  protected function getItemState(ProductFilterElementItem $item)
  {
    return array($item->parent->id => ($item->parent->isMultiple() ? array($item->id => $item->id) : $item->id));
  }
}