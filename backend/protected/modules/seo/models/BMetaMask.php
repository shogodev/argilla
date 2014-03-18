<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @method static BMetaMask model(string $class = __CLASS__)
 *
 * @property string $url_mask
 * @property string $visible
 */
class BMetaMask extends BActiveRecord
{
  public function tableName()
  {
    return '{{seo_meta_mask}}';
  }

  public function rules()
  {
    return array(
      array('url_mask, title', 'required'),
      array('url_mask', 'unique'),
      array('url_mask, title, description, keywords', 'length', 'max' => 255),
      array('url_mask, title, description, keywords, visible' , 'safe'),
    );
  }

  public function beforeSave()
  {
    $this->url_mask = Utils::getRelativeUrl($this->url_mask);

    return parent::beforeSave();
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'url_mask' => 'Маска',
      'title' => 'Title страницы',
    ));
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('url_mask', $this->url_mask, true);
    $criteria->compare('visible', $this->visible);

    return $criteria;
  }
}