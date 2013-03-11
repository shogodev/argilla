<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BFrontendMenuGridView extends BGridView
{
  protected static $gridIdPrefix = 'BFrontendMenu_';

  /**
   * @param BFrontendMenu $model
   *
   * @return string
   */
  public static function buildGridId(BFrontendMenu $model)
  {
    return self::$gridIdPrefix.$model->getId();
  }

  /**
   * @param string $gridId
   *
   * @return mixed
   */
  public static function getIdFromGridViewId($gridId)
  {
    return str_replace(self::$gridIdPrefix, '', $gridId);
  }
}