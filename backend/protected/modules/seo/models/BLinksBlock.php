<?php
/**
 * @author Alexander Kolobkov <kolobkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @method static BLinksBlock model(string $class = __CLASS__)
 */
class BLinksBlock extends BActiveRecord
{
  public function tableName()
  {
    return '{{seo_links_block}}';
  }

  public function rules()
  {
    return array(
      array('name, code, key, url', 'required'),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('visible', 'length', 'max' => 1),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'code' => 'Код ссылки',
      'url' => 'Список урлов',
    ));
  }

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