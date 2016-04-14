<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ImportHelper
{
  private static $urlCache;

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
   * Преобразует номер столбца в индекс колонки exсel таблици A - 0, B - 1
   *
   * @param $letters
   * @param bool $firstZero индексация начинается с 0
   *
   * @return int
   */
  public static function lettersToNumber($letters, $firstZero = true)
  {
    $num = 0;
    $arr = array_reverse(str_split($letters));

    for($i = 0; $i < count($arr); $i++)
    {
      $num += (ord(strtolower($arr[$i])) - 96) * (pow(26, $i));
    }

    return $firstZero ? $num - 1 : $num;
  }

  /**
   * Преобразует индекс колонки exсel в номер столбца в 1 - A, 2 - B
   *
   * @param $number
   *
   * @return string
   */
  public static function numberToLetters($number)
  {
    $letter = "";
    if( $number <= 0 )
      return '';

    while($number != 0)
    {
      $p = ($number - 1) % 26;
      $number = intval(($number - $p) / 26);
      $letter = chr(65 + $p).$letter;
    }

    return $letter;
  }

  /**
   * Преобразует дивпазон индексов колонок exсel в массив
   * например 'a-c' в array('A', 'B', 'C');
   *
   * @param $string
   *
   * @return array
   */
  public static function getLettersRange($string)
  {
    list($begin, $end) = explode('-', $string);

    $array = array();

    $current = self::lettersToNumber($begin);
    $end = self::lettersToNumber($end);

    while(1)
    {
      $array[] = self::numberToLetters(($current++) + 1);

      if( $current > $end )
        break;
    }

    return $array;
  }

  public static function getModelWithoutBehaviors($className, $scenario = null)
  {
    /**
     * @var CActiveRecord $model
     */
    $model = new $className(null);
    if( $scenario )
      $model->setScenario($scenario);

    $model->setIsNewRecord(true);
    $model->init();

    return $model;
  }

  /**
   * @param $array
   *
   * @return mixed
   */
  public static function convertColumnIndexes($array)
  {
    foreach($array as $key => $columnIndex)
    {
      if( !empty($columnIndex) && !is_numeric($columnIndex) && !is_array($columnIndex) )
      {
        $array[$key] = self::lettersToNumber($columnIndex);
      }
      else if(is_array($columnIndex))
      {
        $columnIndex[0] = self::lettersToNumber(Arr::get($columnIndex, 0));
        $array[$key] = $columnIndex;
      }
      else
        unset($array[$key]);
    }

    return $array;
  }

  /**
   * Оборачивает путь в слеши, исключаея дублирование
   *
   * @param $path
   * @param string $wrapper
   *
   * @return string
   */
  public static function wrapInSlash($path, $wrapper = DIRECTORY_SEPARATOR)
  {
    return self::wrapInSlashBegin(self::wrapInSlashEnd($path, $wrapper, false), $wrapper);
  }

  /**
   * Оборачивает начало путь в слеш, исключаея дублирование
   *
   * @param $path
   * @param string $wrapper
   * @param bool $clearEnd
   *
   * @return string
   */
  public static function wrapInSlashBegin($path, $wrapper = DIRECTORY_SEPARATOR, $clearEnd = true)
  {
    $path = preg_replace("/(^\\{$wrapper})/", '', $path);

    if( $clearEnd )
      preg_replace("/(\\{$wrapper})$/", '', $path);

    return $wrapper.$path;
  }

  /**
   * Оборачивает конец путь в слеш, исключаея дублирование
   * @param $path
   * @param string $wrapper
   * @param bool $clearBegin
   *
   * @return string
   */
  public static function wrapInSlashEnd($path, $wrapper = DIRECTORY_SEPARATOR, $clearBegin = true)
  {
    $path = preg_replace("/(\\{$wrapper}$)/", '', $path);

    if( $clearBegin )
      preg_replace("/(^\\{$wrapper})/", '', $path);

    return $path.$wrapper;
  }

  /**
   * @param string $table
   * @param $url
   * @param bool|false $warmUpCache - прогреть
   *
   * @return mixed|string
   */
  public static function createUniqueUrl($table, $url, $warmUpCache = false)
  {
    if( !isset(self::$urlCache[$table]) )
    {
      self::$urlCache[$table] = array();

      if( $warmUpCache )
      {
        foreach(self::getUrls($table) as $itemUrl)
          self::$urlCache[$table][$itemUrl] = $itemUrl;
      }
    }

    if( !$warmUpCache )
    {
      if( $itemUrl = Arr::get(self::getUrls($table, $url), 0 ) )
        self::$urlCache[$table][$itemUrl] = $itemUrl;
    }

    $uniqueUrl = Utils::translite(trim($url));
    $suffix = 1;
    while( isset(self::$urlCache[$table][$uniqueUrl]) )
    {
      $uniqueUrl = $url.'_'.$suffix++;

      if( !$warmUpCache )
      {
        if( $checkUrl = Arr::get(self::getUrls($table, $uniqueUrl), 0 ) )
          self::$urlCache[$table][$checkUrl] = $checkUrl;
      }
    }

    if( $warmUpCache )
      self::$urlCache[$table][$uniqueUrl] = $uniqueUrl;

    return $uniqueUrl;
  }

  /**
   * @param $table|null "null" clears all
   */
  public static function clearUrlCache($table)
  {
    if( is_null($table) )
      self::$urlCache = null;
    else
      self::$urlCache[$table] = null;
  }

  private static function getUrls($table, $url = null)
  {
    $criteria = new CDbCriteria();
    $criteria->select = 'url';

    if( isset($url) )
      $criteria->compare('url', $url);

    $command = Yii::app()->db->schema->commandBuilder->createFindCommand($table, $criteria);

    return $command->queryColumn();
  }
}