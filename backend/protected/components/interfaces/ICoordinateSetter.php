<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components.interfaces
 */
interface ICoordinateSetter
{
  /**
   * @param $id
   * @param $attribute
   *
   * @return void
   */
  public function actionSetCoordinates($id, $attribute);
}