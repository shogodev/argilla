<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @property $name
 * @property $phone
 * @property $time
 * @property $content
 */
class Callback extends FActiveRecord
{
  public function tableName()
  {
    return '{{callbacks}}';
  }

  public function rules()
  {
    return array(
      array('name, phone', 'required'),
      array('time, content', 'safe'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'name'    => 'Ваше имя',
      'phone'   => 'Номер телефона',
      'time'    => 'Время звонка',
      'content' => 'Комментарий'
    );
  }
}