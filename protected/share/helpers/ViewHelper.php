<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ViewHelper
{
  /**
   * Строит виджет FMenu по входному массиву или масииву объектов
   * @param CComponent[]|array $elements
   * @param array $attributeOptions атрибуты для построения эламента массива из объектов
   * @param array $menuOptions опции виджета FMenu
   *
   * @return array
   *
   * Пример использования:
   * ViewHelper::menu($part, array('label' => 'name', 'url' => 'url'), array('itemTemplate' => '{menu} ({linkCount})'))
   */
  public static function menu($elements, $attributeOptions = array(), $menuOptions = array())
  {
    $items = array();

    foreach($elements as $key => $element)
    {
      if( $element instanceof CComponent )
      {
        $item = array();
        foreach($attributeOptions as $name => $attribute)
        {
          $item[$name] = $element->$attribute;
        }
      }
      else
      {
        $item = $element;
      }

      if( isset($menuOptions['itemTemplate']) )
      {
        $item['template'] = self::replace($menuOptions['itemTemplate'], $element);
      }

      $items[$key] = $item;
    }

    Yii::app()->controller->widget('FMenu', CMap::mergeArray($menuOptions, array('items' => $items)));
  }

  public static function header($header, $tag = 'h1', $htmlOptions = array())
  {
    echo CHtml::tag($tag, $htmlOptions, Yii::app()->meta->setHeader($header));
  }

  /**
   * Заменяет содержащиеся в $template выражения вида {выражение} на своиства или заначения $data
   * @param string $template шаблон
   * @param array|CComponent $data источник данных для замены объект или массив
   * @param bool $clearNotReplaced очистить не замененные выражения default false
   *
   * @return string
   */
  public static function replace($template, $data, $clearNotReplaced = false)
  {
    if( preg_match_all('/{([^{}\s]*)}/', $template, $matches) )
    {
      $replaceArray = array();
      foreach(Arr::reset($matches) as $matchIndex => $expression)
      {
        $attribute = $matches[1][$matchIndex];

        if( is_array($data) && isset($data[$attribute]) )
          $replaceArray[$expression] = $data[$attribute];
        else if( is_object($data) && isset($data->{$attribute}) )
          $replaceArray[$expression] = $data->{$attribute};

        if( $attribute == 'url' && is_array($replaceArray[$expression]) )
          $replaceArray[$expression] = CHtml::normalizeUrl($replaceArray[$expression]);
      }

      if( $replaceArray )
        $template = strtr($template, $replaceArray);
    }

    if( $clearNotReplaced )
      $template = preg_replace('/({[^{}\s]*})/', '', $template);

    return $template;
  }

  /**
   * @return Contact|FActiveRecord|null
   */
  public static function contact()
  {
    if( $contact = Yii::app()->controller->getHeaderContacts() )
      return $contact;

    return null;
  }

  /**
   * @return ContactField[]
   */
  public static function phones()
  {
    if( $contact = self::contact() )
    {
      return $contact->getFields('phones');
    }

    return array();
  }

  /**
   * @param bool $clear
   *
   * @return ContactField|null|string
   */
  public static function phone($clear = false)
  {
    if( $phones = self::phones() )
    {
      $phone = Arr::reset($phones);

      return $clear ? $phone->getClearPhone() : $phone;
    }

    return null;
  }
}