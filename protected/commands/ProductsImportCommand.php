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
      $productWriter = new ImportProductWriter($this->logger);
      $productWriter->clear = true;
      $productWriter->clearTables = array(
        '{{product}}',
        '{{product_assignment}}',
        '{{product_param}}',
        '{{product_param_variant}}',
        '{{product_tree_assignment}}',
        '{{product_section}}',
        '{{product_type}}',
        '{{product_category}}',
        '{{product_collection}}',
        '{{product_param_name}}',
      );
      $productWriter->uniqueAttribute = 'external_id';
      $productWriter->assignmentTree = array('type_id' => 'section_id', 'collection_id' => 'type_id');
      $productWriter->assignment = array('section_id', 'type_id', 'collection_id', 'category_id');

      $productAggregator = new ProductAggregator($productWriter);

      $productAggregator->assignmentDelimiter = '@';
      $productAggregator->parameterVariantsDelimiter = '@';
      $productAggregator->collectItemBufferSize = null;

      $productAggregator->groupByColumn = ImportHelper::lettersToNumber('f');
      $productAggregator->product = ImportHelper::convertColumnIndexes(array(
        'name' => 'g',
        'articul' => 'i',
        'price' => 'k',
        'url' => 'w',
        'notice' => 'q',
        'content' => 'r',
        'visible' => 't',
        'dump' => 'l',
        'external_id' => 'p',
        'model' => 'f'
      ));
      $productAggregator->basketParameter = array(ImportHelper::lettersToNumber('h'));
      $productAggregator->assignment = ImportHelper::convertColumnIndexes(array(
        'section_id' => 'b',
        'type_id' => 'c',
        'collection_id' => 'd',
        'category_id' => 'e',
      ));
      $productAggregator->parameter = ImportHelper::convertColumnIndexes(ImportHelper::getLettersRange('z-en'));
      $productAggregator->parameterCommon = ImportHelper::convertColumnIndexes(ImportHelper::getLettersRange('z-ac'));

      $csvReader = new ImportCsvReader($this->logger, $productAggregator);
      $csvReader->csvDelimiter = ',';
      $csvReader->headerRowIndex = 2;
      $csvReader->skipTopRowAmount = 2;
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