<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.image
 *
 * Класс используется для работы с таблицей изображений модели и их миниатюр
 *
 * Examples:
 *
 * $image = ProductImage::model()->findByPk(1);
 * echo $image;
 * echo $image->pre;
 */
class FActiveImage extends FActiveRecord implements ImageInterface
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
   * Путь к папке с изображениями относительно корня проекта без начального слэша
   *
   * @var string
   */
  protected $imageDir = 'f/';

  /**
   * По умолчанию отдаётся оригинальный файл
   *
   * @return string
   */
  public function __toString()
  {
    if( file_exists($this->getFullPath()) )
    {
      return '/' . $this->getFullPath();
    }
    else
    {
      return $this->defaultImage;
    }
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
      else
      {
        return $this->defaultImage;
      }
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
    return $this->imageDir;
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