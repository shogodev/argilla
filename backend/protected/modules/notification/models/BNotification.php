<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.form.models
 *
 * @method static BNotification model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $index
 * @property string name
 * @property string email
 * @property string subject
 * @property string view
 * @property string message
 * @property integer visible
 */
class BNotification extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('index', 'required'),
      array('index', 'unique'),
      array('name, email, subject, view, message, visible', 'safe'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'index' => 'Идентификатор',
      'name' => 'Название',
      'view' => 'Шаблон',
    ));
  }

  public function getViews()
  {
    $data = array();

    if( $dh = opendir('../protected/views/email/') )
    {
      while( ($file = readdir($dh)) !== false )
      {
        if( preg_match('/(.*).php/', $file, $matches) )
          $data[] = array('id'   => $matches[1],
                          'name' => $matches[1]);
      }
      closedir($dh);
    }

    if( !empty($data) )
      ksort($data);

    return $data;
  }

  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('id', '='.$this->id);
    $criteria->addSearchCondition('name', $this->name);
    $criteria->addSearchCondition('email', $this->email);
    $criteria->compare('visible', '='.$this->visible);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}