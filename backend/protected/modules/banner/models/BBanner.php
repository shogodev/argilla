<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.banner.models
 *
 * @method static BBanner model(string $class = __CLASS__)
 *
 * @property integer $id
 *
 * @property integer $position
 * @property integer $location
 *
 * @property string $title
 * @property string $url
 * @property string $img
 *
 * @property integer $swd_w
 * @property integer $swd_h
 *
 * @property string $code
 *
 * @property string $pagelist
 * @property string $pagelist_exc
 *
 * @property boolean $new_window
 * @property boolean $visible
 */
class BBanner extends BActiveRecord
{
  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    return array(
      array('location, title', 'required'),
      array('position, swf_w, swf_h, new_window, visible', 'numerical', 'integerOnly' => true),
      array('title', 'length', 'max' => 128),
      array('url, code', 'length', 'max' => 512),
      array('location, title, url, pagelist, swf_w, swf_h, new_window, visible, pagelist_exc', 'safe'),
  );
  }

  public function behaviors()
  {
    return array(
      'uploadBehavior' => array(
        'class' => 'UploadBehavior', 'validAttributes' => 'img'
      ),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array(
      'id'           => '#',
      'position'     => 'Позиция',
      'location'     => 'Расположение',
      'getLocation'  => 'Расположение',
      'title'        => 'Название',
      'url'          => 'URL',
      'img'          => 'Изображение',
      'swf_w'        => 'Ширина для SWF',
      'swf_h'        => 'Высота для SWF',
      'code'         => 'Код',
      'pagelist'     => 'Список страниц',
      'pagelist_exc' => 'Исключения из списка',
      'new_window'   => 'В новом окне',
      'visible'      => 'Вид',
      'upload'       => 'Изображение',
    );
  }

  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('title', $this->title, true);
    $criteria->compare('visible', '='.$this->visible);
    $criteria->compare('location', $this->location, true);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}