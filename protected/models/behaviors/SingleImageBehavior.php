<?php
/**
 * @author Artyom Panin <panin@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSEs
 */

/**
 * Бехейвор для привязки FSingleImage к моделям FActiveRecord
 *
 * <pre>
 *  ...
 *    'imageBehavior' => array('class' => 'SingleImageBehavior', 'path' => 'product'),
 *  ...
 *
 *  ...
 *   'imageBehavior' => array(
 *     'class' => 'SingleImageBehavior',
 *     'path' => 'product',
 *     'types' => array('pre', 'big'),
 *     'defaultImage' => '/i/contest/default-dish.jpg',
 *     'useDefaultImage' => true
 *   ),
 *  ...
 * </pre>
 *
 * @property FActiveRecord $owner
 */

class SingleImageBehavior extends SActiveRecordBehavior
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

  public $defaultImage = '/i/sp.gif';

  /**
   * @var bool
   * Если изображение не найдено выводить заглушку
   */
  public $useDefaultImage = false;

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

  public function init()
  {
    if( !isset($this->path) )
    {
      throw new CException('Необходимо задать свойство path');
    }
  }

  public function afterFind($event)
  {
    $imageNameProperty = $this->owner->{$this->imageNameProperty};
    $this->{$this->imagePathProperty} = $this->useDefaultImage || !empty($imageNameProperty) ? new FSingleImage($imageNameProperty, $this->path, $this->types, $this->defaultImage) : null;
  }
}