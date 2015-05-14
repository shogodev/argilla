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
   * @param string $filename
   *
   * @return string
   */
  public static function doCustomFilename($filename)
  {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $filename = str_replace('.'.$ext, '', $filename);
    $filename = $filename . '_' . rand(1, 1000) . '.' . $ext;

    return $filename;
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
    $fileName = Utils::translite($fileName, false);

    while( file_exists($path . $fileName) )
      $fileName = self::doCustomFilename($fileName);

    return $fileName;
  }
} 