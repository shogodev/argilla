<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.image
 *
 * Класс используется для работы с таблицей изображений модели и их миниатюр
 *
 * Массив $availableTypes в данном случае содержит префиксы изображения
 * (preview, gallery)
 *
 * @HINT: Свойство $defaultImage содержит путь к изображению, которое будет выводиться в случае, если
 * указанное изображение не существует
 *
 * Обращение к миниатюрам происходит обращением к названию типа миниатюры
 * <code>
 *  $picture->gallery
 * </code>
 *
 * Для вызова пути к оригинальному изображению используется __toString()
 * <code>
 *  echo $picture;
 * </code>
 */
class FActiveImage extends FActiveRecord implements ImageInterface
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
   * По умолчанию отдаётся оригинальный файл
   *
   * @return string
   */
  public function __toString()
  {
    return $this->path . $this->name;
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
    if( in_array($name, $this->availableTypes) )
    {
      $path =  $this->path . $name . '_' . $this->name;

      if( file_exists($path) )
        return $path;
      else
        return $this->defaultImage;
    }
    else
      return parent::__get($name);
  }

  /**
   * Создание пути для файла
   *
   * @return string
   */
  public function getPath()
  {
    return 'f/' . lcfirst(get_called_class()) . '/';
  }
}