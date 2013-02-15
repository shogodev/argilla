<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.contact.models
 *
 * @method static BContactTextBlock model(string $class = __CLASS__)
 */
class BContactTextBlock extends BActiveRecord
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{contact_textblock}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('name, contact_id', 'required'),
      array('sysname', 'unique'),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('name, sysname, content, position, visible', 'safe')
    );
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array('sysname' => 'Системное имя', 'contact_id' => 'Запись контактов'));
  }

  /**
   * @return array
   */
  public function getContactList()
  {
    $data = array();

    foreach( BContact::model()->findAll() as $contact )
    {
      $data[$contact->id] = $contact->name;
    }

    return $data;
  }
}