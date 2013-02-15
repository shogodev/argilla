<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.image
 *
 * Класс используется для работы с одним изображением модели и его миниатюр
 * @example
 * Есть класс MyClass, у которого есть поле @img с названием файла,
 * файл и его миниатюры хранятся по пути f/myClass/
 * с именами preview_@img.png, gallery_@img.png, @img.png
 * Тогда для получения оригинала изображения или его миниатюр в базовую модель необходимо добавить
 * myMethod(), который будет возвращать пути к изображениям
 *
 * <code>
 * class MyClass
 * {
 *  public function myMethod()
 *  {
 *    return new FSingleImage($this->img, $this, array('gallery', 'preview'));
 *  }
 * }
 * </code>
 *
 * Массив $availableTypes в данном случае содержит префиксы изображения
 * (preview, gallery)
 *
 * @HINT: Параметр $defaultImage конструктора содержит путь к изображению, которое будет выводиться в случае, если
 * указанное изображение не существует
 *
 * Обращение к миниатюрам происходит обращением к названию типа миниатюры
 * <code>
 *  echo $myClass->myMethod()->gallery
 * </code>
 *
 * Для вызова пути к оригинальному изображению используется __toString()
 * <code>
 *  echo $myClass->myMethod();
 * </code>
 */
class FSingleImage implements ImageInterface
{
  /**
   * Изображение, отображаемое при отсутствии файла
   *
   * @var string
   */
  protected $defaultImage = 'i/sp.gif';

  /**
   * Массив доступных префиксов для файла
   *
   * @var array
   */
  protected $availableTypes = array();

  /**
   * Оригинальное имя файла
   *
   * @var string
   */
  protected $name;

  /**
   * Название директории в папке f
   *
   * @var string
   */
  protected $path;

  /**
   *
   * @param string $name
   * @param string $path
   * @param array $types
   * @param string $defaultImage
   */
  public function __construct($name, $path, array $types = array(), $defaultImage = 'i/sp.gif')
  {
    $this->name           = $name;
    $this->path           = $path;
    $this->availableTypes = $types;
    $this->defaultImage   = $defaultImage;
  }

  /**
   * Получение неоригинального изображения
   * Допустимые типы находятся в $this->availableTypes
   * Если файл не совпадает с доступными типами, вызывается магия Yii
   * Если файл не существует, отдается defaultImage
   *
   * @param string $name
   *
   * @return string
   * @throws CException
   */
  public function __get($name)
  {
    if( file_exists($this->getFullPath($name)) )
      return $this->getFullPath($name);
    else
    {
      if( in_array($name, $this->availableTypes) )
        return $this->defaultImage;
      else
        throw new CException('Запрашиваемое изображение не существует');
    }

  }

  /**
   * По умолчанию отдаётся оригинальный файл
   *
   * @return string
   */
  public function __toString()
  {
    if( file_exists($this->getFullPath()) )
      return $this->getFullPath();
    else
      return $this->defaultImage;
  }

  /**
   * Создание пути для файла
   *
   * @return string
   */
  public function getPath()
  {
    return 'f/' . $this->path . '/';
  }

  /**
   * Создание полного пути до файла,
   * если не установлен параметр $name
   * используется свойство класса $name
   *
   * @param string $name
   *
   * @return string
   */
  protected function getFullPath($name = null)
  {
    if( empty($name) )
      return $this->getPath() . $this->name;
    else
      return $this->getPath() . $name . '_' . $this->name;
  }
}