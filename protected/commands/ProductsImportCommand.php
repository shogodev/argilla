<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::import('backend.modules.product.modules.import.components.*');

chdir(Yii::getPathOfAlias('frontend').'/../');

class ProductsImportCommand extends AbstractImportCommand
{
  public function actionIndex()
  {
    try
    {
      $productWriter = new AbstractImportProductWriter($this->logger);
      $productWriter->clear = true;
      $productWriter->dstTables = array(
        '{{product}}',
        '{{product_assignment}}',
        '{{product_param}}',
        '{{product_param_variant}}',
        '{{product_tree_assignment}}',
        '{{product_section}}',
        '{{product_type}}',
        '{{product_category}}',
        '{{product_collection}}',
      );

      $productWriter->assignmentTree = array('type_id' => 'section_id');
      $productWriter->assignment = array('section_id', 'type_id', 'category_id');

      $productAggregator = new ProductAggregator($productWriter);
      $productAggregator->groupByColumn = 'u'; // url
      $productAggregator->product = array(
        'name' => 'a',
        'articul' => 'c',
        'price' => 'e',
        'url' => 'u',
        'notice' => 'k',
        'content' => 'l',
        'visible' => '',
        'dump' => '',
      );
      $productAggregator->assignment = array(
        'section_id' => 'o',
        'type_id' => 'ad',
        'category_id' => 'x',
        //'collection_id' => 'ad'
      );

      $productAggregator->parameter = array(
        'ae', //длинна
        'af', //тест
        'fg', //строй
        'ah', //транспортная длина:
        'ao'  //передаточное число:
      );

      $csvReader = new ImportCsvReader($this->logger, $productAggregator);
      $csvReader->csvDelimiter = ';';
      $csvReader->start();
      $csvReader->processFiles(ImportHelper::getFiles('f/prices'));
      $csvReader->finish();

      //$this->reindex();
    }
    catch(Exception $e)
    {
      $this->logger->error($e->getMessage());
    }
  }


  private function reindex()
  {
    $runner = new CConsoleCommandRunner();
    $runner->commands = array(
      'indexer' => array(
        'class' => 'frontend.commands.IndexerCommand',
      ),
    );

    ob_start();
    $runner->run(array('yiic', 'indexer', 'refresh'));
    return ob_get_clean();
  }
}