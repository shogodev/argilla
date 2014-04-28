<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.models
 *
 * @method static BOrderPaymentType model(string $className = __CLASS__)
 *
 * @property int $id
 * @property string $name
 * @property int $position
 * @property string $notice
 * @property int $visible
 */
class BOrderPaymentType extends BActiveRecord
{
  const CASH = 1;

  const NON_CASH  = 2;

  const EPAY = 3;

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('name', 'required'),
      array('notice, position, visible', 'safe'),
    );
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('name', $this->name, true);
    $criteria->compare('visible', $this->visible);

    return $criteria;
  }
}