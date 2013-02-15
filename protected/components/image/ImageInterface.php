<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.image
 */
interface ImageInterface
{
  /**
   * Получение неоригинального изображения
   * Допустимые типы находятся в $this->availableTypes
   * Если файл не совпадает с доступными типами, вызывается магия Yii
   * Если файл не существует, отдается defaultImage
   *
   * @param string $name
   *
   * @throws CException
   *
   * @return string
   */
  public function __get($name);

  /**
   * По умолчанию отдаётся оригинальный файл
   *
   * @return string
   */
  public function __toString();

  /**
   * Создание пути к файлу
   *
   * @return string
   */
  public function getPath();
}