<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo.models
 *
 * @method static BMetaMask model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $url_mask
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $custom
 * @property string $header
 * @property integer $noindex
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
      array('url_mask', 'required'),
      array('url_mask', 'unique'),
      array('url_mask, title, description, keywords, header', 'length', 'max' => 255),
      array('noindex, visible', 'numerical', 'integerOnly' => true),
      array('custom', 'safe'),
    );
  }

  public function beforeSave()
  {
    if( !preg_match('/^#.*#$/', $this->url_mask) )
      $this->url_mask = Utils::getRelativeUrl($this->url_mask);

    return parent::beforeSave();
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'url_mask' => 'Маска',
      'title' => 'Title страницы',
      'header' => 'Заголовок h1',
      'noindex' => 'Запретить индексацию',
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