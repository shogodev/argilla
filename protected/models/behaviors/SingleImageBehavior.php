<?php
/**
 * @author Artyom Panin <panin@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSEs
 */

/**
 * Бехейвор для привязки FSingleImage к моделям FActiveRecord
 *
 * <pre>
 * public function behaviors()
 * {
 *   return array(
 *     'imageBehavior' => array('class' => 'SingleImageBehavior', 'path' => 'product'),
 *   );
 * }
 * </pre>
 *
 * @property FActiveRecord $owner
 */

class SingleImageBehavior extends CActiveRecordBehavior
{
  /**
   * @var string
   * Путь к папке с изображениями в папке f/.
   */
  public $path;

  /**
   * @var string
   * Свойство класса, которое будет содержать полный путь до изображения.
   */
  public $imagePathProperty = 'image';

  /**
   * @var string
   * Свойство класса, которое содержит имя файла изображения.
   */
  public $imageNameProperty = 'img';

  /**
   * @var array Типы изображений
   */
  public $types = array();

  public function __set($name, $value)
  {
    if ( $name == $this->imagePathProperty )
      $this->$name = $value;
    else
      parent::__set($name, $value);
  }

  public function attach($owner)
  {
    parent::attach($owner);

    if( !isset($this->path) )
    {
      throw new CException('Необходимо задать свойство path');
    }
  }

  public function afterFind($event)
  {
    $imageNameProperty = $this->owner->{$this->imageNameProperty};
    $this->{$this->imagePathProperty} = $imageNameProperty ? new FSingleImage($imageNameProperty, $this->path, $this->types) : null;
  }
}