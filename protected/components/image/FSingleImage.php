<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.image
 *
 * Класс используется для работы с одним изображением модели и его миниатюр
 *
 * Examples:
 *
 * $image = new FSingleImage($model->img, 'product', array('pre'));
 * echo $image;
 * echo $image->pre;
 */
class FSingleImage implements ImageInterface
{
  /**
   * Изображение, отображаемое при отсутствии файла
   *
   * @var string
   */
  protected $defaultImage = '/i/sp.gif';

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
   * Название директории в папке с изображениями
   *
   * @var string
   */
  protected $path;

  /**
   * Путь к папке с изображениями относительно корня проекта
   *
   * @var string
   */
  protected $imageDir = 'f/';

  /**
   *
   * @param string $name
   * @param string $path
   * @param array $types
   * @param string $defaultImage
   */
  public function __construct($name, $path, array $types = array(), $defaultImage = '/i/sp.gif')
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
    if( in_array($name, $this->availableTypes) )
    {
      if( file_exists($this->getFullPath($name)) )
      {
        return '/' . $this->getFullPath($name);
      }

      return $this->defaultImage;
    }
    else
    {
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
    if( !empty($this->name) && file_exists($this->getFullPath()) )
    {
      return '/' . $this->getFullPath();
    }
    else
    {
      return $this->defaultImage;
    }
  }

  /**
   * Создание пути для файла
   *
   * @return string
   */
  public function getPath()
  {
    return $this->imageDir . $this->path . '/';
  }

  /**
   * @param $imageDir
   */
  public function setImageDir($imageDir)
  {
    $this->imageDir = $imageDir;
  }

  /**
   * Создание полного пути до файла
   *
   * @param string $name
   *
   * @return string
   */
  protected function getFullPath($name = null)
  {
    if( empty($name) )
    {
      return $this->getPath() . $this->name;
    }
    else
    {
      return $this->getPath() . $name . '_' . $this->name;
    }
  }
}