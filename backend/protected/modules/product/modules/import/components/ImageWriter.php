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
 * @property bool $replaceFile default false
 */
class ImageWriter extends AbstractImportWriter
{
  public $previews = array();

  public $defaultJpegQuality = 90;

  /**
   * @var EPhpThumb
   */
  protected $phpThumb;

  protected $productIdsCache;

  protected $tables = array(
    'product' => '{{product}}',
    'productImage' => '{{product_img}}'
  );

  protected $basePath = 'f/product';

  protected $sourcePath = 'src';

  protected $replaceFile = false;

  /**
   * @var CDbCommandBuilder $commandBuilder
   */
  protected $commandBuilder;

  public function __construct(ConsoleFileLogger $logger)
  {
    parent::__construct($logger);

    $this->basePath = realpath(Yii::getPathOfAlias('frontend').'/..').ImportHelper::wrapInSlash($this->basePath);

    $this->sourcePath = $this->basePath.ImportHelper::wrapInSlashEnd($this->sourcePath);

    $this->commandBuilder = Yii::app()->db->schema->commandBuilder;

    $this->phpThumb = Yii::createComponent(array(
      'class' => 'ext.phpthumb.EPhpThumb',
      'options' => array(
        'jpegQuality' => $this->defaultJpegQuality,
      ),
    ));

    $this->phpThumb->init();
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

  public function showStatistics()
  {

  }

  protected function setSourcePath($path)
  {
    $this->sourcePath = $this->basePath.ImportHelper::wrapInSlashEnd($path);
  }

  protected function setReplaceFile($replace)
  {
    $this->replaceFile = $replace;
  }

  protected function safeWriteItem($uniqueAttributeValue, $images)
  {
    try
    {
      $this->write($uniqueAttributeValue, $images);
    }
    catch(WarningException $e)
    {
      $this->logger->warning($e->getMessage());
    }
  }

  protected function write($uniqueAttributeValue, array $images)
  {
    if( !($productId = $this->getProductIdByAttribute($this->uniqueAttribute, $uniqueAttributeValue)) )
      throw new WarningException('Не удалсь найти продукт по атрибуту '.$this->uniqueAttribute.' = '.$uniqueAttributeValue);

    foreach($images as $image)
    {
      $file = $this->sourcePath.$image;

      if( !file_exists($file) )
        throw new WarningException('Файл '.$file.' не найден');

      $type = ($image == reset($images) ? 'main' : 'gallery');

      try
      {
        $this->beginTransaction();
        $newFileName = $this->createProductImageRecord($file, $productId, $type);
        $this->createImages($file, $newFileName);
        $this->commitTransaction();
      }
      catch(Exception $e)
      {
        $this->rollbackTransaction();
        throw $e;
      }
    }
  }

  protected function createImages($file, $newFileName)
  {
    foreach($this->previews as $preview => $sizes)
    {
      $newPath = $this->basePath.($preview === 'origin' ? "" : $preview.'_').$newFileName;

      if( !$this->replaceFile && file_exists($newPath) )
        throw new WarningException('Файл '.$newPath.' существует (старое имя '.$file.')');

      $thumb = $this->phpThumb->create($file);
      $thumb->resize($sizes[0], $sizes[1]);
      $thumb->save($newPath);
      chmod($newPath, 0775);
    }
  }

  /**
   * @param $file
   * @param $productId
   * @param $type
   *
   * @return BProductImg
   * @throws CDbException
   * @throws WarningException
   * @internal param $filePath
   * @internal param string $file
   */
  protected function createProductImageRecord($file, $productId, $type)
  {
    $fileName = pathinfo($file, PATHINFO_BASENAME);
    $fileName = $this->normalizeFileName($fileName);

    $criteria = new CDbCriteria();
    $criteria->compare('name', $fileName);
    $command = $this->commandBuilder->createFindCommand($this->tables['productImage'], $criteria);
    $result = $command->queryRow();

    if( !$result )
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

    return $fileName;
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