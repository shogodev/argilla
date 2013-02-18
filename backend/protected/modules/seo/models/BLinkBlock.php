<?php
/**
 * @author Alexander Kolobkov <kolobkov@shogo.ru>, Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $url
 * @property string $key
 * @property int $position
 * @property int $visible
 *
 * @method static BLinkBlock model(string $class = __CLASS__)
 */
class BLinkBlock extends BActiveRecord
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{seo_link_block}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('name, code, key, url', 'required'),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('visible', 'length', 'max' => 1),
    );
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'code' => 'Код ссылки',
      'url' => 'Список урлов',
    ));
  }

  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('name', $this->name, true);
    $criteria->compare('visible', $this->visible);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}