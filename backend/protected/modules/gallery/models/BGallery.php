<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.gallery.models
 *
 * @method static BGallery model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $type
 * @property integer $visible
 */
class BGallery extends BActiveRecord
{
  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('name, url', 'required'),
      array('name, type', 'length', 'max' => 255),
      array('name, url, type, visible', 'safe'),
    );
  }

  /**
   * @return array
   */
  public function behaviors()
  {
    return array(
      'uploadBehavior' => array(
        'class' => 'UploadBehavior', 'validAttributes' => 'img'
      ),
    );
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array('gallery_image' => 'Изображения'));
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('name', $this->name, true);
    $criteria->compare('url', $this->url, true);
    $criteria->compare('type', $this->type, true);
    $criteria->compare('visible', $this->visible);

    return $criteria;
  }
}