<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @method static BRedirect model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $base
 * @property string $target
 * @property integer $type_id
 * @property integer $visible
 *
 * @property BRedirectType $type
 */
class BRedirect extends BActiveRecord
{
  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('base, target, type_id', 'required'),
      array('type_id, visible', 'numerical', 'integerOnly' => true),
      array('base, target', 'length', 'max' => 255),
    );
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'type' => array(self::HAS_ONE, 'BRedirectType', 'type_id'),
    );
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'base'    => 'Начальный URL',
      'target'  => 'Конечный URL',
      'type_id' => 'Тип',
    ));
  }

  /**
   * @return bool
   */
  protected function beforeSave()
  {
    $this->base = $this->prepareUrl($this->base);
    $this->target = $this->prepareUrl($this->target);

    return parent::beforeSave();
  }

  /**
   * Удаление домента из строки url
   *
   * @param string $string
   *
   * @return string
   */
  public function prepareUrl($string)
  {
    $parts = parse_url($string);
    $string = '';

    if( !empty($parts['path']) )
      $string .= $parts['path'];

    if( !empty($parts['query']) )
      $string .= '?'.$parts['query'];

    if( !empty($parts['fragment']) )
      $string = '#'.$parts['fragment'];

    return $string;
  }

  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('base', $this->base, true);
    $criteria->compare('target', $this->target, true);
    $criteria->compare('type_id', $this->type_id);
    $criteria->compare('visible', $this->visible);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}