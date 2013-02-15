<?php
/**
 * User: Nikita Melnikov <melnikov@shogo.ru>
 * Date: 1/11/13
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