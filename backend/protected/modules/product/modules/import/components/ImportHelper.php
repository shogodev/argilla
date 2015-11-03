<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ImportHelper
{
  /**
   * @param $folder
   * @param array $extensions
   *
   * @return null
   * @internal param string $extension
   */
  public static function getFileLastModified($folder, $extensions = array('csv'))
  {
    $extensions = is_array($extensions) ? $extensions : array($extensions);
    $files = CFileHelper::findFiles($folder, array(
      'level' => 0,
      'fileTypes' => $extensions,
    ));

    $filesWithDate = array();
    foreach($files as $file)
      $filesWithDate[filemtime($file)] = $file;

    return !empty($filesWithDate) ? $filesWithDate[max(array_keys($filesWithDate))] : null;
  }

  /**
   * @param $folder
   * @param array $extensions
   *
   * @return array
   * @internal param string $extension
   */
  public static function getFiles($folder, $extensions = array('csv'))
  {
    $extensions = is_array($extensions) ? $extensions : array($extensions);

    $files = array_reverse(CFileHelper::findFiles($folder, array(
      'fileTypes' => $extensions
    )));

    asort($files, SORT_NATURAL);

    return $files;
  }

  /**
   * Преобразует номер столбца в индекс колонки exсel таблици
   * @param $letters
   *
   * @return int
   */
  public static function lettersToNumber($letters)
  {
    $num = 0;
    $arr = array_reverse(str_split($letters));

    for($i = 0; $i < count($arr); $i++)
    {
      $num += (ord(strtolower($arr[$i])) - 96) * (pow(26, $i));
    }

    return $num - 1;
  }
}