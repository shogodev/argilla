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
 * @property ProductFilter $filter
 */
class ProductFilterElementItem extends CComponent
{
  const UNDEFINED_NAME = 'Не определено';

  public $id;

  public $selected = false;

  public $amount = 0;

  public $image;

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

  /**
   * @return string
   */
  protected function buildUrl()
  {
    $curUrl = parse_url(Yii::app()->controller->getCurrentUrl());
    $path   = explode("/", $curUrl['path']);

    $state = Arr::mergeAssoc($this->filter->state, $this->getElementState());

    if( $this->isSelected() )
      unset($state[$this->parent->id][$this->id]);

    $state = $this->sortUrlState($state);

    foreach($this->filter->elements as $element)
      if( $element->isUrlDependence() )
        list($state, $path) = $this->changeUrlPath($state, $path, $element);

    $state['submit'] = 1;
    $path = preg_replace("/\/+/", "/", implode('/', $path));
    $url  = Utils::buildUrl(array(
      'path' => $path,
      'query' => http_build_query(array($this->filter->filterKey => $state))
    ));

    return $url;
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
  protected function changeUrlPath($state, $path, $element)
  {
    $key   = $element->id;
    $value = Arr::get($state, $key);

    if( is_array($value) )
    {
      $id = key($value);
      unset($state[$key][$id]);
    }
    else
    {
      $id = $value;
      unset($state[$key]);
    }

    return array($state, $this->processPath($path, $element, $id));
  }

  /**
   * @param $path
   * @param $element
   * @param $id
   *
   * @return mixed
   */
  protected function processPath($path, $element, $id)
  {
    foreach(explode("/", $this->filter->urlPattern) as $i => $pattern)
      if( $pattern === '{'.$this->parent->id.'}' )
        $path[$i] = Arr::get($element->itemUrls, $id, '').'/';

    return $path;
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
      foreach($element->items as $itemId => $item)
        if( isset($state[$elementId][$itemId]) )
          $sorted[$elementId][$itemId] = $state[$elementId][$itemId];

    return $sorted;
  }

  /**
   * @return array
   */
  protected function getElementState()
  {
    return array($this->parent->id => ($this->parent->isMultiple() ? array($this->id => $this->id) : $this->id));
  }
}