<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class UploadHelper
{
  /**
   * Добавляет случайное число после имени файла
   *
   * @param string $fileName
   *
   * @return string
   */
  public static function doUniqueFilename($fileName)
  {
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $name = pathinfo($fileName, PATHINFO_FILENAME);

    return $name . '_' . rand(1, 1000) . '.' . $ext;
  }

  /**
   * Если файл существует с таким названием уже существует, то создаёт новое имя файла.
   *
   * @param string $path
   * @param string $fileName
   *
   * @return string
   */
  public static function prepareFileName($path, $fileName)
  {
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $name = pathinfo($fileName, PATHINFO_FILENAME);

    $name = Utils::translite($name, true);
    $ext = Utils::translite($ext, true);

    $fileName = $name.'.'.$ext;

    while( file_exists($path . $fileName) )
      $fileName = self::doUniqueFilename($fileName);

    return $fileName;
  }
} 