<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class FileBackup
{
  const SUFFIX_DATE_TIME = '.Y-m-d_H-i';

  public $backupPath;

  public $suffixFormat;

  public function __construct($backupPath)
  {
    $this->backupPath = $backupPath;
    $this->suffixFormat = self::SUFFIX_DATE_TIME;
  }

  public function makeBackup($file)
  {
    $fileName = basename($file);
    $fileName .= date($this->suffixFormat);

    if( !rename($file, $this->backupPath.$fileName) )
      throw new ErrorException('Не удалось создать резервную копию файла '.$file);
  }
}