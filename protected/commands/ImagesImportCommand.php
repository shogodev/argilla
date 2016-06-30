<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::import('backend.modules.product.modules.import.components.*');
chdir(GlobalConfig::instance()->rootPath);

// Игнорируем  E_NOTICE и E_NOTICE прежде всего для phpThumb, чтобы не прерывить импорт
error_reporting(E_ALL & ~E_NOTICE & ~E_NOTICE);

class ImagesImportCommand extends AbstractImagesImportCommand
{
  public function actionIndex()
  {
    try
    {
      $this->actionImportFromCsv();
      //$this->updateProducts();
    }
    catch(Exception $e)
    {
      $this->logger->error($e->getMessage());
    }
  }

  public function actionImportFromCsv()
  {
    $imageWriter = new ImageWriter($this->logger);
    $imageWriter->previews = array(
      'origin' => array(4500, 4500),
      'big' => array(600, 460),
      'pre' => array(250, 190),
    );
    $imageWriter->uniqueAttribute = 'id';
    $imageWriter->clear = false;
    $imageWriter->clearTables = array('{{product_img}}');
    $imageWriter->defaultJpegQuality = 95;
    $imageWriter->phpThumbErrorExceptionToWarning = true;
    $imageWriter->actionWithSameFiles = ImageWriter::FILE_ACTION_RENAME_NEW_FILE;
    $imageWriter->actionWithExistingRecord = ImageWriter::DB_ACTION_EXISTING_RECORD_SKIP_SILENT;

    $imageAggregator = new ImageAggregator($imageWriter);
    $imageAggregator->groupByColumn = ImportHelper::lettersToNumber('a');
    $imageAggregator->imagesColumns = ImportHelper::convertColumnIndexes(ImportHelper::getLettersRange('b-ab'));
    /*    $imageAggregator->replace = array(
      'http://openfish.ru/wa-data/public/shop/products/' => ''
    );*/

    $imageAggregator->collectItemBufferSize = 100;

    $csvReader = new ImportCsvReader($this->logger, $imageAggregator);
    $csvReader->csvDelimiter = ',';
    $csvReader->headerRowIndex = 1;
    $csvReader->skipTopRowAmount = 1;
    $csvReader->start();
    $csvReader->processFiles(ImportHelper::getFiles('f/import_image'));
    $csvReader->finish();
  }

  private function updateProducts()
  {
    $sql = array(
      'UPDATE `{{product}}` SET visible=1 WHERE 1',
      'UPDATE `{{product}}` SET visible=0 WHERE id NOT IN (SELECT parent FROM `{{product_img}}` WHERE 1)',
    );

    foreach($sql as $query)
    {
      $command = new CDbCommand(Yii::app()->db, $query);
      $command->execute();
    }
  }
}