<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.modules.product.modules.import.components.*');

chdir(Yii::getPathOfAlias('frontend').'/../');

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
    $imageWriter->uniqueAttribute = 'external_id';
    $imageWriter->clear = true;
    $imageWriter->clearTables = array('{{product_img}}');
    $imageWriter->defaultJpegQuality = 95;

    $imageAggregator = new ImageAggregator($imageWriter);
    $imageAggregator->groupByColumn = ImportHelper::lettersToNumber('p');
    $imageAggregator->imagesColumns = ImportHelper::convertColumnIndexes(ImportHelper::getLettersRange('eo-fo'));
    $imageAggregator->replace = array(
      'http://openfish.ru/wa-data/public/shop/products/' => ''
    );

    $imageAggregator->collectItemBufferSize = 10;

    $csvReader = new ImportCsvReader($this->logger, $imageAggregator);
    $csvReader->csvDelimiter = ',';
    $csvReader->headerRowIndex = 2;
    $csvReader->skipTopRowAmount = 2;
    $csvReader->start();
    $csvReader->processFiles(ImportHelper::getFiles('f/prices'));
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