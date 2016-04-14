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
      $productWriter->clear = false;
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
      $productWriter->uniqueAttribute = 'id';
      $productWriter->assignmentTree = array('type_id' => 'section_id', 'collection_id' => 'type_id');
      $productWriter->assignment = array('section_id', 'type_id', 'collection_id', 'category_id');

      $productAggregator = new ProductAggregator($productWriter);

      $productAggregator->assignmentDelimiter = '@';
      $productAggregator->parameterVariantsDelimiter = '@';
      $productAggregator->collectItemBufferSize = 100;

      $productAggregator->groupByColumn = ImportHelper::lettersToNumber('a');
      $productAggregator->product = ImportHelper::convertColumnIndexes(array(
        'id' => 'a',
        'name' => 'm',
        'name_alternative' => 'm',
        'articul' => 'd',
        'price' => array('o', 'value' => 0),
        'url' => 'm', // по названию
        'notice' => 'r',
        'content' => array('s', 'callback' => function($value, $data) { return nl2br($value);}),
        'visible' => array('q', 'value' => 0),
        'dump' => array('p', 'value' => 0),
        'external_id' => 'a',
        'model' => 'k'
      ));
      $productAggregator->basketParameter = array(ImportHelper::lettersToNumber('n'));
      $productAggregator->assignment = ImportHelper::convertColumnIndexes(array(
        'section_id' => 'g',
        'type_id' => 'h',
        'collection_id' => 'i',
        'category_id' => 'j',
      ));

      $productAggregator->parameter = ImportHelper::convertColumnIndexes(ImportHelper::getLettersRange('t-ff'));
      $productAggregator->parameterCommon = ImportHelper::convertColumnIndexes(ImportHelper::getLettersRange('t-x'));

      $csvReader = new ImportCsvReader($this->logger, $productAggregator);
      $csvReader->csvDelimiter = ',';
      $csvReader->headerRowIndex = 1;
      $csvReader->skipTopRowAmount = 1;
      $csvReader->start();
      $csvReader->processFiles(ImportHelper::getFiles('f/prices'));
      $csvReader->finish();

      $this->reindex();
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