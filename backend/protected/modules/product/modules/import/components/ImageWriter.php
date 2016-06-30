<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::import('frontend.share.helpers.*');
Yii::import('backend.modules.product.modules.import.components.exceptions.*');
Yii::import('backend.modules.product.modules.import.components.abstracts.AbstractImportWriter');

/**
 * Class ImageWriter
 * @property string $sourcePath
 */
class ImageWriter extends AbstractImportWriter
{
  /**
   * Пропускаем с предупреждением
   */
  const FILE_ACTION_SKIP_WITH_WARNING = 1;

  /**
   * Пропускаем с без предупреждения(по тихому)
   */
  const FILE_ACTION_SKIP_SILENT = 2;

  /**
   * Заменяем существующий файл
   */
  const FILE_ACTION_REPLACE_OLD = 3;

  /**
   * Переименовывает новый файл в уникальное имя
   */
  const FILE_ACTION_RENAME_NEW_FILE = 4;

  const DB_ACTION_EXISTING_RECORD_SKIP_WITH_WARNING = 1;

  const DB_ACTION_EXISTING_RECORD_SKIP_SILENT = 2;

  //const DB_ACTION_EXISTING_RECORD_REPLACE = 3;

  /**
   * @var int $actionWithSameFiles действия с одинаковыми файлами (FILE_ACTION_SKIP_WITH_WARNING, FILE_ACTION_SKIP_SILENT, FILE_ACTION_REPLACE_OLD)
   */
  public $actionWithSameFiles = self::FILE_ACTION_SKIP_WITH_WARNING;

  public $actionWithExistingRecord = self::DB_ACTION_EXISTING_RECORD_SKIP_WITH_WARNING;

  public $previews = array();

  public $defaultJpegQuality = 90;

  /***
   * @var bool $phpThumbErrorExceptionToWarning - игнорировать phpThumb исключения
   */
  public $phpThumbErrorExceptionToWarning = true;

  /**
   * @var EPhpThumb
   */
  protected $phpThumb;

  protected $productIdsCache;

  protected $tables = array(
    'product' => '{{product}}',
    'productImage' => '{{product_img}}'
  );

  protected $outputPath;

  protected $sourcePath;

  /**
   * @var CDbCommandBuilder $commandBuilder
   */
  protected $commandBuilder;

  public function __construct(ConsoleFileLogger $logger, $sourcePath = 'f/product/src', $outputPath = 'f/product')
  {
    parent::__construct($logger);

    $basePath = GlobalConfig::instance()->rootPath;

    $this->outputPath = $basePath.ImportHelper::wrapInSlashBegin($outputPath);
    $this->sourcePath = $basePath.ImportHelper::wrapInSlashBegin($sourcePath);

    $this->commandBuilder = Yii::app()->db->schema->commandBuilder;

    $this->phpThumb = Yii::createComponent(array(
      'class' => 'ext.phpthumb.EPhpThumb',
      'options' => array(
        'jpegQuality' => $this->defaultJpegQuality,
      ),
    ));

    $this->phpThumb->init();
  }

  public function showStatistics()
  {
  }

  public function writeAll(array $data)
  {
    $itemsAmount = count($data);
    if( $itemsAmount == 0 )
      return;

    $progress = new ConsoleProgressBar($itemsAmount);
    $this->logger->log('Начало обработки файлов');
    $progress->start();
    foreach($data as $uniqueAttributeValue => $images)
    {
      $this->safeWriteItem($uniqueAttributeValue, $images);
      $progress->setValueMap('memory', Yii::app()->format->formatSize(memory_get_usage()));
      $progress->advance();
    }
    $progress->finish();
    $this->logger->log('Обработка файлов завершена');
  }

  public function writePartial(array $data)
  {
    foreach($data as $uniqueAttributeValue => $images)
    {
      $this->safeWriteItem($uniqueAttributeValue, $images);
    }
  }

  protected function safeWriteItem($uniqueAttributeValue, $images)
  {
    try
    {
      if( !($productId = $this->getProductIdByAttribute($this->uniqueAttribute, $uniqueAttributeValue)) )
        throw new WarningException('Не удалсь найти продукт по атрибуту '.$this->uniqueAttribute.' = '.$uniqueAttributeValue);

      foreach($images as $itemData)
      {
        $image = $itemData['file'];
        $file = $this->sourcePath.ImportHelper::wrapInSlashBegin($image);

        if( !file_exists($file) )
          throw new WarningException('Файл '.$file.' не найден (строка '.$itemData['rowIndex'].')');

        try
        {
          $this->beginTransaction();

          $fileName = pathinfo($file, PATHINFO_BASENAME);
          $fileName = $this->normalizeFileName($fileName);

          $firstImage = reset($images)['file'];
          $type = ($image == $firstImage ? 'main' : 'gallery');
          $this->write($file, $fileName, $productId, $type);

          $this->commitTransaction();
        }
        catch(Exception $e)
        {
          $this->rollbackTransaction();

          if( !($e instanceof SilentException) )
            throw $e;
        }
      }
    }
    catch(WarningException $e)
    {
      $this->logger->warning($e->getMessage());
    }
  }

  protected function write($file, $fileName, $productId, $type)
  {
    $record = $this->findRecordByNameAndParent($fileName, $productId);
    $fileExists = $this->checkExistFile($fileName);

    if( $fileExists )
    {
      switch( $this->actionWithSameFiles )
      {
        case self::FILE_ACTION_SKIP_WITH_WARNING:
          throw new WarningException('Файл '.$fileName.' существует (старое имя '.$file.')');
          break;

        case self::FILE_ACTION_SKIP_SILENT:
          throw new SilentException('Файл '.$fileName.' существует (старое имя '.$file.')');
          break;

        case self::FILE_ACTION_RENAME_NEW_FILE:
          $fileName = $this->createUniqueFileName($fileName);
          break;

        case self::FILE_ACTION_REPLACE_OLD:
          break;
      }
    }

    if( $record )
    {
      switch( $this->actionWithExistingRecord )
      {
        case self::DB_ACTION_EXISTING_RECORD_SKIP_WITH_WARNING:
          throw new WarningException('Запись c name = '.$fileName.' и parent = '.$record['parent'].' существует (id = '.$record['id'].')');
          break;

        case self::DB_ACTION_EXISTING_RECORD_SKIP_SILENT:
          throw new SilentException('Запись c name = '.$fileName.' и parent = '.$record['parent'].' существует (id = '.$record['id'].')');
          break;

        /*        case self::DB_ACTION_EXISTING_RECORD_REPLACE:
          //to-do: Стерать все записи продукта и файлы, и записать новые
          if( $fileExists )
            $this->deleteOldImages($record['name']);

          if( $record['name'] != $fileName )
            $this->updateImageRecord($record['id'], $productId);

          $this->createImages($file, $fileName);
          break;*/
      }
    }
    else
    {
      $this->createImageRecord($fileName, $productId, $type);
      $this->createImages($file, $fileName);
    }
  }

  /**
   * @param $fileName
   * @param $productId
   *
   * @return mixed
   */
  protected function findRecordByNameAndParent($fileName, $productId)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('name', $fileName);
    $criteria->compare('parent', $productId);

    $command = $this->commandBuilder->createFindCommand($this->tables['productImage'], $criteria);

    return $command->queryRow();
  }

  /**
   * @param $fileName
   *
   * @return bool
   */
  protected function checkExistFile($fileName)
  {
    return file_exists($this->outputPath.ImportHelper::wrapInSlashBegin($fileName));
  }

  protected function deleteOldImages($fileName)
  {
    foreach($this->previews as $preview => $sizes)
    {
      $filePath = $this->outputPath.ImportHelper::wrapInSlashBegin(($preview === 'origin' ? "" : $preview.'_').$fileName);

      if( !unlink($filePath) )
        throw new WarningException("Ошибка, не удальсь удалить файл ".$filePath);
    }
  }

  /**
   * @param $fileName
   *
   * @return string $newFileName
   */
  protected function createUniqueFileName($fileName)
  {
    return UploadHelper::prepareFileName($this->outputPath, $fileName);
  }

  protected function createImages($file, $newFileName)
  {
    foreach($this->previews as $preview => $sizes)
    {
      $newPath = $this->outputPath.ImportHelper::wrapInSlashBegin(($preview === 'origin' ? "" : $preview.'_').$newFileName);

      try
      {
        $thumb = $this->phpThumb->create($file);
        $thumb->resize($sizes[0], $sizes[1]);
        $thumb->save($newPath);
        chmod($newPath, 0775);
      }
      catch(Exception $e)
      {
        if( $this->phpThumbErrorExceptionToWarning )
          throw new WarningException($e->getMessage());
        else
          throw $e;
      }
    }
  }

  /**
   * @param $fileName
   * @param $productId
   * @param $type
   *
   * @return BProductImg
   * @throws CDbException
   * @throws WarningException
   * @internal param $filePath
   * @internal param string $file
   */
  protected function createImageRecord($fileName, $productId, $type)
  {
    $command = $this->commandBuilder->createInsertCommand($this->tables['productImage'], array(
      'parent' => $productId,
      'name' => $fileName,
      'type' => $type
    ));

    if( !$command->execute() )
    {
      throw new WarningException('Ошибака записи файла '.$fileName.' в БД product_id = '.$productId);
    }
  }

  protected function updateImageRecord($id, $fileName)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('id', $id);
    $command = $this->commandBuilder->createUpdateCommand($this->tables['productImage'], array('name' => $fileName), $criteria);

    if( !$command->execute() )
    {
      throw new WarningException('Ошибака обновления записи в БД id = '.$id);
    }
  }

  protected function getProductIdByAttribute($attribute, $value)
  {
    if( is_null($this->productIdsCache) )
    {
      $this->productIdsCache = array();

      $criteria = new CDbCriteria();
      $criteria->select = array($attribute, 'id');
      $command = $this->commandBuilder->createFindCommand($this->tables['product'], $criteria);

      foreach($command->queryAll() as $data)
        $this->productIdsCache[$data['id']] = $data[$attribute];
    }

    return array_search($value, $this->productIdsCache);
  }

  protected function normalizeFileName($file)
  {
    $name = pathinfo($file, PATHINFO_FILENAME);
    $ext = pathinfo($file, PATHINFO_EXTENSION);

    return strtolower(Utils::translite($name)).'.'.$ext;
  }
}