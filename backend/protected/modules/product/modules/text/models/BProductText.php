<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductText model(string $class = __CLASS__)
 *
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property integer $visible
 * @property string $content
 * @property string $content_upper
 * @property string $img
 */
class BProductText extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('url', 'required'),
      array('id, visible', 'numerical', 'integerOnly' => true),
      array('url', 'length', 'max' => 255),
      array('content, content_upper', 'safe'),
      array('url, visible, content', 'safe', 'on' => 'search'),
    );
  }

  protected function beforeSave()
  {
    $this->url = '/'.trim($this->url, " /").'/';
    return parent::beforeSave();
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'content_upper' => 'Текс вверху',
      'content' => 'Текс внизу',
    ));
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  public function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria = new CDbCriteria;

    $criteria->compare('url', $this->url, true);
    $criteria->compare('visible', $this->visible);
    $criteria->compare('content', $this->content, true);

    return $criteria;
  }
}