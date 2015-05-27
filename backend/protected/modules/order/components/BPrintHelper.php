<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BPrintHelper
{
  /**
   * @param string $header
   */
  public static function rowHeader($header)
  {
    $b = CHtml::tag('b', array(), $header);

    echo self::tr(CHtml::tag('td', array('colspan' => 2), $b));
  }

  /**
   * @param BActiveRecord $model
   * @param string $attribute
   * @param null $label
   */
  public static function modelRow($model, $attribute, $label = null)
  {
    if( empty($model->{$attribute}) )
      return;

    $outLabel = $label ? $label : $model->getAttributeLabel($attribute);
    self::row($model->{$attribute}, $outLabel);
  }

  /**
   * @param BActiveRecord $model
   * @param string $attribute
   * @param null $label
   * @param $list
   */
  public static function modelRowList($model, $attribute, $label = null, $list)
  {
    if( empty($model->{$attribute}) )
      return;

    if( isset($list[$model->{$attribute}]) )
    {
      $outLabel = $label ? $label : $model->getAttributeLabel($attribute);
      self::row($list[$model->{$attribute}], $outLabel);
    }
  }

  public static function row($content, $label)
  {
    if( empty($content) )
      return;

    $out = CHtml::tag('td', array('align' => 'right', 'valign' => 'top'), $label.':');
    $out .= CHtml::tag('td', array(), $content);

    echo self::tr($out);
  }

  private static function tr($content, $htmlOptions = array())
  {
    return CHtml::tag('tr', $htmlOptions, $content);
  }
}