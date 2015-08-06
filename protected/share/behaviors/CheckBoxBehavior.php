<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 * Пример подключения
 *
 * public function behaviors()
 * {
 *   return array(
 *    'checkRecommendedBehavior' => array(
 *      'class' => 'CheckBoxBehavior',
 *      'attribute' => 'recommended'
 *    )
 *   );
 * }
 *
 * В чекбоксе нужно прописать value равное CheckBoxBehavior::CHECKED_VALUE
 * Например:
 * <input name="BReport[4][recommended]" value="<?php echo CheckBoxBehavior::CHECKED_VALUE?>" type="checkbox">
 */
/**
 * Class CheckBoxBehavior
 * Поведение для чекбоксов решающее проблему того, что на сервер не отправлются данные чекбокса если он выключен
 */
class CheckBoxBehavior extends SActiveRecordBehavior
{
  const CHECKED_VALUE = 'checked';

  public $attribute;

  public function init()
  {
    if( empty($this->attribute) )
      throw new CHttpException(500, 'Ошибка! Заполните обязательное свойство attribute');
  }

  public function beforeSave($event)
  {
    if( $this->owner->{$this->attribute} == self::CHECKED_VALUE )
      $this->owner->{$this->attribute} = 1;
    else
      $this->owner->{$this->attribute} = 0;
  }
}